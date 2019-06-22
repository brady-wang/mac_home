<?php

namespace Home\Model;

use Think\Model;

class GameConfModel extends Model
{
    // 游戏状态
    const GAME_STATUS_ONLINE = 1; // 正常运行
    const GAME_STATUS_UPDATE = 2; // 维护中
    const GAME_STATUS_READING = 3; // 准备维护
    public $gameStatusMap = array(
        self::GAME_STATUS_ONLINE => array('name' => '正常运行', 'val' => 'online'),
        self::GAME_STATUS_UPDATE => array('name' => '维护中', 'val' => 'update'),
        self::GAME_STATUS_READING => array('name' => '准备维护', 'val' => 'reading'),
    );

    // 维护公告面板开关
    const NOTIFY_STATUS_OPEN = 1; // 开启
    const NOTIFY_STATUS_CLOSE = 9; // 关闭
    public $notifyStatusMap = array(
        self::NOTIFY_STATUS_OPEN => array('name' => '开启', 'val' => 'open'),
        self::NOTIFY_STATUS_CLOSE => array('name' => '关闭', 'val' => 'close'),
    );

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 查询游戏配置信息
     * @author Carter
     */
    public function queryGameConfByGameId($gameId, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try {
            $info = $this->field($field)->where(array('game_id' => $gameId))->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGameConfByGameId] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $info;
        return $ret;
    }

    /**
     * 添加维护控制配置
     * @author Carter
     */
    public function insertGameAppConf($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try {
            $info = $this->field('id')->where(array('game_id' => $attr['game_id']))->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertGameAppConf] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        // 不允许重复插入
        if (!empty($info)) {
            $ret['code'] = ERRCODE_DATA_ERR;
            $ret['msg'] = "游戏 {$attr['game_id']} 已存在维护控制配置，不允许重复插入数据";
            return $ret;
        }

        // 系统维护时间处理
        if ('' === $attr['upgrade_time']) {
            $upgradeTime = 0;
        } else {
            $upgradeTime = strtotime($attr['upgrade_time']);
        }

        // 提醒时间点规则处理
        if (is_null($attr['upgrade_notify_rule'])) {
            $attr['upgrade_notify_rule'] = array();
        } else if (is_string($attr['upgrade_notify_rule'])) {
            $attr['upgrade_notify_rule'] = array($attr['upgrade_notify_rule']);
        }

        // 维护公告开始、结束时间处理
        if ('' === $attr['upgrade_notify_launch']) {
            $notifyStartTime = 0;
            $notifyEndTime = 0;
        } else {
            $notifyStartTime = $upgradeTime - $attr['upgrade_notify_launch'] * 60;
            $notifyEndTime = $upgradeTime;
        }

        $insertData = array(
            'game_id' => $attr['game_id'],
            'game_status' => $attr['game_status'],
            'upgrade_time' => $upgradeTime,
            'upgrade_dismiss_time' => 15,
            'upgrade_notify_rule' => serialize($attr['upgrade_notify_rule']),
            'upgrade_msg' => $attr['upgrade_msg'],
            'upgrade_notify_status' => $attr['upgrade_notify_status'],
            'upgrade_notify_start_time' => $notifyStartTime,
            'upgrade_notify_end_time' => $notifyEndTime,
            'upgrade_notify_title' => $attr['upgrade_notify_title'],
            'upgrade_notify_content' => $attr['upgrade_notify_content'],
        );

        try {
            $id = $this->add($insertData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertGameAppConf] insert failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_ADD_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = array_merge(array('id' => $id), $insertData);
        return $ret;
    }

    /**
     * 修改维护控制配置
     * @author Carter
     */
    public function updateGameAppConf($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try {
            $info = $this->where(array('game_id' => $attr['game_id']))->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateGameAppConf] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if (empty($info)) {
            $ret['code'] = ERRCODE_DATA_ERR;
            $ret['msg'] = "游戏 {$attr['game_id']} 不存在维护控制配置，无法进行修改";
            return $ret;
        }

        // 系统维护时间处理
        if ('' === $attr['upgrade_time']) {
            $attr['upgrade_time'] = 0;
        } else {
            $attr['upgrade_time'] = strtotime($attr['upgrade_time']);
        }

        // 提醒时间点规则处理
        if (is_null($attr['upgrade_notify_rule'])) {
            $attr['upgrade_notify_rule'] = serialize(array());
        } else if (is_string($attr['upgrade_notify_rule'])) {
            $attr['upgrade_notify_rule'] = serialize(array($attr['upgrade_notify_rule']));
        } else {
            $attr['upgrade_notify_rule'] = serialize($attr['upgrade_notify_rule']);
        }

        // 维护公告开始、结束时间处理
        if ('' === $attr['upgrade_notify_launch']) {
            $attr['upgrade_notify_start_time'] = 0;
            $attr['upgrade_notify_end_time'] = 0;
        } else {
            $attr['upgrade_notify_start_time'] = $attr['upgrade_time'] - $attr['upgrade_notify_launch'] * 60;
            $attr['upgrade_notify_end_time'] = $attr['upgrade_time'];
        }

        // 先过滤出拥有相同 key 的数组，再获取 value 不同的列
        $intersectArr = array_intersect_key($attr, $info);
        $updateData = array_diff_assoc($intersectArr, $info);
        if ($updateData == array()) {
            $ret['code'] = ERRCODE_UPDATE_NONE;
            $ret['msg'] = '无任何修改';
            return $ret;
        }

        try {
            $this->where(array('id' => $info['id']))->save($updateData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateGameAppConf] update failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        // 将修改内容返回记录
        $ret['data'] = array(
            'id' => $info['id'],
            'update' => $updateData,
        );

        return $ret;
    }
}
