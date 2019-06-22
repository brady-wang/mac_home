<?php
namespace Home\Model;

use Think\Model;

class StatUserTotalModel extends Model
{
    /**
     * 取得累计数据统计数据
     * @author liyao
     */
    public function queryStatUserTotalData($where, $pageParam, $field = "*")
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        // 分页获取
        try {
            $pageSize = C('PAGE_SIZE');
            $count = $this->where($where)->count();
            $paginate = new \Think\Page($count, $pageSize);
            foreach ($pageParam as $k => $v) {
                $paginate->parameter[$k] = $v;
            }
            $pagination = $paginate->show();

            $page = $paginate->getCurPage();
            $list = $this->field($field)->where($where)->order('data_time DESC')->page("{$page},{$pageSize}")->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryStatUserTotalData] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data']['list'] = $list;
        $ret['data']['pagination'] = $pagination;
        return $ret;
    }
    
    /**
     * 取得累计统计的全部数据
     * @author liyao
     */
    public function queryStatUserTotalAllData($where, $field = "*")
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        try {
            $list = $this->field($field)->where($where)->order('data_time DESC')->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryStatUserTotalAllData] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data']['list'] = $list;
        return $ret;
    }
    
    /**
     * 得到累计统计的图形数据
     * @author liyao
     */
    public function queryStatUserTotalChartData($where, $chartMap, $stime, $etime)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        try {
            if ($stime && $etime) {
                $list = $this->where($where)->order('data_time DESC')->select();
                $list = array_reverse($list);
            } else if ($stime) {
                $list = $this->where($where)->order('data_time ASC')->limit(7)->select();
            } else {
                $list = $this->where($where)->order('data_time DESC')->limit(7)->select();
                $list = array_reverse($list);
            }
            $chartData = array();
            foreach ($chartMap as $k => $v) {
                $da = array();
                $xVal = array();
                for ($i = 0; $i < count($list); $i++) {
                    $xVal[] = date("Y-m-d", $list[$i]['data_time']);
                    $da[] = $list[$i][$k];
                }
                $chartData[] = array('name'=>$v, 'key'=>$k, 'xAxis'=>$xVal, 'data'=>$da);
            }
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryStatUserTotalChartData] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data']['list'] = $chartData;
        return $ret;
    }
    
    /**
     * 取得最新时间
     * @author liyao
     */
    public function queryStatUserMaxDate($gameid) {
        $date = 0;
        try {
            $date = $this->where(array("game_id" => $gameid))->max('data_time');
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryStatUserMaxDate] select failed: ".$e->getMessage());
        }
        return $date;
    }
                  
    /**
     * 取得初始时间
     * @author liyao
     */
    public function queryStatUserMinDate($gameid) {
        $date = 0;
        try {
            $date = $this->where(array("game_id" => $gameid))->min('data_time');
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryStatUserMinDate] select failed: ".$e->getMessage());
        }
        return $date;
    }    

    /**
     * 插入累计数据统计数据
     * @author liyao
     */
    public function insertStatTotal($data, $gameid, $curDate)
    {
        try {
            $id = $this->where(array("game_id" => $gameid, "data_time" => $curDate))->getField('id');
            if ($id) {
                $data["update_time"] = time();
                $this->where('id=' . $id)->save($data);
            } else {
                $data["create_time"] = time();
                $this->data($data)->add();
            }
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertStatTotalRemain] insert failed ".$e->getMessage());
        }
    }
}
