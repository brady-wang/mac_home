<?php
namespace Home\Model\ClubLog;

use Think\Model;

class LogPromoterSellModel extends Model
{
    // 初始配置
    protected $connection = 'CONF_DBTYPE_CLUB_LOG';
    protected $trueTableName = 'log_promoter_sell';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据条件获取卖钻撤回流水
     * @author Carter
     */
    public function queryPromoterSellRevokeLogByAttr($attr, $field)
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
        if (isset($attr['createDate'])) {
            $where['createDate'] = $attr['createDate'];
        }

        try {
            $list = $this->field($field)->where($where)->select();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryPromoterSellRevokeLogByAttr] select failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;

        return $ret;
    }
}
