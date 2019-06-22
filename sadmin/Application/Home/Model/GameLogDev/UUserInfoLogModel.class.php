<?php

namespace Home\Model\GameLogDev;

use Think\Model;

class UUserInfoLogModel extends Model
{
    // 初始配置
    protected $connection = 'CONF_DBTYPE_GAME_LOG_DEV';
    protected $trueTableName = 'u_user_info_log';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据条件获取用户列表
     * @author tangjie
     */
    public function queryDevLogUserInfoList($attr, $field = "*")
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array();
        if (isset($attr['sLoginTime'])) {
            $where['loginTime'][] = array('egt', $attr['sLoginTime']);
        }
        if (isset($attr['eLoginTime'])) {
            $where['loginTime'][] = array('elt', $attr['eLoginTime']);
        }

        try {
            $list = $this->field($field)->where($where)->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryDevLogUserInfoList] select failed, ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;

        return $ret;
    }
    
    /**
     * 获取用户最新登录信息
     * @author liyao
     */
    public function queryDevLogUserInfoRecent($attr, $field = "*")
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array();
        $where["userId"] = $attr["userId"];

        try {
            $list = $this->field($field)->where($where)->order("loginTime desc")->limit(1)->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryDevLogUserInfoRecent] select failed, ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;

        return $ret;
    }
}
