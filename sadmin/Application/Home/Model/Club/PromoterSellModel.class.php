<?php
namespace Home\Model\Club;

use Think\Model;

class PromoterSellModel extends Model
{
    // 初始配置
    protected $connection = 'CONF_DBTYPE_CLUB';
    protected $trueTableName = 'promoter_sell';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取指定日期有卖钻与撤回的代理商
     * @author Carter
     */
    public function queryClubPromoterSellAndRevoke($gameId, $date, $revokeId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array(
            'gameId' => $gameId,
            'isSuccess' => 1,
            'createDate' => $date,
        );
        if (!empty($revokeId)) {
            $where = array(
                '_complex' => $where,
                'id' => array('in', $revokeId),
                '_logic' => 'or',
            );
        }

        try {
            $list = $this->field('promoterId')->where($where)->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryClubPromoterSellAndRevoke] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = array_column($list, 'promoterId');

        return $ret;
    }
}
