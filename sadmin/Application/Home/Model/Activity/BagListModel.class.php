<?php
namespace Home\Model\Activity;

use Think\Model;

class BagListModel extends Model
{

    public $luckInfo = array(
        1 => array('name' => '普通礼包', 'label' => 'label-info'),
        2 => array('name' => '抽奖礼包', 'label' => 'label-warning'),
    );

    /**
     * 获取礼包列表
     * @author rave
     */
    public function getList($data)
    {

        $where = ['status' => 0];
        if (!empty($data['name'])) {
            $where['name'] = ["like", "%{$data['name']}%"];
        }

        if (!empty($data['luck'])) {
            $where['luck'] = $data['luck'];
        }

        if (!empty($data['optBy'])) {
            $where['update_by'] = ["like", "%{$data['optBy']}%"];
        }

        if (!empty($data['bagId'])) {
            $where['id'] = $data['bagId'];
        }
        
        $list = $this->where($where)->order('id desc')->select();
        //p($list, $this->getLastSql());
        return $list;
    }
}
