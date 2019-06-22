<?php
namespace Home\Model\ClubLog;

use Think\Model;

/**
 * 俱乐部开房钻石日志表
 */
class LogClubRoomcardModel extends Model
{
    // 初始配置
    protected $connection = 'CONF_DBTYPE_CLUB_LOG';
    protected $trueTableName = 'log_club_roomcard';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据条件获取获取钻石记录
     * @author Carter
     */
    public function queryClugLogRoomcardByAttr($attr, $field)
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
        if (isset($attr['sCreateTime'])) {
            $where['createTime'][] = array('egt', $attr['sCreateTime']);
        }
        if (isset($attr['eCreateTime'])) {
            $where['createTime'][] = array('elt', $attr['eCreateTime']);
        }

        try {
            $list = $this->field($field)->where($where)->select();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryClugLogRoomcardByAttr] select failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;

        return $ret;
    }

    /**
     * 统计俱乐部代理商的开房钻石记录
     * @param array $where 查询ID
     * @return array|boole 返回单条数据或false
     * @author tangjie <tj_youxiang_2008@163.com>
     */
    public function queryClubRoomCardNumberByWhere($where)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try {
            $data = $this->where($where)->sum('cardConsume');
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryClubRoomCardNumberByWhere] select failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $data;

        return $ret;
    }
}
