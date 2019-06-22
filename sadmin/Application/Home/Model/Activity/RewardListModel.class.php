<?php
namespace Home\Model\Activity;

use Think\Model;

class ActivityListModel extends Model
{

    /**
     * 机器人概况 - 获取每个游戏场次
     * @author Giant
     */
    public function getList($data)
    {

        $where = [];
        if (isset($data['name'])) {
            $where['name'] = ["like", ];
        }

        if (isset($data['start_time'])) {
            $where['start_time'] = $data['start_time'];
        }

        if (isset($data['end_time'])) {
            $where['end_time'] = $data['end_time'];
        }

        $list = $this->where($where)->select();
        return $list;
    }
}
