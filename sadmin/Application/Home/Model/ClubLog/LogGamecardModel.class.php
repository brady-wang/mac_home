<?php
namespace Home\Model\ClubLog;

use Think\Model;

class LogGamecardModel extends Model
{
    // 初始配置
    protected $connection = 'CONF_DBTYPE_CLUB_LOG';
    protected $trueTableName = 'log_gamecard';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取指定游戏下首条俱乐部钻石日志
     * @author Carter
     */
    public function queryFirstClubGamecardLog($gameId)
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
            set_exception(__FILE__, __LINE__, "[queryFirstClubGamecardLog] select failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if (empty($info)) {
            $ret['code'] = ERRCODE_DB_DATA_EMPTY;
            $ret['msg'] = '没有俱乐部钻石日志';
            return $ret;
        }
        $ret['data'] = $info;
        return $ret;
    }

    /**
     * 获取指定游戏下指定日期的俱乐部钻石产出日志列表
     * @author Carter
     */
    public function queryClubGamecardProduceLogList($gameId, $date)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try {
            $field = 'id,source,changeNum';
            $where = array(
                'gameId' => $gameId,
                'source' => array('in', '2,3,6,7'),
                'createDate' => $date,
            );
            $list = $this->field($field)->where($where)->select();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryClubGamecardProduceLogList] select failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;
        return $ret;
    }

    /**
     * 统计俱乐部代理商的房卡钻石变化
     * @param array $where 查询ID
     * @return array|boole 返回单条数据或false
     * @author tangjie <tj_youxiang_2008@163.com>
     */
    public function getClubLogCountNumberByGameId($where)
    {
        $result =  array(
            'code' => ERRCODE_SUCCESS,
            'msg' => '',
            'data' => array(),
        );
        try {
            $data = $this->where($where)->sum('changeNum');
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[getClubLogCountNumberByGameId] select failed. ".$e->getMessage());
            $result['code'] = ERRCODE_DB_SELECT_ERR;
            $result['msg'] = $e->getMessage();
            return $result;
        }
        $result['data'] = $data;
        return $result;
    }
}
