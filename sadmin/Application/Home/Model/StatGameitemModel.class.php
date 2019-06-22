<?php
namespace Home\Model;

use Think\Model;

class StatGameitemModel extends Model
{
    /**
     * 根据条件获取对局统计数据列表，分页
     * @author Carter
     */
    public function queryStatGameItemListByAttr($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array();
        if ($attr['game_id']) {
            $where['game_id'] = $attr['game_id'];
        }
        if ($attr['start_date']) {
            $where['data_time'][] = array('egt', strtotime($attr['start_date']));
        }
        if ($attr['end_date']) {
            $where['data_time'][] = array('elt', strtotime($attr['end_date']));
        }

        try {
            // 分页获取
            $pageSize = C('PAGE_SIZE');
            $count = $this->where($where)->count();
            $paginate = new \Think\Page($count, $pageSize);
            $pagination = $paginate->show();
            $page = $paginate->getCurPage();

            $field = 'create_count,create_access_count,total_average_time,';
            $field .= 'item_average_time,win_average_integral,average_integral,data_time';
            $list = $this->field($field)->where($where)->order('data_time DESC')->page("{$page},{$pageSize}")->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryStatGameItemListByAttr] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data']['list'] = $list;
        $ret['data']['pagination'] = $pagination;
        return $ret;
    }

    /**
     * 得到对局统计的图形数据
     * @author Carter
     */
    public function queryGameRoundChartData($attr, $chartMap)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        // 默认查询最近七天数据
        if (empty($attr['start_date']) && empty($attr['end_date'])) {
            $eTime = strtotime('midnight yesterday');
            $sTime = strtotime('-6 days', $eTime);
        }
        // 同时选择了开始与结束日期
        else if (!empty($attr['start_date']) && !empty($attr['end_date'])) {
            $sTime = strtotime($attr['start_date']);
            $eTime = strtotime($attr['end_date']);
        }
        // 只选了开始日期，那就查询从开始日期往后推七天
        else if (!empty($attr['start_date'])) {
            $sTime = strtotime($attr['start_date']);
            $eTime = strtotime('+6 days', $sTime);
        }
        // 只选了结束日期，那就查询从结束日期往前推七天
        else if (!empty($attr['end_date'])) {
            $eTime = strtotime($attr['end_date']);
            $sTime = strtotime('-6 days', $eTime);
        }

        $where = array(
            'game_id' => $attr['game_id'],
            'data_time' => array(
                array('egt', $sTime),
                array('elt', $eTime),
            ),
        );

        try {
            $field = 'create_count,create_access_count,total_average_time,';
            $field .= 'item_average_time,win_average_integral,average_integral,data_time';
            $list = $this->field($field)->where($where)->order('data_time ASC')->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGameRoundChartData] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $xAxis = array_column($list, 'data_time');
        array_walk($xAxis, function(&$item) {
            $item = date('Y-m-d', $item);
        });

        $chartData = array();
        foreach ($chartMap as $key => $name) {
            $chartData[] = array(
                'name' => $name,
                'key' => $key,
                'xAxis' => $xAxis,
                'data' => array_column($list, $key),
            );
        }

        $ret['data'] = $chartData;
        return $ret;
    }

    /**
     * 根据条件获取对局统计数据列表，不分页
     * @author Carter
     */
    public function queryStatGameItemAllList($attr, $field = '*')
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array();
        if ($attr['game_id']) {
            $where['game_id'] = $attr['game_id'];
        }
        if ($attr['start_time']) {
            $where['data_time'][] = array('egt', $attr['start_time']);
        }
        if ($attr['end_time']) {
            $where['data_time'][] = array('elt', $attr['end_time']);
        }

        try {
            $list = $this->field($field)->where($where)->order("data_time DESC")->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryStatGameItemAllList] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $list;
        return $ret;
    }

    /**
     * 插入对局统计数据，新数据则插入，已存在则更新
     * @author Carter
     */
    public function insertStatGameItem($gameId, $dataTime, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        // 查询数据是否已存在
        try {
            $where = array(
                'game_id' => $gameId,
                'data_time' => $dataTime,
            );
            $info = $this->field('id')->where($where)->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertStatGameItem] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        // 已存在更新
        if ($info) {
            $id = $info['id'];
            $updateData = array(
                'create_count' => $attr['createCount'],
                'create_access_count' => $attr['createAccessCount'],
                'total_average_time' => $attr['totalAverageTime'],
                'item_average_time' => $attr['itemAverageTime'],
                'win_average_integral' => $attr['winAverageIntegral'],
                'average_integral' => $attr['averageIntegral'],
            );
            try {
                $this->where(array('id' => $id))->save($updateData);
            } catch (\Exception $e) {
                set_exception(__FILE__, __LINE__, "[insertStatGameItem] update failed: ".$e->getMessage());
                $ret['code'] = ERRCODE_DB_UPDATE_ERR;
                $ret['msg'] = $e->getMessage();
                return $ret;
            }
            $ret['data']['update'] = $updateData;
        }
        // 不存在插入
        else {
            $insertData = array(
                'game_id' => $gameId,
                'create_count' => $attr['createCount'],
                'create_access_count' => $attr['createAccessCount'],
                'total_average_time' => $attr['totalAverageTime'],
                'item_average_time' => $attr['itemAverageTime'],
                'win_average_integral' => $attr['winAverageIntegral'],
                'average_integral' => $attr['averageIntegral'],
                'data_time' => $dataTime,
            );
            try {
                $id = $this->add($insertData);
            } catch (\Exception $e) {
                set_exception(__FILE__, __LINE__, "[insertStatGameItem] update failed: ".$e->getMessage());
                $ret['code'] = ERRCODE_DB_UPDATE_ERR;
                $ret['msg'] = $e->getMessage();
                return $ret;
            }
            $ret['data']['insert'] = $insertData;
        }

        $ret['data']['id'] = $id;

        return $ret;
    }
}
