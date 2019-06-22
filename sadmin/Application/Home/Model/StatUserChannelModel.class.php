<?php
namespace Home\Model;

use Think\Model;

class StatUserChannelModel extends Model
{
    /**
     * 取得渠道统计数据
     * @author liyao
     */
    public function queryStatUserChannelData($where, $pageParam, $field = "*")
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        // 分页获取
        try {
            $pageSize = C('PAGE_SIZE');
            $data = $this->where($where)->group('data_time')->order('data_time DESC')->select();
            $count = count($data);
            $paginate = new \Think\Page($count, $pageSize);
            foreach ($pageParam as $k => $v) {
                $paginate->parameter[$k] = $v;
            }
            $pagination = $paginate->show();

            $page = $paginate->getCurPage();
            if ($where['type'] == 1) {
                $field = "data_time, code, login_number as number";
            } else {
                $field = "data_time, code, register_number as number";
            }

            $wt = $this->field("data_time")->where($where)->group('data_time')->order('data_time DESC')->select();
            $separr = array_chunk($wt, $pageSize);
            $pagearr = $separr[($page - 1)];
            $where['data_time'] = array();
            $where['data_time'][] = array('egt', $pagearr[count($pagearr) - 1]['data_time']);
            $where['data_time'][] = array('elt', $pagearr[0]['data_time']);
            $list = $this->field($field)->where($where)->order('data_time DESC, code ASC')->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryStatUserChannelData] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $deldata = array();
        foreach ($list as $v) {
            $deldata[$v['data_time']][] = array('code' => $v['code'], 'value' => $v['number']);
        }
        $deldata2 = array();
        foreach ($deldata as $k => $v) {
            $deldata2[] = array("data_time" => $k, "channel" => $v);
        }

        $ret['data']['list'] = $deldata2;
        $ret['data']['pagination'] = $pagination;
        return $ret;
    }
    
    /**
     * 得到用户渠道的图形数据
     * @author liyao
     */
    public function queryStatUserChannelChartData($where, $chartMap, $stime, $etime)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        try {
            if ($where['type'] == 1) {
                $field = "data_time, code, login_number as number";
            } else {
                $field = "data_time, code, register_number as number";
            }
            if ($stime && $etime) {
                $list = $this->field($field)->where($where)->order('data_time DESC, code DESC')->select();
                $list = array_reverse($list);
            } else if ($stime) {
                $data = $this->field("data_time")->where($where)->group('data_time')->order('data_time ASC')->limit(7)->select();
                if (count($data) > 0) {
                    $idx = count($data) - 1;
                    $where["data_time"][] = array('elt', $data[$idx]["data_time"]);
                }
                $list = $this->field($field)->where($where)->order('data_time ASC, code ASC')->select();
            } else if ($etime) {
                $data = $this->field("data_time")->where($where)->group('data_time')->order('data_time DESC')->limit(7)->select();
                if (count($data) > 0) {
                    $idx = count($data) - 1;
                    $where["data_time"][] = array('egt', $data[$idx]["data_time"]);
                }
                $list = $this->field($field)->where($where)->order('data_time ASC, code ASC')->select();
            } else {
                $data = $this->field("data_time")->where($where)->group('data_time')->order('data_time DESC')->limit(7)->select();
                if (count($data) > 0) {
                    $idx = count($data) - 1;
                    $where["data_time"][] = array('egt', $data[$idx]["data_time"]);
                    $where["data_time"][] = array('elt', $data[0]["data_time"]);
                }
                $list = $this->field($field)->where($where)->order('data_time ASC, code ASC')->select();
            }
            $deldata = array();
            foreach ($list as $v) {
                if (!isset($deldata[$v['data_time']])) {
                    $deldata[$v['data_time']] = array("data_time" => $v["data_time"]);
                }
                $deldata[$v['data_time']][$v['code']] = $v['number'];
            }
            $list = $deldata;
            $chartData = array();
            foreach ($chartMap as $k => $v) {
                $da = array();
                $xVal = array();
                foreach ($list as $kk => $vv) {
                    $xVal[] = date("Y-m-d", $kk);
                    if (isset($vv[$k])) {
                        $da[] = $vv[$k];
                    }
                }
                $chartData[] = array('name'=>$v, 'key'=>$k, 'xAxis'=>$xVal, 'data'=>$da);
            }
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryStatUserChannelChartData] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data']['list'] = $chartData;
        return $ret;
    }
    
    /**
     * 取得渠道统计的全部数据
     * @author liyao
     */
    public function queryStatUserChannelAllData($where, $field = "*")
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        try {
            if ($where['type'] == 1) {
                $field = "data_time, code, login_number as number";
            } else {
                $field = "data_time, code, register_number as number";
            }

            $list = $this->field($field)->where($where)->order('data_time DESC, code ASC')->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryStatUserChannelAllData] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $deldata = array();
        foreach ($list as $v) {
            $deldata[$v['data_time']][] = array('code' => $v['code'], 'value' => $v['number']);
        }
        $deldata2 = array();
        foreach ($deldata as $k => $v) {
            $deldata2[] = array("data_time" => $k, "channel" => $v);
        }

        $ret['data']['list'] = $deldata2;
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
     * 插入渠道统计数据
     * @author liyao
     */
    public function insertStatUserChannel($data, $gameid, $curDate, $code)
    {
        try {
            $id = $this->where(array("game_id" => $gameid, "data_time" => $curDate, 'code' => $code))->getField('id');
            if ($id) {
                $data["update_time"] = time();
                $this->where('id=' . $id)->save($data);
            } else {
                $data["create_time"] = time();
                $this->data($data)->add();
            }
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertStatUserChannel] insert failed ".$e->getMessage());
        }
    }
}
