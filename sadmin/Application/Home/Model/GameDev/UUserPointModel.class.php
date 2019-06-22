<?php

namespace Home\Model\GameDev;

use Think\Model;

class UUserPointModel extends Model
{
    // 初始配置
    protected $connection = 'GAME_DEV_DB';
    protected $trueTableName = 'u_user_point';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据条件获取用户信息
     * @author liyao
     */
    public function queryDevUserPointInfoByAttr($attr, $field)
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
            set_exception(__FILE__, __LINE__, "[queryDevUserPointInfoByAttr] select failed, ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;

        return $ret;
    }
}
