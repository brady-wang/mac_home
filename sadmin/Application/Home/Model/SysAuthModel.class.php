<?php
namespace Home\Model;

use Think\Model;

class SysAuthModel extends Model
{
    // 类型
    const AUTH_TYPE_ROLE_ACCS = 1; // 角色访问权限
    const AUTH_TYPE_ROLE_OPER = 2; // 角色操作权限
    const AUTH_TYPE_USER_ACCS = 11; // 用户访问权限
    const AUTH_TYPE_USER_OPER = 12; // 用户操作权限

    /**
     * 获取用户权限列表
     * @author Carter
     */
    public function queryUserAuthByUid($uid)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try {
            $field = "auth_type,auth_code";
            $where = array(
                'uid' => $uid,
                'auth_type' => array('in', array(self::AUTH_TYPE_USER_ACCS, self::AUTH_TYPE_USER_OPER)),
            );
            $list = $this->field($field)->where($where)->select();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryUserAuthByUid] select failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $access = array();
        $operate = array();
        foreach ($list as $v) {
            if (self::AUTH_TYPE_USER_ACCS == $v['auth_type']) {
                $access[] = $v['auth_code'];
            } else if (self::AUTH_TYPE_USER_OPER == $v['auth_type']) {
                $operate[] = $v['auth_code'];
            }
        }
        $ret['data'] = array(
            'access' => $access,
            'operate' => $operate,
        );

        return $ret;
    }

    /**
     * 通过角色 id 获取角色权限
     * @author Carter
     */
    public function querySysRoleAuthByRoleId($roleId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try {
            $field = "auth_type,auth_code";
            $where = array(
                'auth_type' => array('in', array(self::AUTH_TYPE_ROLE_ACCS, self::AUTH_TYPE_ROLE_OPER)),
                'role_id' => $roleId,
            );
            $list = $this->field($field)->where($where)->select();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[querySysRoleAuthByRoleId] select failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if (empty($list)) {
            $ret['code'] = ERRCODE_DB_DATA_EMPTY;
            $ret['msg'] = '遗失权限数据，请联系系统管理员';
            return $ret;
        }

        $access = array();
        $operate = array();
        foreach ($list as $v) {
            if (self::AUTH_TYPE_ROLE_ACCS == $v['auth_type']) {
                $access[] = $v['auth_code'];
            } else if (self::AUTH_TYPE_ROLE_OPER == $v['auth_type']) {
                $operate[] = $v['auth_code'];
            }
        }
        $ret['data'] = array(
            'access' => $access,
            'operate' => $operate,
        );

        return $ret;
    }

    /**
     * 批量插入权限（仅针对角色，创建角色时用）
     * @author Carter
     */
    public function insertSysAuthForRole($roleId, $accesAuth, $operAuth)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $insertData = array();
        foreach ($accesAuth as $v) {
            $insertData[] = array(
                'auth_type' => self::AUTH_TYPE_ROLE_ACCS,
                'role_id' => $roleId,
                'uid' => 0,
                'auth_code' => $v,
            );
        }
        foreach ($operAuth as $v) {
            $insertData[] = array(
                'auth_type' => self::AUTH_TYPE_ROLE_OPER,
                'role_id' => $roleId,
                'uid' => 0,
                'auth_code' => $v,
            );
        }

        try {
            $this->addAll($insertData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertSysAuthForRole] insert failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_ADD_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = array('auth' => $insertData);
        return $ret;
    }

    /**
     * 根据角色权限插入用户权限
     * @author
     */
    public function insertSysUserAuth($roleId, $uid, $access, $operate)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $insertData = array();
        foreach ($access as $v) {
            $insertData[] = array(
                'auth_type' => self::AUTH_TYPE_USER_ACCS,
                'role_id' => $roleId,
                'uid' => $uid,
                'auth_code' => $v,
            );
        }
        foreach ($operate as $v) {
            $insertData[] = array(
                'auth_type' => self::AUTH_TYPE_USER_OPER,
                'role_id' => $roleId,
                'uid' => $uid,
                'auth_code' => $v,
            );
        }

        try {
            $this->addAll($insertData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertSysUserAuth] insert failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_ADD_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $insertData;
        return $ret;
    }

    /**
     * 根据角色 id 修改角色及其用户权限
     * @author Carter
     */
    public function updateSysAuthByRoleId($roleId, $accs, $oper)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        // 获取该角色下所有权限列表
        try {
            $field = "id,auth_type,uid,auth_code";
            $list = $this->field($field)->where(array('role_id' => $roleId))->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateSysAuthByRoleId] select info failed, ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        // 当前角色访问权限数组
        $roleAccs = array();

        // 当前角色操作权限数组
        $roleOper = array();

        // 当前角色下，权限值与所有 id 的 map ，用于删掉旧权限
        $authIdMap = array();

        // 该角色所有用户列表
        $userList = array();

        foreach ($list as $v) {
            // 记录角色访问权限
            if (self::AUTH_TYPE_ROLE_ACCS == $v['auth_type']) {
                $roleAccs[] = $v['auth_code'];
            }
            // 记录角色操作权限
            else if (self::AUTH_TYPE_ROLE_OPER == $v['auth_type']) {
                $roleOper[] = $v['auth_code'];
            }
            // 记录该角色所有用户 uid
            else {
                $userList[] = $v['uid'];
            }

            $authIdMap[$v['auth_code']][] = $v['id'];
        }
        // 去重
        $roleAccs = array_flip(array_flip($roleAccs));
        $roleOper = array_flip(array_flip($roleOper));
        $userList = array_flip(array_flip($userList));
        
        if(is_null($oper)){
            $oper = array();
        }
        

        // 需要删除的权限
        $deleteAuth = array_merge(
            array_diff($roleAccs, $accs),
            array_diff($roleOper, $oper)
        );
        $delArr = array();
        foreach ($deleteAuth as $v) {
            $delArr = array_merge($delArr, $authIdMap[$v]);
        }
        if (!empty($delArr)) {
            try {
                $this->where(array('id' => array('in', $delArr)))->delete();
            } catch(\Exception $e) {
                set_exception(__FILE__, __LINE__, "[updateSysAuthByRoleId] delete failed, ".$e->getMessage());
                $ret['code'] = ERRCODE_DB_DELETE_ERR;
                $ret['msg'] = $e->getMessage();
                return $ret;
            }
        }

        // 需要新增的权限
        $insertAccsAuth = array_diff($accs, $roleAccs);
        $insertOperAuth = array_diff($oper, $roleOper);
        $insertData = array();
        foreach ($insertAccsAuth as $v) {
            $insertData[] = array(
                'auth_type' => self::AUTH_TYPE_ROLE_ACCS,
                'role_id' => $roleId,
                'uid' => 0,
                'auth_code' => $v,
            );
            foreach ($userList as $uid) {
                $insertData[] = array(
                    'auth_type' => self::AUTH_TYPE_USER_ACCS,
                    'role_id' => $roleId,
                    'uid' => $uid,
                    'auth_code' => $v,
                );
            }
        }
        foreach ($insertOperAuth as $v) {
            $insertData[] = array(
                'auth_type' => self::AUTH_TYPE_ROLE_OPER,
                'role_id' => $roleId,
                'uid' => 0,
                'auth_code' => $v,
            );
            foreach ($userList as $uid) {
                $insertData[] = array(
                    'auth_type' => self::AUTH_TYPE_USER_OPER,
                    'role_id' => $roleId,
                    'uid' => $uid,
                    'auth_code' => $v,
                );
            }
        }

        try {
            $this->addAll($insertData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateSysAuthByRoleId] insert failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_ADD_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = array(
            'delete' => $deleteAuth,
            'insert' => $insertData,
        );
        return $ret;
    }

    /**
     * 根据角色 id 修改用户权限，删除旧权限，根据新角色插入角色权限
     * @author Carter
     */
    public function updateUserAuthByRoleId($uid, $roleId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        // 删除旧用户权限
        try {
            $this->where(array('uid' => $uid))->delete();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateUserAuthByRoleId] delete failed, ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_DELETE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        // 获取该角色下所有权限列表
        try {
            $where = array(
                'role_id' => $roleId,
                'auth_type' => array('in', array(self::AUTH_TYPE_ROLE_ACCS, self::AUTH_TYPE_ROLE_OPER)),
            );
            $list = $this->field('auth_type,auth_code')->where($where)->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateUserAuthByRoleId] select info failed, ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        // 角色权限就是用户要插入的权限
        $insertData = array();
        foreach ($list as $v) {
            if (self::AUTH_TYPE_ROLE_ACCS == $v['auth_type']) {
                $insertData[] = array(
                    'auth_type' => self::AUTH_TYPE_USER_ACCS,
                    'role_id' => $roleId,
                    'uid' => $uid,
                    'auth_code' => $v['auth_code'],
                );
            } else if (self::AUTH_TYPE_ROLE_OPER == $v['auth_type']) {
                $insertData[] = array(
                    'auth_type' => self::AUTH_TYPE_USER_OPER,
                    'role_id' => $roleId,
                    'uid' => $uid,
                    'auth_code' => $v['auth_code'],
                );
            }
        }

        try {
            $this->addAll($insertData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateUserAuthByRoleId] insert failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_ADD_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $insertData;
        return $ret;
    }

    /**
     * 根据角色 id 删除权限
     * @author Carter
     */
    public function deleteSysAuthByRoleId($roleId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        try {
            $this->where(array('role_id' => $roleId))->delete();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[deleteSysAuthByRoleId] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_DELETE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        return $ret;
    }

    /**
     * 根据用户id删除用户所有权限
     * @author Carter
     */
    public function deleteSysUserAuthByUid($uid)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        try {
            $this->where(array('uid' => $uid))->delete();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[deleteSysUserAuthByUid] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_DELETE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        return $ret;
    }
}
