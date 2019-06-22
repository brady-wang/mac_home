<?php
namespace Home\Model\Club;

use Think\Model;

class PromoterPayModel extends Model
{
    // 初始配置
    protected $connection = 'CONF_DBTYPE_CLUB';
    protected $trueTableName = 'promoter_pay';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据指定条件获取代理商充值列表
     * @author Carter
     */
    public function queryClubPromoterPayListByAttr($attr, $field)
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
        if (isset($attr['isSuccess'])) {
            $where['isSuccess'] = $attr['isSuccess'];
        }
        if (isset($attr['createDate'])) {
            $where['createDate'] = $attr['createDate'];
        }

        try {
            $list = $this->field($field)->where($where)->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryClubPromoterPayListByAttr] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;

        return $ret;
    }
}
