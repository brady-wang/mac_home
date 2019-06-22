<?php
namespace Home\Model\ClubDict;

use Think\Model;

class DictShareModel extends Model
{
    // 初始配置
    protected $connection = 'AGENT_ALL_DICT_DB';
    protected $trueTableName = 'dict_share';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取指定游戏俱乐部分享信息
     * @author Carter
     */
    public function queryClubShareByGameId($gameId, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array(
            'gameId' => $gameId,
        );

        try{
            $info = $this->field($field)->where($where)->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryClubShareByGameId] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $info;
        return $ret;
    }
}
