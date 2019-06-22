<?php
namespace Home\Logic;

use Home\Model\SysAuthModel;
use Home\Model\SysRoleModel;
use Home\Model\SysUserModel;

class AccountLogic
{

    /****************************** 权限校验 ******************************/

    /**
     * 校验登录权限并返回信息
     * 不走 Behavior CheckAuth 作权限验证的 action ，通过本方法进行权限校验并返回导航栏信息等
     * @author Carter
     */
    public function getUserAuthInfoAfterVertify()
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $userMod = new SysUserModel();
        $authMod = new SysAuthModel();

        // 获取 cookie remember_token
        $rememberToken = I('cookie.remember_token');
        if (empty($rememberToken)) {
            // cookie 数据不存在
            $ret['code'] = ERRCODE_OPER_UNAUTH;
            $ret['msg'] = '遗失重要数据';
            return $ret;
        }

        // 获取 cookie uid
        $uid = think_decrypt(I('cookie.sadmin_identify'));
        if (false == $uid) {
            // 解密失败
            $ret['code'] = ERRCODE_OPER_UNAUTH;
            $ret['msg'] = '遗失身份数据';
            return $ret;
        }

        // 获取 cookie game id
        $gameId = I('cookie.sadmin_gid');
        if (empty($gameId)) {
            // cookie 数据不存在
            $ret['code'] = ERRCODE_OPER_UNAUTH;
            $ret['msg'] = '遗失游戏地区数据';
            return $ret;
        }

        // 获取用户信息
        $field = "username,role_id,remember_token,status";
        $modRet = $userMod->querySysUserInfoByUid($uid, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $user = $modRet['data'];

        // 用户状态异常
        if ($userMod::STATUS_NORMAL != $user['status']) {
            $ret['code'] = ERRCODE_OPER_UNAUTH;
            $ret['msg'] = '用户状态异常，请找管理员确认';
            return $ret;
        }

        // token 必须一致
        if ($user['remember_token'] !== $rememberToken) {
            $ret['code'] = ERRCODE_OPER_UNAUTH;
            $ret['msg'] = '登录令牌校验失败';
            return $ret;
        }

        // 获取权限信息
        $modRet = $authMod->queryUserAuthByUid($uid);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $auth = $modRet['data'];

        $authMap = C('C_ACCESS_NAV_MAP');

        // 对 authMap(完整 nav 表)进行过滤，只留下用户拥有访问权限的项
        $nav = array();
        foreach ($authMap as $mCode => $main) {
            // 拥有权限，才可以加到导航表中
            if (in_array($mCode, $auth['access'])) {
                $nav[$mCode] = array(
                    'name' => $main['name'],
                    'url' => $main['url'],
                    'icon' => $main['icon'],
                    'sublevel' => array(),
                );
            }
            // 扫描二级目录
            foreach ($main['sublevel'] as $sCode => $sublevel) {
                if (in_array($sCode, $auth['access'])) {
                    $nav[$mCode]['sublevel'][$sCode] = array(
                        'name' => $sublevel['name'],
                        'url' => $sublevel['url'],
                        'third' => array(),
                    );
                }
                // 如果存在三级目录，则扫描第三级
                if (!empty($sublevel['third'])) {
                    foreach ($sublevel['third'] as $tCode => $third) {
                        if (in_array($tCode, $auth['access'])) {
                            $nav[$mCode]['sublevel'][$sCode]['third'][$tCode] = array(
                                'name' => $third['name'],
                                'url' => $third['url'],
                            );
                        }
                    }
                }
            }
        }

        $ret['data'] = array(
            'uid' => $uid,
            'username' => $user['username'],
            'roleId' => $user['role_id'],
            'gameId' => $gameId,
            'nav' => $nav,
        );

        return $ret;
    }

    /**
     * 获取用户权限信息
     * @author Carter
     */
    public function getUserAuthInfoByUid($uid)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $userMod = new SysUserModel();
        $authMod = new SysAuthModel();

        // 获取用户信息
        $modRet = $userMod->querySysUserInfoByUid($uid, "username,remember_token,timeout,status,role_id");
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $user = $modRet['data'];

        // 用户状态异常
        if ($userMod::STATUS_NORMAL != $user['status']) {
            $ret['code'] = ERRCODE_VALIDATE_FAILED;
            $ret['msg'] = "用户状态异常，可能已被管理员移除";
            return $ret;
        }

        // 获取权限信息
        $modRet = $authMod->queryUserAuthByUid($uid);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $auth = $modRet['data'];

        $ret['data'] = array_merge($user, $auth);

        return $ret;
    }

    /****************************** 角色 ******************************/

    /**
     * 根据查询条件获取角色列表，顺序排序，且包含用户信息
     * @author Carter
     */
    public function getSysRoleList($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $roleMod = new SysRoleModel();
        $userMod = new SysUserModel();

        // 查询条件
        $where = array();
        if ($attr['username']) {
            $modRet = $userMod->queryAllSysUserByAttr(array('username_like' => $attr['username']), "role_id");
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $ret['code'] = $modRet['code'];
                $ret['msg'] = $modRet['msg'];
                return $ret;
            }
            $roleIdArr = array();
            foreach ($modRet['data'] as $v) {
                $roleIdArr[] = $v['role_id'];
            }
            $where['role_id'] = empty($roleIdArr) ? -1 : $roleIdArr;
        }

        // 获取角色列表
        $modRet = $roleMod->querySysRoleListByAttr($where);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $roleList = $modRet['data'];

        // 角色 map
        $roleMap = array();
        if (empty($roleList)) {
            $ret['data'] = $roleMap;
            return $ret;
        }

        $roleIds = array();
        foreach ($roleList as $v) {
            $roleMap[$v['id']] = array(
                'role_name' => $v['role_name'],
                'sort' => $v['sort'],
                'user' => array(),
            );
            $roleIds[] = $v['id'];
        }

        $modRet = $userMod->queryAllSysUserByAttr(array('role_id' => $roleIds));
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $userList = $modRet['data'];

        // 将用户分到角色列表中，关联了员工信息的，需要把部门及姓名一起推过去
        foreach ($userList as $v) {
            $roleMap[$v['role_id']]['user'][] = array(
                'username' => $v['username'],
            );
        }
        $ret['data'] = $roleMap;

        return $ret;
    }

    /**
     * 根据角色 id 获取角色信息及权限信息
     * @author Carter
     */
    public function getRoleAuthInfoByRoleId($roleId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $roleMod = new SysRoleModel();
        $authMod = new SysAuthModel();

        // 获取角色信息
        $field = 'id,role_name';
        $modRet = $roleMod->querySysRoleById($roleId, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $roleData = $modRet['data'];

        // 获取权限信息
        $modRet = $authMod->querySysRoleAuthByRoleId($roleId);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $authData = $modRet['data'];

        $ret['data'] = array_merge($roleData, array('auth' => $authData));
        return $ret;
    }

    /**
     * 添加角色
     * @author Carter
     */
    public function addSysRole($attr) {

        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $roleMod = new SysRoleModel();
        $authMod = new SysAuthModel();

        // 插入角色表
        $modRet = $roleMod->insertSysRole($attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $roleData = $modRet['data'];
        $roleId = $roleData['id'];
        $ret['role_id'] =  $roleId ;
        // 插入权限表
        $modRet = $authMod->insertSysAuthForRole($roleId, $attr['access'], $attr['oper']);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $authData = $modRet['data'];

        $ret['data'] = array_merge($roleData, $authData);
        return $ret;
    }

    /**
     * 修改角色
     * @author Carter
     */
    public function editSysRole($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $roleMod = new SysRoleModel();
        $authMod = new SysAuthModel();

        // 修改角色信息
        $modRet = $roleMod->updateSysRoleByRoleId($attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $roleData = $modRet['data'];

        // 修改权限
        $modRet = $authMod->updateSysAuthByRoleId($attr['id'], $attr['access'], $attr['oper']);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $authData = $modRet['data'];

        $ret['data'] = array(
            'role' => $roleData,
            'auth' => $authData,
        );
        return $ret;
    }

    /**
     * 根据角色 id 删除角色
     * @author Carter
     */
    public function removeSysRoleByRoleId($roleId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $roleMod = new SysRoleModel();
        $authMod = new SysAuthModel();
        $userMod = new SysUserModel();

        // 确认该角色下已没有任何用户
        $modRet = $userMod->queryAllSysUserByAttr(array('role_id' => $roleId), "uid");
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        if (!empty($modRet['data'])) {
            $ret['code'] = ERRCODE_PARAM_INVALID;
            $ret['msg'] = '该角色下还存在用户，请先将所有用户移到其他角色再进行删除';
            return $ret;
        }

        // 删除该角色所有权限
        $modRet = $authMod->deleteSysAuthByRoleId($roleId);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }

        // 删除角色
        $modRet = $roleMod->deleteSysRoleByRoleId($roleId);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }

        return $ret;
    }

    /****************************** 用户 ******************************/

    /**
     * 添加用户
     * @author Carter
     */
    public function addSysUser($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $userMod = new SysUserModel();
        $authMod = new SysAuthModel();

        // 添加用户信息
        $modRet = $userMod->insertSysUser($attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $uid = $modRet['data']['uid'];
        $userData = $modRet['data']['insert'];

        // 获取角色权限
        $modRet = $authMod->querySysRoleAuthByRoleId($attr['role_id']);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $authData = $modRet['data'];

        // 添加用户权限
        $modRet = $authMod->insertSysUserAuth($attr['role_id'], $uid, $authData['access'], $authData['operate']);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }

        $ret['data'] = array(
            'uid' => $uid,
            'role_id' => $attr['role_id'],
            'user' => $userData,
            'auth' => $authData,
        );
        return $ret;
    }

    /**
     * 修改用户
     * @author Carter
     */
    public function editSysUserByUid($uid, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $userMod = new SysUserModel();
        $authMod = new SysAuthModel();

        // 修改用户信息
        $modRet = $userMod->updateSysUserInfo($uid, $attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $userUp = $modRet['data'];

        // 角色有改变，需要根据角色全新刷新用户权限
        if (isset($userUp['role_id'])) {
            $modRet = $authMod->updateUserAuthByRoleId($uid, $userUp['role_id']);
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $ret['code'] = $modRet['code'];
                $ret['msg'] = $modRet['msg'];
                return $ret;
            }
            $authUp = $modRet['data'];
        }

        $ret['data'] = array(
            'user' => $userUp,
            'auth' => $authUp ? : null,
        );
        return $ret;
    }

    /**
     * 移除用户
     * @author Carter
     */
    public function removeSysUserByUid($uid)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $userMod = new SysUserModel();
        $authMod = new SysAuthModel();

        // 删除用户所有权限
        $modRet = $authMod->deleteSysUserAuthByUid($uid);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }

        // 不作物理删除，仅修改状态，同时移除角色
        $modRet = $userMod->updateSysToDeleteStatus($uid);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }

        return $ret;
    }

    /**
     * 恢复用户到正常状态
     * @author Carter
     */
    public function recoverSysUser($uid, $roleId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $userMod = new SysUserModel();
        $authMod = new SysAuthModel();

        // 通过角色 id 将角色的权限赋给用户
        $modRet = $authMod->querySysRoleAuthByRoleId($roleId);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $auth = $modRet['data'];
        $modRet = $authMod->insertSysUserAuth($roleId, $uid, $auth['access'], $auth['operate']);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $authData = $modRet['data'];

        // 恢复用户状态，并绑定角色
        $modRet = $userMod->updateSysToNormalStatus($uid, $roleId);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $upData = $modRet['data'];

        $ret['data'] = array(
            'uid' => $uid,
            'role_id' => $roleId,
            'auth' => $authData,
        );
        return $ret;
    }
}
