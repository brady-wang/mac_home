<?php
namespace Home\Model;

use Think\Model;

class SysUserModel extends Model
{
    // 用户状态
    const STATUS_NORMAL = 1; // 正常
    const STATUS_DELETE = 9; // 已删除

    /**
     * 执行用户登陆
     * @author Carter
     */
    public function execUserLogin($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // cookie 存续时长上限，一周 60 * 60 * 24 * 7
        $cookieLiveTime = 604800;

        try {
            // 根据昵称获取用户信息
            $userInfo = $this->where(array('username' => $attr['username']))->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[execUserLogin] select failed ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if (empty($userInfo)) {
            $ret['code'] = ERRCODE_DB_DATA_EMPTY;
            $ret['msg'] = "用户不存在";
            return $ret;
        }

        // 判断用户状态
        if (self::STATUS_NORMAL != $userInfo['status']) {
            $ret['code'] = ERRCODE_VALIDATE_FAILED;
            $ret['msg'] = "用户状态异常，请与系统管理员联系！";
            return $ret;
        }

        // 判断密码是否一致
        if (true !== password_verify($attr['password'], $userInfo['password'])) {
            $ret['code'] = ERRCODE_VALIDATE_FAILED;
            $ret['msg'] = "密码有误";
            return $ret;
        }

        // 根据“记住密码”选项确定 cookie 续存时长
        if (empty($attr['remember'])) {
            $expire = 0;
            $timeout = 0;
        } else {
            $expire = $cookieLiveTime;
            $timeout = time() + $cookieLiveTime;
        }

        // 将游戏id写入 cookie
        cookie('sadmin_gid', $attr['region'], $expire);

        // 将 uid 进行加密，写入 cookie
        $encryptIdentify = think_encrypt($userInfo['uid']);
        cookie('sadmin_identify', $encryptIdentify, $expire);

        // 记录 remember_token
        $rememberToken = md5($userInfo['uid'].mt_rand());
        cookie('remember_token', $rememberToken, $expire);

        // 更新 remember_token 和登陆信息
        $updateData = array(
            'remember_token' => $rememberToken,
            'timeout' => $timeout,
        );
        try {
            $this->where(array('uid' => $userInfo['uid']))->save($updateData);
        } catch(\Exception $e) {
            // 出错，清理 cookie
            cookie('sadmin_identify', null);
            cookie('remember_token', null);

            set_exception(__FILE__, __LINE__, "[execUserLogin] update failed ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $user = array(
            'uid' => $userInfo['uid'],
            'gameid' => $attr['region'],
        );
        C('G_USER', $user);

        return $ret;
    }

    /**
     * 通过 id 获取用户信息
     * @author Carter
     */
    public function querySysUserInfoByUid($uid, $field = "*")
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try {
            $userInfo = $this->field($field)->where(array('uid' => $uid))->find();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[querySysUserInfoByUid] select failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if (empty($userInfo)) {
            $ret['code'] = ERRCODE_DB_DATA_EMPTY;
            $ret['msg'] = '用户记录不存在';
            return $ret;
        }
        $ret['data'] = $userInfo;
        return $ret;
    }

    /**
     * 通过用户名查询用户信息
     * @author tangjie
     */
    public function querySysUserByUsername($username, $field = "*")
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array(
            'username' => $username,
        );
        try {
            $userInfo = $this->field($field)->where($where)->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[querySysUserByUsername] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $userInfo;

        return $ret;
    }

    /**
     * 通过参数查询用户列表
     * @author Carter
     */
    public function querySysUserByAttr($attr, $field = "*")
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array();
        if ($attr['uid']) {
            $where['uid'] = $attr['uid'];
        }
        if ($attr['username']) {
            $where['username'] = array('like', "%{$attr['username']}%");
        }
        if ($attr['role_id']) {
            $where['role_id'] = $attr['role_id'];
        }
        if ($attr['status']) {
            $where['status'] = $attr['status'];
        }

        // 分页获取
        $pageSize = C('PAGE_SIZE');
        $count = $this->where($where)->count();
        $paginate = new \Think\Page($count, $pageSize);
        $pagination = $paginate->show();

        $page = $paginate->getCurPage();
        try{
            $userList = $this->field($field)->where($where)->order('uid DESC')->page("{$page},{$pageSize}")->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[querySysUserByAttr] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data']['list'] = $userList;
        $ret['data']['pagination'] = $pagination;
        return $ret;
    }

    /**
     * 通过参数获取所有用户列表，包括已删除等所有状态的用户，且不分页
     * @author Carter
     */
    public function queryAllSysUserByAttr($attr, $field = "*")
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array();
        if ($attr['uid']) {
            if (is_array($attr['uid'])) {
                $where['uid'] = array('in', $attr['uid']);
            } else {
                $where['uid'] = $attr['uid'];
            }
        }
        if ($attr['username']) {
            $where['username'] = $attr['username'];
        }
        if ($attr['username_like']) {
            $where['_string'] = "position('{$attr['username_like']}' IN `username`)";
        }
        if ($attr['role_id']) {
            if (is_array($attr['role_id'])) {
                $where['role_id'] = array('in', $attr['role_id']);
            } else {
                $where['role_id'] = $attr['role_id'];
            }
        }

        try {
            $list = $this->field($field)->where($where)->select();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryAllSysUserByAttr] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;
        return $ret;
    }

    /**
     * 添加用户
     * @author Carter
     */
    public function insertSysUser($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        // 检查同名
        try {
            $user = $this->field('uid')->where(array('username' => $attr['username']))->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertSysUser] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if (!empty($user)) {
            $ret['code'] = ERRCODE_DATA_OVERLAP;
            $ret['msg'] = '用户名已存在';
            return $ret;
        }

        $insertData = array(
            'role_id' => $attr['role_id'],
            'username' => $attr['username'],
            'realname' => $attr['realname'],
            'password' => password_hash($attr['password'], PASSWORD_BCRYPT),
            'status' => self::STATUS_NORMAL,
        );

        try {
            $uid = $this->add($insertData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertSysUser] insert failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_ADD_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = array(
            'uid' => $uid,
            'insert' => $insertData,
        );
        return $ret;
    }

    /**
     * 修改本用户信息
     * @author Carter
     */
    public function updateSysUserInfo($uid, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        if ($attr['password']) {
            $attr['password'] = password_hash($attr['password'], PASSWORD_BCRYPT);
        } else {
            unset($attr['password']);
        }

        try {
            $userInfo = $this->where(array('uid' => $uid))->find();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateSysUserInfo] select failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        // 先过滤出拥有相同 key 的数组，再获取 value 不同的列
        $intersectArr = array_intersect_key($attr, $userInfo);
        $updateData = array_diff_assoc($intersectArr, $userInfo);
        if ($updateData == array()) {
            $ret['code'] = ERRCODE_UPDATE_NONE;
            $ret['msg'] = '无任何修改';
            return $ret;
        }

        // 检查用户名是否有重复
        if (isset($updateData['username'])) {
            try {
                $valInfo = $this->where(array('username' => $updateData['username']))->find();
            } catch (\Exception $e) {
                set_exception(__FILE__, __LINE__, "[updateSysUserInfo] select failed. ".$e->getMessage());
                $ret['code'] = ERRCODE_DB_SELECT_ERR;
                $ret['msg'] = $e->getMessage();
                return $ret;
            }
            if (!empty($valInfo)) {
                $ret['code'] = ERRCODE_DATA_OVERLAP;
                $ret['msg'] = "用户名 {$updateData['username']} 已存在，请更换后再提交";
                return $ret;
            }
        }

        try {
            $this->where(array('uid' => $uid))->save($updateData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateSysUserInfo] update failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        // 将修改内容返回记录
        $ret['data'] = $updateData;

        return $ret;
    }

    /**
     * 修改用户为已删除状态，系统不作物理删除
     * @author Carter
     */
    public function updateSysToDeleteStatus($uid)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $updateData = array(
            // 已删除的用户不能绑定任何角色
            'role_id' => 0,

            // 清 remember_token、timeout
            'remember_token' => '',
            'timeout' => 0,

            // 状态置为已删除
            'status' => self::STATUS_DELETE,
        );

        try {
            $this->where(array('uid' => $uid))->save($updateData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateSysToDeleteStatus] update failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        return $ret;
    }

    /**
     * 修改用户为正常状态，且绑定角色
     * @author Carter
     */
    public function updateSysToNormalStatus($uid, $roleId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $updateData = array(
            // 绑定角色
            'role_id' => $roleId,
            // 状态置为正常
            'status' => self::STATUS_NORMAL,
        );

        try {
            $this->where(array('uid' => $uid))->save($updateData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateSysToNormalStatus] update failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $updateData;

        return $ret;
    }
}
