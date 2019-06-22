<?php
namespace Home\Model\Club;

use Think\Model;

class PromoterDelModel extends Model
{
    // 初始配置
    protected $connection = 'CONF_DBTYPE_CLUB';
    protected $trueTableName = 'promoter_del';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据指定条件获取代理商的已删人数
     * @author Carter
     */
    public function queryClubPromoterCountByAttr($attr)
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
        if (isset($attr['clubId'])) {
            if (is_array($attr['clubId'])) {
                $where['clubId'] = array('in', $attr['clubId']);
            } else {
                $where['clubId'] = $attr['clubId'];
            }
        }
        if (isset($attr['promoterDate'])) {
            $where['promoterDate'] = $attr['promoterDate'];
        }
        if (isset($attr['createDate'])) {
            $where['createDate'] = $attr['createDate'];
        }

        try{
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
