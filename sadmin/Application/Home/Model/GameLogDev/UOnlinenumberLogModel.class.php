<?php
namespace Home\Model\GameLogDev;

use Think\Model;

class UOnlinenumberLogModel extends Model
{
    // 初始配置
    protected $connection = 'CONF_DBTYPE_GAME_LOG_DEV';
    protected $trueTableName = 'u_onlinenumber_log';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取列表
     * @author tangjie
     */
    public function getGameOnlineMaxNumber($where )
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try{
            $maxNumber = $this->where($where)->order('onlineNumber DESC')->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[getGameOnlineMaxNumber] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $maxNumber;
        return $ret;
    }

    //获取条件的第一条数据
    public function getOnleDataInfo($where){
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try{
            $firstOne = $this->where($where)->order('id ASC')->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[getOnleDataInfo] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $firstOne;
        return $ret;
    }
}
