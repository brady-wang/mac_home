<?php
namespace Home\Model\DsqpDict;

use Think\Model;

class DictPlaceConfigModel extends Model
{
    // 初始配置
    protected $connection = 'GAME_DICT_DB';
    protected $trueTableName = 'dict_place_config';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取指定游戏信息
     * @author Carter
     */
    public function queryDsqpPlaceConfigByPlaceId($placeId, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array();
        if (is_array($placeId)) {
            $where['placeID'] = array('in', $placeId);
        } else {
            $where['placeID'] = $placeId;
        }

        try{
            $list = $this->field($field)->where($where)->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryDsqpPlaceConfigByPlaceId] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $list;
        return $ret;
    }
}
