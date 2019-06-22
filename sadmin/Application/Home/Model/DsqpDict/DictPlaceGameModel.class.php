<?php
namespace Home\Model\DsqpDict;

use Think\Model;

class DictPlaceGameModel extends Model
{
    // 初始配置
    protected $connection = 'GAME_DICT_DB';
    protected $trueTableName = 'dict_place_game';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取指定游戏信息
     * @author Carter
     */
    public function queryDsqpPlaceGameByPlaceId($placeId, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array();
        if (is_array($placeId)) {
            $where['placeID'] = array('in', $placeId);
        } else {
            $where['placeID'] = $placeId;
        }

        try{
            $list = $this->field($field)->where($where)->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryDsqpPlaceGameByPlaceId] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $list;
        return $ret;
    }

    /**
     * 根据地区id和玩法id获取游戏信息
     * @author Carter
     */
    public function queryDsqpPlaceGameByPlacePlayId($placeId, $playId, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array(
            'placeID' => $placeId,
            'gameId' => $playId,
        );

        try{
            $info = $this->field($field)->where($where)->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryDsqpPlaceGameByPlacePlayId] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        if (empty($info)) {
            $ret['code'] = ERRCODE_DB_DATA_EMPTY;
            $ret['msg'] = "游戏配置信息不存在，地区{$placeId}，玩法{$playId}";
            return $ret;
        }

        $ret['data'] = $info;
        return $ret;
    }

    /**
     * 根据地区id和玩法id更新游戏信息
     * @author Carter
     */
    public function updateDsqpPlaceGameByPlacePlayId($placeId, $playId, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array(
            'placeID' => $placeId,
            'gameId' => $playId,
        );

        try {
            $field = 'initScore,expiredTime';
            $info = $this->field($field)->where($where)->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateDsqpPlaceGameByPlacePlayId] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if (empty($info)) {
            $ret['code'] = ERRCODE_DB_DATA_EMPTY;
            $ret['msg'] = "游戏配置信息不存在，地区{$placeId}，玩法{$playId}";
            return $ret;
        }

        $updateData = array();
        // 初始分数
        if (isset($attr['init_score']) && $attr['init_score'] != $info['initScore']) {
            $updateData['initScore'] = $attr['init_score'];
        }
        // 解散时间
        if (isset($attr['expired_time']) && $attr['expired_time'] != $info['expiredTime']) {
            $updateData['expiredTime'] = $attr['expired_time'];
        }
        if (!empty($updateData)) {
            try {
                $this->where($where)->save($updateData);
            } catch(\Exception $e) {
                set_exception(__FILE__, __LINE__, "[updateDsqpPlaceGameByPlacePlayId] update failed. ".$e->getMessage());
                $ret['code'] = ERRCODE_DB_UPDATE_ERR;
                $ret['msg'] = $e->getMessage();
                return $ret;
            }
        }

        $ret['data'] = $updateData;
        return $ret;
    }
}
