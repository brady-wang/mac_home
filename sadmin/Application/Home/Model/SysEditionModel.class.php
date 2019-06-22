<?php
namespace Home\Model;

use Think\Model;

class SysEditionModel extends Model
{
    /**
     * 根据 key 获取兼容信息
     * @author Carter
     */
    public function querySysEditionByKey($eKey)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try{
            $info = $this->field('game')->where(array('edition_key' => $eKey))->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[querySysEditionByKey] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $info;
        return $ret;
    }

    /**
     * 获取功能兼容数据，不分页
     * @author Carter
     */
    public function querySysEditionAllList()
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try{
            $field = 'id,edition_key,edition_name,del_desc,game';
            $list = $this->field($field)->order("id ASC")->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[querySysEditionAllList] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        foreach ($list as $k => $v) {
            $list[$k]['game_list'] = unserialize($v['game']);
        }

        $ret['data'] = $list;
        return $ret;
    }

    /**
     * 添加功能兼容项
     * @author Carter
     */
    public function insertSysEdition($attr, $game)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try {
            $info = $this->field('id')->where(array('edition_key' => $attr['edition_key']))->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertSysEdition] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        // 不允许重复插入
        if (!empty($info)) {
            $ret['code'] = ERRCODE_DATA_ERR;
            $ret['msg'] = "功能 {$attr['edition_key']} 已存在，不允许重复插入";
            return $ret;
        }

        $insertData = array(
            'edition_key' => $attr['edition_key'],
            'edition_name' => $attr['edition_name'],
            'del_desc' => $attr['del_desc'],
            'game' => serialize($game),
        );

        try {
            $id = $this->add($insertData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertSysEdition] insert failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_ADD_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = array_merge(array('id' => $id), $insertData);
        return $ret;
    }

    /**
     * 修改功能兼容项
     * @author Carter
     */
    public function updateSysEdition($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try {
            $info = $this->where(array('id' => $attr['id']))->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateSysEdition] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if (empty($info)) {
            $ret['code'] = ERRCODE_DATA_ERR;
            $ret['msg'] = "兼容项 {$attr['id']} 不存在，无法进行修改";
            return $ret;
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
            $this->where(array('id' => $attr['id']))->save($updateData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateSysEdition] update failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        // 将修改内容返回记录
        $ret['data'] = $updateData;

        return $ret;
    }

    /**
     * 删除功能兼容数据
     * @author Carter
     */
    public function deleteSysEdition($id)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try {
            $this->where(array('id' => $id))->delete();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[deleteSysEdition] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_DELETE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        return $ret;
    }
}
