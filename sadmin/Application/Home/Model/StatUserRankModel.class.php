<?php
namespace Home\Model;

use Think\Model;

class StatUserRankModel extends Model
{
    /**
     * 根据条件获取用户排行数据列表，不分页
     * @author Carter
     */
    public function queryStatUserRankAllList($attr, $field = '*')
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
        if ($attr['stat_time']) {
            $where['data_time'][] = array('eq', $attr['stat_time']);
        }

        try {
            $list = $this->field($field)->where($where)->order("data_time DESC")->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryStatUserRankAllList] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $list;
        return $ret;
    }

    /**
     * 获取用户排行统计表指定游戏最后统计时间
     * @author Carter
     */
    public function queryStatUserRankLastTime($gameId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try {
            $info = $this->field('id,data_time')->where(array('game_id' => $gameId))->order('data_time DESC')->find();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryStatUserRankLastTime] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $info;

        return $ret;
    }

    /**
     * 取得按日期区间统计数据
     */
    public function queryStatUserRankInterval($where, $sDate, $eDate)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $limitUser = !empty(C('RANK_SIZE')) ? C('RANK_SIZE') : 200;
        $list = array("data_time"=>array(), "data"=>array());
        try {
            $map = array();
            $propData = $this->field("prop_user_id, prop_user_name as nm, SUM(prop_nums) as s")->where($where)->where('prop_user_id <> 0')->group('prop_user_id')->order('s desc')->limit($limitUser)->select();
            $winData = $this->field("win_user_id, win_user_name as nm, SUM(win_nums) as s")->where($where)->where('win_user_id <> 0')->group('win_user_id')->order('s desc')->limit($limitUser)->select();
            $recordData = $this->field("record_user_id, record_user_name as nm, SUM(record_nums) as s")->where($where)->where('record_user_id <> 0')->group('record_user_id')->order('s desc')->limit($limitUser)->select();
            $record4Data = $this->field("record4_user_id, record4_user_name as nm, SUM(record4_nums) as s")->where($where)->where('record4_user_id <> 0')->group('record4_user_id')->order('s desc')->limit($limitUser)->select();
            $inviteData = $this->field("invite_user_id, invite_user_name as nm, SUM(invite_nums) as s")->where($where)->where('invite_user_id <> 0')->group('invite_user_id')->order('s desc')->limit($limitUser)->select();
            $list["data_time"][] = $sDate." ~ ".$eDate;//date("Y-m-d", $start)." ~ ".date("Y-m-d", $end);
            for ($i = 0; $i < $limitUser; $i++) {
                if (($i + 1) > count($propData) && ($i + 1) > count($winData) && ($i + 1) > count($recordData) && ($i + 1) > count($record4Data) && ($i + 1) > count($inviteData))
                    break;
                $rd = array("prop_user_id"=>0,"prop_user_name"=>"","prop_nums"=>0,"win_user_id"=>0,"win_user_name"=>"","win_nums"=>0,
                    "record_user_id"=>0,"record_user_name"=>"","record_nums"=>0,"record4_user_id"=>0, "record4_user_name"=>"", "record4_nums"=>0,"invite_user_id"=>0,"invite_user_name"=>"","invite_nums"=>0
                    );
                if ($i < count($propData) && $propData[$i]['prop_user_id'] != 0) {
                    $rd["prop_user_id"] = $propData[$i]['prop_user_id'];
                    $rd["prop_user_name"] = $propData[$i]['nm'];
                    $rd["prop_nums"] = $propData[$i]['s'];
                }
                if ($i < count($winData) && $winData[$i]['win_user_id'] != 0) {
                    $rd["win_user_id"] = $winData[$i]['win_user_id'];
                    $rd["win_user_name"] = $winData[$i]['nm'];
                    $rd["win_nums"] = $winData[$i]['s'];
                }
                if ($i < count($recordData) && $recordData[$i]['record_user_id'] != 0) {
                    $rd["record_user_id"] = $recordData[$i]['record_user_id'];
                    $rd["record_user_name"] = $recordData[$i]['nm'];
                    $rd["record_nums"] = $recordData[$i]['s'];
                }
                if ($i < count($record4Data) && $record4Data[$i]['record4_user_id'] != 0) {
                    $rd["record4_user_id"] = $record4Data[$i]['record4_user_id'];
                    $rd["record4_user_name"] = $record4Data[$i]['nm'];
                    $rd["record4_nums"] = $record4Data[$i]['s'];
                }
                if ($i < count($inviteData) && $inviteData[$i]['invite_user_id'] != 0) {
                    $rd["invite_user_id"] = $inviteData[$i]['invite_user_id'];
                    $rd["invite_user_name"] = $inviteData[$i]['nm'];
                    $rd["invite_nums"] = $inviteData[$i]['s'];
                }
                $list["data"][] = $rd;
            }
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryStatUserRankInterval] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data']['list'] = $list;
        return $ret;
    }

    /**
     * 批量插入排行统计数据
     * @author Carter
     */
    public function insertStatUserRankByData($gameId, $statTime, $data)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $curTime = time();

        $insertData = array();
        foreach ($data as $v) {
            $insertData[] = array(
                'game_id' => $gameId,
                'data_time' => $statTime,
                'prop_user_id' => $v['prop_user_id'],
                'prop_user_name' => $v['prop_user_name'],
                'prop_nums' => $v['prop_nums'],
                'win_user_id' => $v['win_user_id'],
                'win_user_name' => $v['win_user_name'],
                'win_nums' => $v['win_nums'],
                'record_user_id' => $v['record_user_id'],
                'record_user_name' => $v['record_user_name'],
                'record_nums' => $v['record_nums'],
                'record4_user_id' => $v['record4_user_id'],
                'record4_user_name' => $v['record4_user_name'],
                'record4_nums' => $v['record4_nums'],
                'invite_user_id' => 0,
                'invite_user_name' => '',
                'invite_nums' => 0,
                'create_time' => $curTime,
                'update_time' => $curTime,
            );
        }

        try {
            $this->addAll($insertData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertStatUserRankByData] insert failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_ADD_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $insertData;
        return $ret;
    }

    /**
     * 根据id删除用户排行统计表记录
     * @author Carter
     */
    public function deleteUserRankStatById($id)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        try {
            $this->where(array('id' => $id))->delete();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[deleteUserRankStatById] delete failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        return $ret;
    }
}
