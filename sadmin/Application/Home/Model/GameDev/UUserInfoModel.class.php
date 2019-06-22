<?php

namespace Home\Model\GameDev;

use Think\Model;

class UUserInfoModel extends Model
{
    // 初始配置
    protected $connection = 'GAME_DEV_DB';
    protected $trueTableName = 'u_user_info';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据用户ID获取用户列表
     * @author tangjie
     */
    public function queryDevUserInfoByUid($uid, $field = "*")
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try {
            $info = $this->field($field)->where(array('userId' => $uid))->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryDevUserInfoByUid] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $info;
        return $ret;
    }

    /**
     * 根据条件获取用户列表
     * @author Carter
     */
    public function queryDevUserListByAttr($attr, $field = "*")
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
        if (isset($attr['type'])) {
            $where['type'] = $attr['type'];
        }
        if (isset($attr['startCreateTime'])) {
            $where['createTime'][] = array('egt', $attr['startCreateTime']);
        }
        if (isset($attr['endCreateTime'])) {
            $where['createTime'][] = array('elt', $attr['endCreateTime']);
        }

        try {
            $list = $this->field($field)->where($where)->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryDevUserListByAttr] select failed, ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;

        return $ret;
    }

    /**
     * 获取用户数量
     * @author liyao
     */
    public function queryUserCountByWhere($where) {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try {
            $count = $this->where($where)->count();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryUserCountByWhere] select failed, ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $count;

        return $ret;
    }
}
