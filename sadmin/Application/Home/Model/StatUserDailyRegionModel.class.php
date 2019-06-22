<?php
namespace Home\Model;

use Think\Model;

class StatUserDailyRegionModel extends Model
{
    // 数据类型
    const DATA_TYPE_REGISTER = 1; // 新增注册
    const DATA_TYPE_LOGIN = 2; // 登录人数
    const DATA_TYPE_ACTIVE = 3; // 活跃人数
    public $dataTypeMap = array(
        self::DATA_TYPE_REGISTER => '新增注册',
        self::DATA_TYPE_LOGIN => '登录人数',
        self::DATA_TYPE_ACTIVE => '活跃人数',
    );

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据条件获取每日简报指定的地区数据
     * @author Carter
     */
    public function queryStatDailyRegionByAttr($attr, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $where = array();
        if ($attr['parentId']) {
            if (is_array($attr['parentId'])) {
                $where['parent_id'] = array('in', $attr['parentId']);
            } else {
                $where['parent_id'] = $attr['parentId'];
            }
        }
        if ($attr['dataType']) {
            $where['data_type'] = $attr['dataType'];
        }

        try {
            $list = $this->field($field)->where($where)->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryStatDailyRegionByAttr] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;

        return $ret;
    }

    /**
     * 插入每日简报统计地区数据
     * @author Carter
     */
    public function insertStatUserDailyRegion($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $insertData = array(
            'parent_id' => $attr['parentId'],
            'place_id' => $attr['placeId'],
            'data_type' => $attr['dataType'],
            'data_val' => $attr['dataVal'],
        );

        try {
            $id = $this->add($insertData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertStatUserDailyRegion] insert failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_ADD_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $id;

        return $ret;
    }

    /**
     * 更新每日简报地区数据
     * @author Carter
     */
    public function updateStatUserDailyRegion($id, $dataVal)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $updateData = array(
            'data_val' => $dataVal,
        );

        try {
            $this->where(array('id' => $id))->save($updateData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateStatUserDailyRegion] update failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        return $ret;
    }
}
