<?php
namespace Home\Model\GameDev;

use Think\Model;

class SPrivateroomInfoModel extends Model
{
    // 初始配置
    protected $connection = 'GAME_DEV_DB';
    protected $trueTableName = 's_privateroom_info';

    public function __construct()
    {
        parent::__construct();
    }

    public function queryGameRoomExtendInfoByWhere($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try{

            if( is_array($attr['roomid']) ){
                $where['roomId'] = array('in' , $attr['roomid'] );
            }else{
                $where['roomId'] = $attr['roomid'];
            }

            if(empty($where)) {
                $ret['code'] = ERRCODE_PARAM_NULL ;
                $ret['msg'] = '参数错误或缺少参数';
                return $ret;
            }

            $roomInfoList = $this->where($where)->order('id DESC')->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGameRoomExtendInfoByWhere] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $roomInfoList;
        return $ret;
    }
}
