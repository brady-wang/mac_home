<?php
namespace Home\Model;

use Think\Model;

class SysRoleModel extends Model
{
    /**
     * 通过角色 id 获取角色信息
     * @author Carter
     */
    public function querySysRoleById($id, $field = "*")
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try{
            $info = $this->field($field)->where(array('id' => $id))->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[querySysRoleById] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $info;
        return $ret;
    }

    /**
     * 通过参数获取角色列表
     * @author Carter
     */
    public function querySysRoleListByAttr($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array();
        if ($attr['role_id']) {
            if (is_array($attr['role_id'])) {
                $where['id'] = array('in', $attr['role_id']);
            } else {
                $where['id'] = $attr['role_id'];
            }
        }

        try{
            $field = "id,role_name,sort";
            $list = $this->field($field)->where($where)->order("id DESC")->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[querySysRoleListByAttr] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $list;
        return $ret;
    }

    /**
     * 获取所有角色 map
     * @author Carter
     */
    public function querySysRoleMap()
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try {
            $list = $this->field('id,role_name')->order("sort ASC")->select();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[querySysRoleMap] select failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $map = array();
        foreach ($list as $v) {
            $map[$v['id']] = $v['role_name'];
        }

        $ret['data'] = $map;
        return $ret;
    }

    /**
     * 插入角色
     * @author Carter
     */
    public function insertSysRole($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        // 检查角色名是否有重复
        try {
            $user = $this->field('id')->where(array('role_name' => $attr['role_name']))->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertSysUser] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if (!empty($user)) {
            $ret['code'] = ERRCODE_DATA_OVERLAP;
            $ret['msg'] = '角色名已存在';
            return $ret;
        }

        // 角色默认插到末尾
        try {
            $roleCount = $this->count();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertSysRole] count failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_ADD_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $insertData = array(
            'role_name' => $attr['role_name'],
            'sort' => ++$roleCount,
        );

        try {
            $id = $this->add($insertData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertSysRole] insert failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_ADD_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = array_merge(array('id' => $id), $insertData);
        return $ret;
    }

    /**
     * 根据角色 id 修改角色信息
     * @author Carter
     */
    public function updateSysRoleByRoleId($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        // 获取角色信息
        try {
            $field = "id,role_name";
            $info = $this->field($field)->where(array('id' => $attr['id']))->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateSysRoleByRoleId] select info failed, ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if (empty($info)) {
            $ret['code'] = ERRCODE_DB_DATA_EMPTY;
            $ret['msg'] = '数据缺失';
            return $ret;
        }

        // 先过滤出拥有相同 key 的数组，再获取 value 不同的列
        $updateData = array_diff_assoc(array_intersect_key($attr, $info), $info);
        if (empty($updateData)) {
            return $ret;
        }

        // 检查角色名是否有重复
        if (isset($updateData['role_name'])) {
            try {
                $user = $this->field('id')->where(array('role_name' => $updateData['role_name']))->find();
            } catch(\Exception $e) {
                set_exception(__FILE__, __LINE__, "[updateSysRoleByRoleId] select failed: ".$e->getMessage());
                $ret['code'] = ERRCODE_DB_SELECT_ERR;
                $ret['msg'] = $e->getMessage();
                return $ret;
            }
            if (!empty($user)) {
                $ret['code'] = ERRCODE_DATA_OVERLAP;
                $ret['msg'] = '角色名已存在';
                return $ret;
            }
        }

        // 修改数据
        try {
            $this->where(array('id' => $attr['id']))->save($updateData);
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateSysRoleByRoleId] update failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $updateData;

        return $ret;
    }

    /**
     * 根据角色 id 删除角色
     * @author Carter
     */
    public function deleteSysRoleByRoleId($roleId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // 获取该角色排序信息
        try {
            $info = $this->field('sort')->where(array('id' => $roleId))->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[deleteSysRoleByRoleId] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        // 将排序在该角色之后的排序进一
        try {
            $this->where('sort>%d', $info['sort'])->setDec('sort');
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[deleteSysRoleByRoleId] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        // 删除角色
        try {
            $this->where(array('id' => $roleId))->delete();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[deleteSysRoleByRoleId] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_DELETE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        return $ret;
    }
}
