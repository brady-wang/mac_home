<?php

namespace Home\Model\GameLogDev;

use Think\Model;

class UShareLogModel extends Model
{
    // 初始配置
    protected $connection = 'CONF_DBTYPE_GAME_LOG_DEV';
    protected $trueTableName = 'u_share_log';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据条件获取分享统计流水
     * @author tangjie
     */
    public function queryUShareAllLogByAttr($attr, $field = "*")
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array();
        if (isset($attr['startTime'])) {
            $where['createTime'][] = array('egt', $attr['startTime']);
        }
        if (isset($attr['endTime'])) {
            $where['createTime'][] = array('elt', $attr['endTime']);
        }

        try {
            $list = $this->field($field)->where($where)->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryUShareAllLogByAttr] select failed, ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;

        return $ret;
    }
}
