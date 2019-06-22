<?php
namespace Home\Model;

use Think\Model;

class StatUserDailyUsercacheModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据条件获取每日简报用户统计缓存列表
     * @author Carter
     */
    public function queryStatDailyUserCacheByAttr($gameId, $attr, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $where = array('game_id' => $gameId);
        if (isset($attr['statTime'])) {
            $where['stat_time'] = $attr['statTime'];
        }
        if (isset($attr['gameFlag'])) {
            $where['game_flag'] = $attr['gameFlag'];
        }

        try {
            $list = $this->field($field)->where($where)->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryStatDailyUserCacheByAttr] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;

        return $ret;
    }

    /**
     * 插入每日简报统计用户缓存数据
     * @author Carter
     */
    public function insertStatUserDailyUserCache($gameId, $statTime, $uidArr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $insertData = array();
        foreach ($uidArr as $v) {
            $insertData[] = array(
                'game_id' => $gameId,
                'stat_time' => $statTime,
                'uid' => $v,
                'game_flag' => 0,
            );
        }

        try {
            $this->addAll($insertData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertStatUserDailyUserCache] insert failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_ADD_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        return $ret;
    }

    /**
     * 将指定记录的参局字段更新为已开局
     * @author Carter
     */
    public function updateStatDailyCacheGameFlag($idArr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $updateData = array('game_flag' => 1);
        try {
            $this->where(array('id' => array('in', $idArr)))->save($updateData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateStatDailyCacheGameFlag] update failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        return $ret;
    }

    /**
     * 删除指定时间之前的记录
     * @author Carter
     */
    public function deleteStatDailyUserCacheByTime($timestamp)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        // 删除角色
        try {
            $delNum = $this->where(array('stat_time' => array('lt', $timestamp)))->delete();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[deleteStatDailyUserCacheByTime] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_DELETE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $delNum;

        return $ret;
    }
}
