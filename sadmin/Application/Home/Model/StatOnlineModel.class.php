<?php
namespace Home\Model;

use Think\Model;

class StatOnlineModel extends Model
{
    /**
     * 获取数据列表
     * @author tangjie
     */
    public function getGameOnlineStatList($where)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        //取得数据列表
        try {
            $field = '*';
            $list = $this->field($field)->where($where)->order('id ASC')->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[getGameOnlineStatList] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $list;

        return $ret;
    }

    /**
     * 判断数据是否存在
     * @param int $dateTime 默认显示数据
     * @return array|boole 返回单条数据或false
     * @author tangjie <tj_youxiang_2008@163.com>
     */
    public function getDateTimeIsExtsts($gameId, $dataHour = 0, $dateTime = 0)
    {
        if (empty($dateTime) || empty($gameId)) {
            return false ;
        }
        $map = array(
            'game_id' => $gameId,
            'data_hour' => $dataHour,
            'data_time' => $dateTime
        );
        $info = $this->where($map)->find();
        return $info;
    }

    /**
     * 插入一条基础数据
     * @param int $date 默认显示数据
     * @return array|boole 返回单条数据或false
     * @author tangjie <tj_youxiang_2008@163.com>
     */
    public function addEmptyData($data)
    {
        if (empty($data)) {
            return false ;
        }

        $info = $this->add($data);
        return $info;
    }

    /**
     * 插入一条基础数据
     * @param int $date 默认显示数据
     * @return array|boole 返回单条数据或false
     * @author tangjie <tj_youxiang_2008@163.com>
     */
    public function updateStatData($id, $data)
    {
        if( empty( $data) || empty($id)){
            return false ;
        }
        $data['update_time'] = time();
        $where= array(
            'id' => (int) $id
        );
        $info = $this->where($where)->save($data);
        return $info;
    }
}
