<?php

namespace Home\Model\GameDev;

use Think\Model;

class UUserRecordModel extends Model
{
    // 初始配置
    protected $connection = 'GAME_DEV_DB';
    protected $trueTableName = 'u_user_record';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据条件获取用户信息
     * @author liyao
     */
    public function queryDevUserRecordInfoByAttr($attr, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array();
        if (isset($attr['userId'])) {
            $where['userId'] = $attr['userId'];
        }
        if (isset($attr['gameId'])) {
            $where['gameId'] = $attr['gameId'];
        }

        try {
            $list = $this->field($field)->where($where)->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryDevUserRecordInfoByAttr] select failed, ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;

        return $ret;
    }
    
    /**
     * 根据条件获取信息之和
     * @author liyao
     */
    public function queryDevUserRecordSumByAttr($attr, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array();
        if (isset($attr['userId'])) {
            $where['userId'] = $attr['userId'];
        }
        if (isset($attr['gameId'])) {
            $where['gameId'] = $attr['gameId'];
        }

        try {
            $list = $this->field("SUM($field) as s")->where($where)->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryDevUserRecordSumByAttr] select failed, ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;

        return $ret;
    }
}
