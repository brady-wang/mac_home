<?php
namespace Home\Model\GameLogDev;
use Think\Model;

class UPyjUserRecordModel extends Model
{
    // 初始配置
    protected $connection = 'CONF_DBTYPE_GAME_LOG_DEV';
    protected $trueTableName = 'u_pyj_user_record';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据房间id获取牌局流水
     * @author Carter
     */
    public function queryUPyjUserRecordGroupByTime($dateTime, $offset, $limit)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        // 'between xxx and yyy' 在效率上与 '>= xxx and <= yyy' 无差
        $sTime = date('Y-m-d 00:00:00', $dateTime);
        $eTime = date('Y-m-d 23:59:59', $dateTime);
        $where = array(
            'gameStartTime' => array('between', array($sTime, $eTime)),
        );

        $field = 'roomId,gameStartTime,date,userinfo1,userinfo2,userinfo3,userinfo4';

        try {
            $list = $this->field($field)->where($where)->group('roomID,gameStartTime')->limit($offset, $limit)->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryUPyjUserRecordGroupByTime] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $list;

        return $ret;
    }
}
