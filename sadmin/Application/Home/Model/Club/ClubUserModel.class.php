<?php

namespace Home\Model\Club;

use Think\Model;

class ClubUserModel extends Model
{

    // 初始配置
    protected $connection = 'CONF_DBTYPE_CLUB';
    protected $trueTableName = 'club_user';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 查询俱乐部用户信息
     * @author liyao
     */
    public function getUserInfoByUserId($userid)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array(
            'club_user.gameUserId' => $userid
        );
        $isProxy = 0;
        try{
            $data = $this->field('promoterId')->where($where)->find();
            if ($data) {
                $isProxy = ($data['promoterId'] == 0)?1:0;
            }
            $data = $this->distinct(true)->field('clubName')->where($where)->join("club ON club_user.clubId = club.id")->select();
            $clubNames = implode(',', array_column($data, 'clubName'));
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[getUserInfoByUserId] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = array('isProxy'=>$isProxy, 'clubNames'=>$clubNames);

        return $ret;
    }

}
