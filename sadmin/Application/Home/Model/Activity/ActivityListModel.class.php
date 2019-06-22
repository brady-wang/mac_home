<?php
namespace Home\Model\Activity;

use Think\Model;

class ActivityListModel extends Model
{

    const GAME_STATUS_ON = 0; // 上线
    const GAME_STATUS_OFF = 1; // 下架
    public $statusInfo = array(
        self::GAME_STATUS_ON => array('name' => '上线', 'label' => 'label-success'),
        self::GAME_STATUS_OFF => array('name' => '下架', 'label' => 'label-default'),
    );

    /**
     * 机器人概况 - 获取每个游戏场次
     * @author Giant
     */
    public function getList($data)
    {

        $where = ['pid' => C('G_USER.gameid')];
        if (!empty($data['name'])) {
            $where['name'] = ['like', "%{$data['name']}%"];
        }

        if (!empty($data['start_time'])) {
            $where['start_time'] = ['egt', strtotime($data['start_time'])];
        }

        if (!empty($data['end_time'])) {
            $where['end_time'] = ['elt', strtotime($data['end_time'])];
        }

        $list = $this->where($where)->order('id desc')->select();
        //p($this->getLastSql());
        return $list;
    }

    /**
     * 获取活动信息
     * @param $id
     * @return array
     */
    public function getActivityInfo($actId, $pid)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try{
            $info = $this->field('*')->where(array('act_id' => $actId, 'pid' => $pid))->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[getActivityInfo] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $info;
        return $ret;
    }

    public function updateActConf($param)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = [
            'act_id' => $param['act_id'],
            'pid' => $param['pid']
        ];

        $updateData = [
            'name' => $param['name'],
            'start_time' => $param['start_time'],
            'end_time' => $param['end_time'],
            'act_conf' => $param['act_conf'],
            'update_time' => time(),
            'update_by' => C('G_USER.username')
        ];

        try{
            $this->where($where)->save($updateData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateActConf] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        return $ret;
    }

    public function getStatusName($id = '')
    {
        return isset($id) ? "<div class='label {$this->statusInfo[$id]['label']}'>". $this->statusInfo[$id]['name']. "</div>" : $this->statusInfo;
    }
}
