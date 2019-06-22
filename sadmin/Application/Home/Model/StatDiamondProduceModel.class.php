<?php
namespace Home\Model;

use Think\Model;

class StatDiamondProduceModel extends Model
{
    /**
     * 根据条件获取钻石产出数据列表，分页
     * @author Carter
     */
    public function queryStatDiamondProduceListByAttr($attr)
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
            $where['stat_time'][] = array('egt', strtotime($attr['start_date']));
        }
        if ($attr['end_date']) {
            $where['stat_time'][] = array('elt', strtotime($attr['end_date']));
        }

        try {
            // 分页获取
            $pageSize = C('PAGE_SIZE');
            $count = $this->where($where)->count();
            $paginate = new \Think\Page($count, $pageSize);
            $pagination = $paginate->show();
            $page = $paginate->getCurPage();

            $field = 'diamond_amount,gift_agent,gift_exclusive,superior_award,';
            $field .= 'agent_purchase,mall_purchase,share_award,invite_award,admin_deliver,stat_time';
            $list = $this->field($field)->where($where)->order('stat_time DESC')->page("{$page},{$pageSize}")->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryStatDiamondProduceListByAttr] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data']['list'] = $list;
        $ret['data']['pagination'] = $pagination;
        return $ret;
    }

    /**
     * 得到钻石产出的图形数据
     * @author liyao
     */
    public function queryStatDiamondProduceChartData($attr, $chartMap, $stime, $etime)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $where = array();
        if ($attr['game_id']) {
            $where['game_id'] = $attr['game_id'];
        }
        if ($attr['start_time']) {
            $where['stat_time'][] = array('egt', $attr['start_time']);
        }
        if ($attr['end_time']) {
            $where['stat_time'][] = array('elt', $attr['end_time']);
        }
        if ($attr['stat_time']) {
            $where['stat_time'][] = array('eq', $attr['stat_time']);
        }

        try {
            if ($stime && $etime) {
                $list = $this->where($where)->order('stat_time DESC')->select();
                $list = array_reverse($list);
            } else if ($stime) {
                $list = $this->where($where)->order('stat_time ASC')->limit(7)->select();
            } else {
                $list = $this->where($where)->order('stat_time DESC')->limit(7)->select();
                $list = array_reverse($list);
            }
            $chartData = array();
            foreach ($chartMap as $k => $v) {
                $da = array();
                $xVal = array();
                for ($i = 0; $i < count($list); $i++) {
                    $xVal[] = date("Y-m-d", $list[$i]['stat_time']);
                    $da[] = $list[$i][$k];
                }
                $chartData[] = array('name'=>$v, 'key'=>$k, 'xAxis'=>$xVal, 'data'=>$da);
            }
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryStatDiamondProduceChartData] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data']['list'] = $chartData;
        return $ret;
    }

    /**
     * 根据条件获取钻石产出数据列表，不分页
     * @author Carter
     */
    public function queryStatDiamondProduceAllList($attr, $field = '*')
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
            $where['stat_time'][] = array('egt', $attr['start_time']);
        }
        if ($attr['end_time']) {
            $where['stat_time'][] = array('elt', $attr['end_time']);
        }
        if ($attr['stat_time']) {
            $where['stat_time'][] = array('eq', $attr['stat_time']);
        }

        try {
            $list = $this->field($field)->where($where)->order("stat_time DESC")->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryStatDiamondProduceAllList] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $list;
        return $ret;
    }

    /**
     * 获取钻石产出统计表指定游戏最后统计时间
     * @author Carter
     */
    public function queryStatDiamondProduceLastTime($gameId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try {
            $info = $this->field('id,stat_time')->where(array('game_id' => $gameId))->order('stat_time DESC')->find();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryStatDiamondProduceLastTime] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $info;

        return $ret;
    }

    /**
     * 按日期插入指定游戏的钻石产出统计记录
     * @author Carter
     */
    public function insertDiamondProduceStatByDate($gameId, $statTime, $statData)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $insertData = array(
            'game_id' => $gameId,
            'diamond_amount' => array_sum($statData),
            'gift_agent' => $statData['giftAgent'],
            'gift_exclusive' => $statData['giftExclusive'],
            'superior_award' => $statData['superiorAward'],
            'agent_purchase' => $statData['agentPurchase'],
            'mall_purchase' => $statData['mallPurchase'],
            'share_award' => $statData['shareAward'],
            'invite_award' => $statData['inviteAward'],
            'admin_deliver' => $statData['adminDeliver'],
            'stat_time' => $statTime,
        );

        try {
            $id = $this->add($insertData);
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertDiamondProduceStatByDate] insert failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $id;

        return $ret;
    }

    /**
     * 根据id更新钻石产出日志记录
     * @author Carter
     */
    public function updateDiamondProduceStatById($id, $statData)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        if (empty($id)) {
            $ret['code'] = ERRCODE_PARAM_NULL;
            $ret['msg'] = 'id can not be null';
            return $ret;
        }

        $updateData = array(
            'diamond_amount' => array_sum($statData),
            'gift_agent' => $statData['giftAgent'],
            'gift_exclusive' => $statData['giftExclusive'],
            'superior_award' => $statData['superiorAward'],
            'agent_purchase' => $statData['agentPurchase'],
            'mall_purchase' => $statData['mallPurchase'],
            'share_award' => $statData['shareAward'],
            'invite_award' => $statData['inviteAward'],
            'admin_deliver' => $statData['adminDeliver'],
        );

        try {
            $this->where(array('id' => $id))->save($updateData);
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateDiamondProduceStatById] insert failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        return $ret;
    }

    /**
     * 根据id删除钻石产出统计表记录
     * @author Carter
     */
    public function deleteDiamondProduceStatById($id)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        try {
            $this->where(array('id' => $id))->delete();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[deleteDiamondProduceStatById] delete failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        return $ret;
    }
}
