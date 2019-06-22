<?php

namespace Home\Model\Club;

use Think\Model;

class ClubModel extends Model
{
    // 初始配置
    protected $connection = 'CONF_DBTYPE_CLUB';
    protected $trueTableName = 'club';

    const CLUB_STATUS_CREATE = 0; // 新注册
    const CLUB_STATUS_NORMAL = 1; // 正常
    const CLUB_STATUS_FORBID = 2; // 封停

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据指定条件获取亲友圈数
     * @author Carter
     */
    public function queryClubCountByAttr($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array();
        if (isset($attr['gameId'])) {
            $where['gameId'] = $attr['gameId'];
        }
        if (isset($attr['clubStatus'])) {
            $where['clubStatus'] = $attr['clubStatus'];
        }
        if (isset($attr['createDateElt'])) {
            $where['createTime'] = array('elt', $attr['createDateElt']." 23:59:59");
        }

        try {
            $count = $this->where($where)->count();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryClubPromoterCountByAttr] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $count;
        return $ret;
    }
}
