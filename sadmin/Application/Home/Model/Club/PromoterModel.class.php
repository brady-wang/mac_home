<?php
namespace Home\Model\Club;

use Think\Model;

class PromoterModel extends Model
{
    // 初始配置
    protected $connection = 'CONF_DBTYPE_CLUB';
    protected $trueTableName = 'promoter';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据条件获取代理商列表
     * @author Carter
     */
    public function queryClubromoterListByAttr($attr, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array();
        if (isset($attr['clubId'])) {
            if (is_array($attr['clubId'])) {
                $where['clubId'] = array('in', $attr['clubId']);
            } else {
                $where['clubId'] = $attr['clubId'];
            }
        }

        try {
            $list = $this->field($field)->where($where)->select();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryClubromoterListByAttr] select failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;

        return $ret;
    }

    /**
     * 获取首条代理信息
     * @author Carter
     */
    public function queryClubPromoterFirstRow($gameId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try{
            $info = $this->field('createDate')->where(array('gameId' => $gameId))->order('createTime ASC')->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryClubPromoterFirstRow] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $info;
        return $ret;
    }

    /**
     * 根据指定条件获取代理商人数
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
        if (isset($attr['promoterId'])) {
            if (is_array($attr['promoterId'])) {
                $where['id'] = array('in', $attr['promoterId']);
            } else {
                $where['id'] = $attr['promoterId'];
            }
        }
        if (isset($attr['gameId'])) {
            $where['gameId'] = $attr['gameId'];
        }
        if (isset($attr['totalPayGt'])) {
            $where['totalPay'] = array('gt', $attr['totalPayGt']);
        }
        if (isset($attr['createDate'])) {
            $where['createDate'] = $attr['createDate'];
        }
        if (isset($attr['createDateElt'])) {
            $where['createDate'] = array('elt', $attr['createDateElt']);
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
