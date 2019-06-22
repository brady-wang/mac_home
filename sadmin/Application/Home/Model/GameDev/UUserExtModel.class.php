<?php

namespace Home\Model\GameDev;

use Think\Model;

class UUserExtModel extends Model
{
    // 初始配置
    protected $connection = 'GAME_DEV_DB';
    protected $trueTableName = 'u_user_ext';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据条件获取用户扩展信息列表
     * @author Carter
     */
    public function queryDevUserExtListByAttr($attr, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array();
        if (isset($attr['userId'])) {
            if (is_array($attr['userId'])) {
                $where['userId'] = array('IN', $attr['userId']);
            } else {
                $where['userId'] = $attr['userId'];
            }
        }

        try {
            $list = $this->field($field)->where($where)->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryDevUserExtListByAttr] select failed, ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;

        return $ret;
    }
    
    /**
     * 根据条件获取用户扩展信息列表
     * @author Carter
     */
    public function queryDevUserExtInfoByAttr($attr, $field)
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

        try {
            $list = $this->field($field)->where($where)->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryDevUserExtInfoByAttr] select failed, ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;

        return $ret;
    }
}
