<?php
namespace Home\Model\GameLogDev;
use Think\Model;

class PyjUserRecordModel extends Model
{
    /* 文件命名不符合规范，已经新建了 UPyjUserRecordModel，本Model未来要删掉 */

    // 初始配置
    protected $connection = 'CONF_DBTYPE_GAME_LOG_DEV';
    protected $trueTableName = 'u_pyj_user_record';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取牌局的战绩列表
     * @author tangjie
     */
    public function getGameRoomRecordByWhere($where )
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try{
            $gameRoomIds = $this->field( '*' )->where($where)->group('gameStartTime')->order('id ASC')->select();

        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[getGameRoomIdByWhere] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $gameRoomIds;
        return $ret;
    }
}
