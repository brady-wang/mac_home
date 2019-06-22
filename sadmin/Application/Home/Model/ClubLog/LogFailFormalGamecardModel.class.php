<?php
namespace Home\Model\ClubLog;

use Think\Model;

class LogFailFormalGamecardModel extends Model
{
    // 初始配置
    protected $connection = 'CONF_DBTYPE_CLUB_LOG';
    protected $trueTableName = 'log_fail_formal_gamecard';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取指定游戏下首条俱乐部邮件送钻日志
     * @author Carter
     */
    public function queryFirstClubFormalGamecardLog($gameId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try {
            $field = 'createDate';
            $info = $this->field($field)->order('createTime ASC')->find();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryFirstClubFormalGamecardLog] select failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if (empty($info)) {
            $ret['code'] = ERRCODE_DB_DATA_EMPTY;
            $ret['msg'] = '没有俱乐部邮件送钻日志';
            return $ret;
        }
        $ret['data'] = $info;
        return $ret;
    }

    /**
     * 获取指定游戏下指定日期的俱乐部邮件送钻产出日志列表
     * @author Carter
     */
    public function queryClubFailFormalGamecardLogList($gameId, $date)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try {
            $field = 'gameCard';
            $where = array(
                'gameId' => $gameId,
                'createDate' => $date,
            );
            $list = $this->field($field)->where($where)->select();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryClubFailFormalGamecardLogList] select failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;
        return $ret;
    }
}
