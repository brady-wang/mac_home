<?php
namespace Home\Controller;

use Common\Service\ValidatorService;
use Home\Logic\AccountLogic;
use Home\Logic\SystemLogic;
use Home\Model\DatabasesConfModel;
use Home\Model\GameModel;
use Home\Model\SysDbsqlModel;
use Home\Model\SysDbsqlStatementModel;
use Home\Model\SysEditionModel;
use Home\Model\SysErrlogModel;
use Home\Model\SysRoleModel;
use Home\Model\SysUserModel;
use Home\Model\SysApiLogModel;

class SystemController extends BaseController
{
    private $roleData = array();
    public function __construct()
    {
        parent::__construct();

        $this->assignBaseData();
    }

    /************************************ 角色管理 ************************************/

    /**
     * 通过末端权限数组，获得完整权限列表（即将各个子权限的父权限计算出来）
     * @author Carter
     */
    private function _getWholeAccessAuthByLeafAuth($access)
    {
        // todo 有效性校验：检查所有访问权限和操作权限是否系统所定义的权限

        $accessList = array();

        $authMap = C('C_ACCESS_NAV_MAP');

        $authParentMap = array();
        foreach ($authMap as $mCode => $main) {
            foreach ($main['sublevel'] as $sCode => $sub) {
                $authParentMap[$sCode] = array($mCode);
                if (isset($sub['third'])) {
                    foreach ($sub['third'] as $tCode => $third) {
                        $authParentMap[$tCode] = array($mCode, $sCode);
                    }
                }
            }
        }

        foreach ($access as $v) {
            if (is_null($authParentMap[$v])) {
                return false;
            }
            $access = array_merge($access, $authParentMap[$v]);
        }
        $access = array_flip(array_flip($access));

        return $access;
    }

    public function role()
    {
        $viewAssign = array();

        $actLgc = new AccountLogic();

        // 页面 title
        $viewAssign['title'] = "系统管理 | 角色管理";

        // 操作权限
        $oper = C('G_USER.operate');
        $viewAssign['addFlag'] = in_array(AUTH_OPER_SYS_ROLE_ADD, $oper) ? true : null;
        $viewAssign['updateFlag'] = in_array(AUTH_OPER_SYS_ROLE_UPDATE, $oper) ? true : null;
        $viewAssign['deleteFlag'] = in_array(AUTH_OPER_SYS_ROLE_DELETE, $oper) ? true : null;

        // 参数校验
        $attr = I('get.', '', 'trim');
        $viewAssign['query'] = json_encode($attr);

        // 获取角色列表
        $lgcRet = $actLgc->getSysRoleList($attr);
        if (ERRCODE_SUCCESS === $lgcRet['code']) {
            $viewAssign['list'] = $lgcRet['data'];
        }

        // 自通过权限校验至今的时间，可视为程序执行时间，传给页面
        $viewAssign['exceTime'] = G('begin', 'end', 2);

        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * 添加角色
     * @author Carter
     */
    public function addRole()
    {
        $viewAssign = array();

        $gameMod = new GameModel();

        // 页面 title
        $viewAssign['title'] = "系统管理 | 角色管理";

        // 操作权限检验
        if (!in_array(AUTH_OPER_SYS_ROLE_ADD, C('G_USER.operate'))) {
            redirect('/System/role');
        }

        // 访问权限结构
        $authStruct = C('C_ACCESS_NAV_MAP');
        foreach ($authStruct as $mCode => $mainItem) {
            // 一级权限需要横跨的行数
            $mRowspan = 0;

            foreach ($mainItem['sublevel'] as $sCode => $sublevelItem) {
                // 二级权限需要横跨的行数
                if (isset($sublevelItem['third'])) {
                    $sRowspan = count($sublevelItem['third']);
                } else {
                    $sRowspan = 1;
                }
                $authStruct[$mCode]['sublevel'][$sCode]['rowspan'] = $sRowspan;

                $mRowspan += $sRowspan;
            }

            $authStruct[$mCode]['rowspan'] = $mRowspan;
        }
        $viewAssign['authStruct'] = $authStruct;

        // 获得游戏列表
        $gameAttr = array('game_status' => $gameMod::GAME_STATUS_ON);
        $modRet = $gameMod->queryGameAllList($gameAttr, 'game_id,game_name');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $gameInfo = array();
        } else {
            $gameInfo = array_combine(array_column($modRet['data'], 'game_id'), $modRet['data']);
        }
        $viewAssign['gameInfo'] = $gameInfo;

        // 回退路径
        $viewAssign['referer'] = get_referer();

        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * ajax 添加角色
     * @author Carter
     */
    public function ajaxAddRole()
    {
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_SYS_ROLE_ADD, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $actLgc = new AccountLogic();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('role_name', 0, array(
                array('require', null, '未填写角色名称'),
                array('len_max', "32", '角色名称不能超过 32 个字符'),
            )),
            array('access', 0, array(
                array('require', null, '至少要勾选一个访问权限'),
                array('array', null, '访问权限数据结构错误'),
            )),
            array('oper', 1, array(
                array('array', null, '操作权限数据结构错误'),
            )),
            array('gameids', 0, array(
                array('require', null, '至少要勾选一个游戏'),
                array('array', null, '游戏授权数据结构错误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        // 获取完整的访问权限
        $attr['access'] = $this->_getWholeAccessAuthByLeafAuth($attr['access']);
        if (false === $attr['access']) {
            $retData['code'] = ERRCODE_DATA_ERR;
            $retData['msg'] = '数据错误';
            $this->ajaxReturn($retData);
        }

        $lgcRet = $actLgc->addSysRole($attr);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        $result = D('SysGameAuth')->updateRodeGameAuth($lgcRet['role_id'],$attr['gameids'] );
        if($result['code'] != ERRCODE_SUCCESS){
            $retData['code'] = $result['code'];
            $retData['msg'] = $result['msg'];
            $this->ajaxReturn($retData);
        }
        session('game_auth',null); //清理游戏授权
        // 记录操作流水
        set_operation("添加角色 {$lgcRet['data']['id']}", $lgcRet['data']);

        $this->ajaxReturn($retData);
    }

    /**
     * 修改角色
     * @author Carter
     */
    public function editRole()
    {
        $viewAssign = array();

        $viewAssign['title'] = "系统管理 | 角色管理";

        // 操作权限检验
        if (!in_array(AUTH_OPER_SYS_ROLE_UPDATE, C('G_USER.operate'))) {
            redirect('/System/role');
        }

        $actLgc = new AccountLogic();
        $gameMod = new GameModel();

        // 访问权限结构
        $authStruct = C('C_ACCESS_NAV_MAP');
        foreach ($authStruct as $mCode => $mainItem) {
            // 一级权限需要横跨的行数
            $mRowspan = 0;

            foreach ($mainItem['sublevel'] as $sCode => $sublevelItem) {
                // 二级权限需要横跨的行数
                if (isset($sublevelItem['third'])) {
                    $sRowspan = count($sublevelItem['third']);
                } else {
                    $sRowspan = 1;
                }
                $authStruct[$mCode]['sublevel'][$sCode]['rowspan'] = $sRowspan;

                $mRowspan += $sRowspan;
            }

            $authStruct[$mCode]['rowspan'] = $mRowspan;
        }
        $viewAssign['authStruct'] = $authStruct;

        $roleId = I('get.id');
        if (empty($roleId)) {
            redirect('/System/role');
        }

        // 获取角色信息
        $lgcRet = $actLgc->getRoleAuthInfoByRoleId($roleId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            redirect('/System/role');
        }
        $viewAssign['role'] = $lgcRet['data'];

        // 获得游戏列表
        $gameAttr = array('game_status' => $gameMod::GAME_STATUS_ON);
        $modRet = $gameMod->queryGameAllList($gameAttr, 'game_id,game_name');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $gameInfo = array();
        } else {
            $gameInfo = array_combine(array_column($modRet['data'], 'game_id'), $modRet['data']);
        }
        $viewAssign['gameInfo'] = $gameInfo;

        // 获得角色已授权游戏列表
        $authWhere = array(
            'role_id' => $roleId
        );
        $roleAuth = D('SysGameAuth')->querySysGameAuthListByAttr($authWhere);
        if($roleAuth['data']){
            foreach($roleAuth['data'] as $auth){
                $authGame[ $auth['game_id'] ] = $auth['game_id'];
            }
            $viewAssign['authGame'] = $authGame;
        }

        // 回退路径
        $viewAssign['referer'] = get_referer();

        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * ajax 修改角色
     * @author Carter
     */
    public function ajaxEditRole()
    {
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_SYS_ROLE_UPDATE, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $actLgc = new AccountLogic();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('id', 0, array(
                array('require', null, '参数错误'),
                array('integer', null, '参数错误'),
            )),
            array('role_name', 0, array(
                array('require', null, '未填写角色名称'),
                array('len_max', "32", '角色名称不能超过 32 个字符'),
            )),
            array('access', 0, array(
                array('require', null, '至少要勾选一个访问权限'),
                array('array', null, '访问权限数据结构错误'),
            )),
            array('oper', 1, array(
                array('array', null, '操作权限数据结构错误'),
            )),
            array('gameids', 0, array(
                array('require', null, '至少要勾选一个游戏'),
                array('array', null, '游戏授权数据结构错误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        // 获取完整的访问权限
        $attr['access'] = $this->_getWholeAccessAuthByLeafAuth($attr['access']);
        if (false === $attr['access']) {
            $retData['code'] = ERRCODE_DATA_ERR;
            $retData['msg'] = '数据错误';
            $this->ajaxReturn($retData);
        }

        $lgcRet = $actLgc->editSysRole($attr);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        //处理游戏授权逻辑
        $result = D('SysGameAuth')->updateRodeGameAuth($attr['id'],$attr['gameids'] );

        if($result['code'] != ERRCODE_SUCCESS){
            $retData['code'] = $result['code'];
            $retData['msg'] = $result['msg'];
            $this->ajaxReturn($retData);
        }

        session('game_auth',null);//清理游戏授权缓存
        // 记录操作流水
        set_operation("修改角色 {$attr['id']}", $lgcRet['data']);

        $this->ajaxReturn($retData);
    }

    /**
     * ajax 删除角色
     * @author Carter
     */
    public function ajaxDelRole()
    {
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_SYS_ROLE_DELETE, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $actLgc = new AccountLogic();

        // 校验输入
        $attr = I('post.');
        $rules = array(
            array('id', 0, array(
                array('require', null, '参数错误'),
                array('integer', null, '参数错误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $lgcRet = $actLgc->removeSysRoleByRoleId($attr['id']);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("删除角色 {$attr['id']}");

        $this->ajaxReturn($retData);
    }

    /**
     * 查看角色
     * @author tangjie
     */
    public function viewRole()
    {
        $viewAssign = array();

        $viewAssign['title'] = "系统管理 | 角色管理";

        // 操作权限检验
        if (!in_array(AUTH_OPER_SYS_ROLE_UPDATE, C('G_USER.operate'))) {
            redirect('/System/role');
        }

        $actLgc = new AccountLogic();
        $gameMod = new GameModel();

        // 访问权限结构
        $authStruct = C('C_ACCESS_NAV_MAP');
        foreach ($authStruct as $mCode => $mainItem) {
            // 一级权限需要横跨的行数
            $mRowspan = 0;

            foreach ($mainItem['sublevel'] as $sCode => $sublevelItem) {
                // 二级权限需要横跨的行数
                if (isset($sublevelItem['third'])) {
                    $sRowspan = count($sublevelItem['third']);
                } else {
                    $sRowspan = 1;
                }
                $authStruct[$mCode]['sublevel'][$sCode]['rowspan'] = $sRowspan;

                $mRowspan += $sRowspan;
            }

            $authStruct[$mCode]['rowspan'] = $mRowspan;
        }
        $viewAssign['authStruct'] = $authStruct;

        $roleId = I('get.id');
        if (empty($roleId)) {
            redirect('/System/role');
        }

        // 获取角色信息
        $lgcRet = $actLgc->getRoleAuthInfoByRoleId($roleId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            redirect('/System/role');
        }
        $viewAssign['role'] = $lgcRet['data'];

        // 获得游戏列表
        $gameAttr = array('game_status' => $gameMod::GAME_STATUS_ON);
        $modRet = $gameMod->queryGameAllList($gameAttr, 'game_id,game_name');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $gameInfo = array();
        } else {
            $gameInfo = array_combine(array_column($modRet['data'], 'game_id'), $modRet['data']);
        }
        $viewAssign['gameInfo'] = $gameInfo;

        // 获得角色已授权游戏列表
        $authWhere = array(
            'role_id' => $roleId
        );
        $roleAuth = D('SysGameAuth')->querySysGameAuthListByAttr($authWhere);
        if($roleAuth['data']){
            foreach($roleAuth['data'] as $auth){
                $authGame[ $auth['game_id'] ] = $auth['game_id'];
            }
            $viewAssign['authGame'] = $authGame;
        }

        // 回退路径
        $viewAssign['referer'] = get_referer();

        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * 权限查询
     * @author tangjie
     */
    public function showRole()
    {
        $viewAssign = array();

        $rodeId = (int)I('post.roleId');

        $gameMod = new GameModel();

        if (!IS_AJAX) {
            $viewAssign['title'] = "系统管理 | 角色查询";

            $rodeModel = new SysRoleModel();
            $rodeResult = $rodeModel->querySysRoleListByAttr();
            $rode = $rodeResult['data'];

            $userMod = new SysUserModel();
            $rodeUser = array();
            foreach ($rode as $key => $item) {

                $modRet = $userMod->queryAllSysUserByAttr(array('role_id' => $item['id']));

                $rode[$key]['userlist'] = $modRet['data'];

            }
            $viewAssign['rodeList'] = $rode;

            $this->assign($viewAssign);
            $this->display();
        } else {
            $result = array(
                'code' => ERRCODE_SUCCESS,
                'msg'  => '操作成功',
            );

            // 获得角色已授权游戏列表
            $authWhere = array(
                'role_id' => $rodeId
            );
            $roleAuth = D('SysGameAuth')->querySysGameAuthListByAttr($authWhere);
            if ($roleAuth['data']) {
                foreach ($roleAuth['data'] as $auth) {
                    $authGame[$auth['game_id']] = $auth['game_id'];
                }
            }

            // 获得游戏列表
            $gameInfo = array();
            $gameAttr = array('game_status' => $gameMod::GAME_STATUS_ON);
            $modRet = $gameMod->queryGameAllList($gameAttr, 'game_id,game_name');
            if (ERRCODE_SUCCESS === $modRet['code']) {
                foreach ($modRet['data'] as $v) {
                    $gameInfo[$v['game_id']] = $v;
                    if (isset($authGame[$v['game_id']])) {
                        $gameInfo[$v['game_id']]['is_auth'] = true;
                    }
                }
            }
            $viewAssign['gameInfo'] = $gameInfo;

            //获取节点的权限
            $rodeAuth = D('SysAuth')->querySysRoleAuthByRoleId($rodeId);
            $access = $rodeAuth['data']['access'];

            //获取节点信息
            $authStruct = C('C_ACCESS_NAV_MAP');
            //格式化数据
            $this->_formatRoleData($authStruct);
            foreach($this->roleData as $key => $value ){
                $roleData[$key] = $value ;

                if(in_array($value['id'], $access)){
                    $roleData[$key]['open'] = true ;
                    $roleData[$key]['name'] = "<span class='tree-leaf-selected'>".$value['name']."</span>" ;
                }
            }
            $viewAssign['roleData'] = $roleData ;

            $result['data'] = $viewAssign;

            $this->ajaxReturn($result);
        }
    }

    // 处理权限节点为目录树需要的结构。
    private function _formatRoleData($authStruct,$level = 0,$pid = 0)
    {
        foreach ($authStruct as $roleid => $roleinfo) {
            $this->roleData[] = array(
                'id' => $roleid,
                'pId' => $pid,
                'name' => $roleinfo['name']

            );

            // 处理二级操作
            if($roleinfo['sublevel']){
                $this->_formatRoleData( $roleinfo['sublevel'] , $level + 1, $roleid );
            }

            // 处理三级操作
            if($roleinfo['third']){
                $this->_formatRoleData( $roleinfo['third'] , $level + 1, $roleid );
            }
        }

        return $this->roleData ;
    }

    /************************************ 用户管理 ************************************/

    public function user()
    {
        $viewAssign = array();

        $userMod = new SysUserModel();
        $roleMod = new SysRoleModel();

        // 页面 title
        $viewAssign['title'] = "系统管理 | 用户管理";

        // 操作权限
        $oper = C('G_USER.operate');
        $viewAssign['addFlag'] = in_array(AUTH_OPER_SYS_USER_ADD, $oper) ? true : null;
        $viewAssign['updateFlag'] = in_array(AUTH_OPER_SYS_USER_UPDATE, $oper) ? true : null;
        $viewAssign['deleteFlag'] = in_array(AUTH_OPER_SYS_USER_DELETE, $oper) ? true : null;

        // 角色 map
        $modRet = $roleMod->querySysRoleMap();
        if (ERRCODE_SUCCESS === $modRet['code']) {
            $viewAssign['roleMap'] = $modRet['data'];
        }

        // 状态 map
        $viewAssign['statusMap'] = array(
            $userMod::STATUS_NORMAL => array(
                'label' => 'label-success',
                'name' => '正常',
            ),
            $userMod::STATUS_DELETE => array(
                'label' => 'label-default',
                'name' => '已删除',
            ),
        );

        // 参数校验
        $attr = I('get.', '', 'trim');
        if (is_null($attr['status'])) {
            $attr['status'] = $userMod::STATUS_NORMAL;
        }
        $viewAssign['query'] = json_encode($attr);

        // 用户列表
        $field = "uid,role_id,username,realname,status";
        $modRet = $userMod->querySysUserByAttr($attr, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign['list'] = $modRet['data']['list'];
            $viewAssign['pagination'] = $modRet['data']['pagination'];
        }

        // 自通过权限校验至今的时间，可视为程序执行时间，传给页面
        $viewAssign['exceTime'] = G('begin', 'end', 2);

        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * 添加用户
     * @author Carter
     */
    public function ajaxAddUser()
    {
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_SYS_USER_ADD, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $actLgc = new AccountLogic();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            // 角色
            array('role_id', 0, array(
                array('require', null, '参数错误'),
            )),
            // 用户名
            array('username', 0, array(
                array('require', null, '请填写用户名'),
                array('len_max', "32", '用户名长度不能超过 32 个字符'),
                array('alpha_dash', null, '用户名仅允许由字母、数字、横杆、点以及下划线组成'),
            )),
            // 姓名
            array('realname', 0, array(
                array('require', null, '请填写姓名'),
                array('len_max', "32", '用户名长度不能超过 32 个字符'),
            )),
            // 密码
            array('password', 0, array(
                array('require', null, '密码不能为空'),
                array('len_between', "6,24", '密码长度只能在 6 至 24 个字符之间'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $lgcRet = $actLgc->addSysUser($attr);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("添加用户 {$lgcRet['data']['uid']}", $lgcRet['data']);

        $this->ajaxReturn($retData);
    }

    /**
     * 修改用户
     * @author Carter
     */
    public function ajaxEdtUser()
    {
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_SYS_USER_UPDATE, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $actLgc = new AccountLogic();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            // uid
            array('uid', 0, array(
                array('require', null, '参数错误'),
            )),
            // 角色
            array('role_id', 0, array(
                array('require', null, '参数错误'),
            )),
            // 用户名
            array('username', 0, array(
                array('require', null, '请填写用户名'),
                array('len_max', "32", '用户名长度不能超过 32 个字符'),
                array('alpha_dash', null, '用户名仅允许由字母、数字、横杆、点以及下划线组成'),
            )),
            // 姓名
            array('realname', 0, array(
                array('require', null, '请填写用户名'),
                array('len_max', "32", '用户名长度不能超过 32 个字符'),
            )),
            // 密码
            array('password', 1, array(
                array('len_between', "6,24", '密码长度只能在 6 至 24 个字符之间'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $lgcRet = $actLgc->editSysUserByUid($attr['uid'], $attr);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("修改用户 {$attr['uid']}", $lgcRet['data']);

        $this->ajaxReturn($retData);
    }

    /**
     * ajax 移除用户
     * @author Carter
     */
    public function ajaxDelUser()
    {
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_SYS_USER_DELETE, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $actLgc = new AccountLogic();

        // 校验输入
        $attr = I('post.');
        $rules = array(
            array('uid', 0, array(
                array('require', null, '参数错误'),
                array('integer', null, '参数错误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $lgcRet = $actLgc->removeSysUserByUid($attr['uid']);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("移除用户 {$attr['uid']}");

        $this->ajaxReturn($retData);
    }

    /**
     * ajax 恢复用户到正常状态
     * @author Carter
     */
    public function ajaxRecoverUser()
    {
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_SYS_USER_DELETE, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $actLgc = new AccountLogic();

        // 校验输入
        $attr = I('post.');
        $rules = array(
            array('uid', 0, array(
                array('require', null, '参数错误'),
                array('integer', null, '参数错误'),
            )),
            array('role_id', 0, array(
                array('require', null, '请选择一个角色'),
                array('integer', null, '参数错误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $lgcRet = $actLgc->recoverSysUser($attr['uid'], $attr['role_id']);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("恢复用户 {$attr['uid']}", $lgcRet['data']);

        $this->ajaxReturn($retData);
    }

    /************************************ 功能兼容管理 ************************************/

    /**
     * 功能版本兼容
     * @author Carter
     */
    public function edition()
    {
        $viewAssign = array();

        $gameMod = new GameModel();
        $edtMod = new SysEditionModel();

        // 页面 title
        $viewAssign['title'] = "系统管理 | 功能兼容配置";

        $attr = array('game_status' => $gameMod::GAME_STATUS_ON);
        $modRet = $gameMod->queryGameAllList($attr, 'game_id,game_name');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign['gameMap'] = array_combine(
                array_column($modRet['data'], 'game_id'),
                array_column($modRet['data'], 'game_name')
            );
        }

        $modRet = $edtMod->querySysEditionAllList();
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign['list'] = $modRet['data'];
        }

        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * 添加功能版本兼容
     * @author Carter
     */
    public function ajaxAddEdition()
    {
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $gameMod = new GameModel();
        $edtMod = new SysEditionModel();

        $attr = array('game_status' => $gameMod::GAME_STATUS_ON);
        $modRet = $gameMod->queryGameAllList($attr, 'game_id');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }
        $gameArr = array_column($modRet['data'], 'game_id');

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            // 功能标识
            array('edition_key', 0, array(
                array('require', null, '请填写功能标识'),
                array('len_max', "128", '功能标识长度不能超过 128 个字符'),
            )),
            // 功能名称
            array('edition_name', 0, array(
                array('require', null, '请填写功能名称'),
                array('len_max', "512", '功能名称长度不能超过 512 个字符'),
            )),
            // 相关代码
            array('del_desc', 0, array(
                array('require', null, '请填写相关代码'),
                array('len_max', "4096", '相关代码长度不能超过 4096 个字符'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        // 以EKEY_为前缀，全大写下划线格式
        if (!preg_match("/^EKEY_[A-Z_]+$/", $attr['edition_key'])) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = '功能标识必须以EKEY_为前缀，全大写下划线格式';
            $this->ajaxReturn($retData);
        }

        // 判断游戏是否都有效
        if (!isset($attr['game'])) {
            $gameList = array();
        } else {
            if (is_array($attr['game'])) {
                $gameList = $attr['game'];
            } else {
                $gameList = array($attr['game']);
            }
        }
        foreach ($gameList as $k => $v) {
            if (!in_array($v, $gameArr)) {
                unset($gameList[$k]);
            }
        }

        // 插入数据
        $modRet = $edtMod->insertSysEdition($attr, $gameList);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("添加兼容功能项 {$modRet['data']['id']}", $modRet['data']);

        $this->ajaxReturn($retData);
    }

    /**
     * 修改功能版本兼容
     * @author Carter
     */
    public function ajaxEdtEdition()
    {
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $gameMod = new GameModel();
        $edtMod = new SysEditionModel();

        $attr = array('game_status' => $gameMod::GAME_STATUS_ON);
        $modRet = $gameMod->queryGameAllList($attr, 'game_id');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }
        $gameArr = array_column($modRet['data'], 'game_id');

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            // id
            array('id', 0, array(
                array('require', null, '参数缺失'),
                array('integer', null, '无效参数'),
            )),
            // 功能标识
            array('edition_key', 0, array(
                array('exclude', null, '非法参数'),
            )),
            // 功能名称
            array('edition_name', 0, array(
                array('require', null, '请填写功能名称'),
                array('len_max', "512", '功能名称长度不能超过 512 个字符'),
            )),
            // 相关代码
            array('del_desc', 0, array(
                array('require', null, '请填写相关代码'),
                array('len_max', "4096", '相关代码长度不能超过 4096 个字符'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        // 判断游戏是否都有效
        if (!isset($attr['game'])) {
            $gameList = array();
        } else {
            if (is_array($attr['game'])) {
                $gameList = $attr['game'];
            } else {
                $gameList = array($attr['game']);
            }
        }
        foreach ($gameList as $k => $v) {
            if (!in_array($v, $gameArr)) {
                unset($gameList[$k]);
            }
        }
        $attr['game'] = serialize($gameList);

        // 插入数据
        $modRet = $edtMod->updateSysEdition($attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("修改兼容功能项 {$attr['id']}", $modRet['data']);

        $this->ajaxReturn($retData);
    }

    /**
     * 功能版本兼容删除
     * @author Carter
     */
    public function ajaxDelEdition()
    {
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $edtMod = new SysEditionModel();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            // id
            array('id', 0, array(
                array('require', null, '参数缺失'),
                array('integer', null, '无效参数'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $modRet = $edtMod->deleteSysEdition($attr['id']);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("删除兼容功能项 {$attr['id']}", array());

        $this->ajaxReturn($retData);
    }

    /************************************ 数据管理 ************************************/

    public function data()
    {
        $viewAssign = array();

        // 页面 title
        $viewAssign['title'] = "系统管理 | 数据管理";

        $vldSer = new ValidatorService();

        $third = I('get.third');
        if (empty($third)) {
            $third = 'gameconf';
        }
        switch ($third) {
            // 游戏配置
            case 'gameconf':
                $this->_dataGameconf($viewAssign);
                $displayPage = "dataGameconf";
                break;
            // 外库配置
            case 'dbconf':
                $this->_dataDbconf($viewAssign);
                $displayPage = "dataDbconf";
                break;
            // 数据库结构
            case 'dbshow':
                $this->_dataDbshow($viewAssign);
                $displayPage = "dataDbshow";
                break;
            // 数据库修改
            case 'dbalter':
                $this->_dataDbalter($viewAssign);
                $displayPage = "dataDbalter";
                break;
            default:
                // 未知三级目录
                redirect('/Auth/logout');
        }

        // 自通过权限校验至今的时间，可视为程序执行时间，传给页面
        $viewAssign['exceTime'] = G('begin', 'end', 2);

        $this->assign($viewAssign);
        $this->display($displayPage);
    }

    /**
     * 游戏配置
     */
    private function _dataGameconf(&$viewAssign)
    {
        $gameMod = new GameModel();

        // 操作权限
        $oper = C('G_USER.operate');
        $viewAssign['mgrFlag'] = in_array(AUTH_OPER_SYS_DATA_GAMECONF_MGR, $oper) ? true : null;

        // 游戏状态
        $viewAssign['statusMap'] = $gameMod->statusMap;

        // 游戏列表
        $field = 'id,game_id,game_name,game_status,ios_package_name,android_package_name,';
        $field .= 'api_ip,api_port,activity_api,activity_api_port,resource_ip,resource_port';
        $modRet = $gameMod->queryGameAllList(array(), $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign["list"] = $modRet["data"];
        }
    }

    /**
     * 添加游戏配置
     */
    public function ajaxAddGameconf()
    {
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_SYS_DATA_GAMECONF_MGR, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $confMod = new GameModel();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            // 状态
            array('game_status', 0, array(
                array('require', null, '参数错误'),
            )),
            // 产品ID
            array('game_id', 0, array(
                array('require', null, "请填写产品ID"),
                array('len_max', '8', "产品ID不能超过8个字符"),
            )),
            // 名称
            array('game_name', 0, array(
                array('require', null, "请填写游戏名称"),
                array('len_max', '32', "游戏名称不能超过32个字符"),
            )),
            // IOS包名
            array('ios_package_name', 0, array(
                array('require', null, "请填写IOS包名"),
                array('len_max', '64', "IOS包名不能超过64个字符"),
            )),
            // 安卓包名
            array('android_package_name', 0, array(
                array('require', null, "请填写安卓包名"),
                array('len_max', '64', "安卓包名不能超过64个字符"),
            )),
            // 游戏服地址
            array('api_ip', 0, array(
                array('require', null, "请填写游戏服地址"),
                array('len_max', '128', "游戏服地址不能超过128个字符"),
            )),
            // web端口
            array('api_port', 0, array(
                array('require', null, "请填写web端口"),
                array('len_max', '8', "web端口不能超过8个字符"),
            )),
            // 活动服地址
            array('activity_api', 0, array(
                array('require', null, "请填写活动服地址"),
                array('len_max', '128', "活动服地址不能超过128个字符"),
            )),
            // 活动服端口
            array('activity_api_port', 0, array(
                array('require', null, "请填写活动服端口"),
                array('len_max', '8', "活动服端口不能超过8个字符"),
            )),
            // 战绩文件地址
            array('resource_ip', 0, array(
                array('require', null, "请填写战绩文件地址"),
                array('len_max', '128', "战绩文件地址不能超过128个字符"),
            )),
            // 战绩文件端口
            array('resource_port', 0, array(
                array('require', null, "请填写战绩文件端口"),
                array('len_max', '8', "战绩文件端口不能超过8个字符"),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $modRet = $confMod->execCheckGameId(0, $attr['game_id']);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }
        if ($modRet['data']) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = '产品ID不能重复';
            $this->ajaxReturn($retData);
        }

        $modRet = $confMod->insertGameData($attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("添加游戏 {$modRet['data']['id']}", $modRet['data'], AUTH_OPER_SYS_DATA_GAMECONF_MGR);

        $this->ajaxReturn($retData);
    }

    /**
     * 修改游戏配置
     */
    public function ajaxEdtGameconf()
    {
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_SYS_DATA_GAMECONF_MGR, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $confMod = new GameModel();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            // id
            array('id', 0, array(
                array('require', null, '参数错误'),
                array('integer', null, "参数错误"),
            )),
            // 产品ID
            array('game_id', 0, array(
                array('exclude', null, "不允许修改产品ID"),
            )),
            // 状态
            array('game_status', 0, array(
                array('require', null, '参数错误'),
            )),
            // 名称
            array('game_name', 0, array(
                array('require', null, "请填写游戏名称"),
                array('len_max', '32', "游戏名称不能超过32个字符"),
            )),
            // IOS包名
            array('ios_package_name', 0, array(
                array('require', null, "请填写IOS包名"),
                array('len_max', '64', "IOS包名不能超过64个字符"),
            )),
            // 安卓包名
            array('android_package_name', 0, array(
                array('require', null, "请填写安卓包名"),
                array('len_max', '64', "安卓包名不能超过64个字符"),
            )),
            // 游戏服地址
            array('api_ip', 0, array(
                array('require', null, "请填写游戏服地址"),
                array('len_max', '128', "游戏服地址不能超过128个字符"),
            )),
            // web端口
            array('api_port', 0, array(
                array('require', null, "请填写web端口"),
                array('len_max', '8', "web端口不能超过8个字符"),
            )),
            // 活动服地址
            array('activity_api', 0, array(
                array('require', null, "请填写活动服地址"),
                array('len_max', '128', "活动服地址不能超过128个字符"),
            )),
            // 活动服端口
            array('activity_api_port', 0, array(
                array('require', null, "请填写活动服端口"),
                array('len_max', '8', "活动服端口不能超过8个字符"),
            )),
            // 战绩文件地址
            array('resource_ip', 0, array(
                array('require', null, "请填写战绩文件地址"),
                array('len_max', '128', "战绩文件地址不能超过128个字符"),
            )),
            // 战绩文件端口
            array('resource_port', 0, array(
                array('require', null, "请填写战绩文件端口"),
                array('len_max', '8', "战绩文件端口不能超过8个字符"),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $modRet = $confMod->updateGameData($attr['id'], $attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("修改游戏 {$attr['id']}", $modRet['data'], AUTH_OPER_SYS_DATA_GAMECONF_MGR);

        $this->ajaxReturn($retData);
    }

    /**
     * 删除游戏配置
     * @author Carter
     */
    public function ajaxDelGameconf()
    {
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_SYS_DATA_GAMECONF_MGR, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $confMod = new GameModel();
        $sysLgc = new SystemLogic();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('id', 0, array(
                array('require', null, '参数错误'),
                array('integer', null, "参数错误"),
            )),
            array('game_id', 0, array(
                array('require', null, '参数错误'),
                array('integer', null, "参数错误"),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $lgcRet = $sysLgc->removeGameByGameId($attr['game_id']);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("删除产品 id {$attr['id']}, game id {$attr['game_id']}", array(), AUTH_OPER_SYS_DATA_GAMECONF_MGR);

        $this->ajaxReturn($retData);
    }

    /**
     * 外库配置
     * @author Carter
     */
    private function _dataDbconf(&$viewAssign)
    {
        $vldSer = new ValidatorService();
        $confMod = new DatabasesConfModel();
        $gameMod = new GameModel();

        // 操作权限
        $oper = C('G_USER.operate');
        $viewAssign['mgrFlag'] = in_array(AUTH_OPER_SYS_DATA_DBCONF_MGR, $oper) ? true : null;

        // 类型 map
        $viewAssign['dbTypeMap'] = $confMod->typeMap;

        // 获得游戏列表
        $modRet = $gameMod->queryGameAllList(array(), 'game_id,game_name');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $gameMap = array();
        } else {
            $gameMap = array_combine(array_column($modRet['data'], 'game_id'), array_column($modRet['data'], 'game_name'));
        }
        $viewAssign['gameMap'] = $gameMap;

        // 主从 map
        $viewAssign['masterMap'] = array(
            '1' => '主库',
            '0' => '从库',
        );

        // 查询参数
        $attr = I('get.', '', 'trim');
        $rules = array(
            array('game_id', 1, array(
                array('in', implode(",", array_keys($gameMap)), '参数错误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $viewAssign['errMsg'] = $vRet;
        }
        $viewAssign['query'] = json_encode($attr);

        // 外库列表
        $modRet = $confMod->queryDbConfList($attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign["pagination"] = $modRet["data"]["pagination"];
            $viewAssign['list'] = $modRet['data']['list'];
        }
    }

    /**
     * 添加外库配置
     * @author Carter
     */
    public function ajaxAddDbconf()
    {
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_SYS_DATA_DBCONF_MGR, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $confMod = new DatabasesConfModel();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            // ID
            array('game_id', 0, array(
                array('require', null, '必须选择游戏'),
            )),
            // 类型
            array('db_type', 0, array(
                array('require', null, '参数错误'),
            )),
            // 服务器地址
            array('host', 0, array(
                array('require', null, "请填服务器地址"),
                array('len_max', '128', "服务器地址不能超过128个字符"),
            )),
            // 端口
            array('port', 0, array(
                array('require', null, "请填端口"),
                array('len_max', '8', "端口不能超过8个字符"),
            )),
            // 用户名
            array('user', 0, array(
                array('require', null, "请填用户名"),
                array('len_max', '32', "用户名不能超过32个字符"),
            )),
            // 密码
            array('pwd', 0, array(
                array('require', null, "请填密码"),
                array('len_max', '32', "密码不能超过32个字符"),
            )),
            // 库名
            array('db_name', 0, array(
                array('require', null, "请填库名"),
                array('len_max', '64', "库名不能超过64个字符"),
            )),
            // 库编码
            array('charset', 0, array(
                array('require', null, "请填库编码"),
                array('len_max', '16', "库编码不能超过个16字符"),
            )),
            // 库主从关系
            array('is_master', 0, array(
                array('require', null, "参数错误"),
                array('in', '1,0', "参数错误"),
            )),
            // 备注
            array('remark', 1, array(
                array('len_max', '64', "备注不能超过64个字符"),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $modRet = $confMod->insertDbConf($attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("添加外库配置 {$modRet['data']['id']}", $modRet['data'], AUTH_OPER_SYS_DATA_DBCONF_MGR);

        $this->ajaxReturn($retData);
    }

    /**
     * 修改外库配置
     * @author Carter
     */
    public function ajaxEdtDbconf()
    {
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_SYS_DATA_DBCONF_MGR, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $confMod = new DatabasesConfModel();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            // id
            array('id', 0, array(
                array('require', null, '参数错误'),
                array('integer', null, "参数错误"),
            )),
            // ID
            array('game_id', 0, array(
                array('require', null, '必须选择游戏'),
            )),
            // 类型
            array('db_type', 0, array(
                array('require', null, '参数错误'),
            )),
            // 服务器地址
            array('host', 0, array(
                array('require', null, "请填服务器地址"),
                array('len_max', '128', "服务器地址不能超过128个字符"),
            )),
            // 端口
            array('port', 0, array(
                array('require', null, "请填端口"),
                array('len_max', '8', "端口不能超过8个字符"),
            )),
            // 用户名
            array('user', 0, array(
                array('require', null, "请填用户名"),
                array('len_max', '32', "用户名不能超过32个字符"),
            )),
            // 密码
            array('pwd', 0, array(
                array('require', null, "请填密码"),
                array('len_max', '32', "密码不能超过32个字符"),
            )),
            // 库名
            array('db_name', 0, array(
                array('require', null, "请填库名"),
                array('len_max', '64', "库名不能超过64个字符"),
            )),
            // 库编码
            array('charset', 0, array(
                array('require', null, "请填库编码"),
                array('len_max', '16', "库编码不能超过个16字符"),
            )),
            // 库主从关系
            array('is_master', 0, array(
                array('require', null, "参数错误"),
                array('in', '1,0', "参数错误"),
            )),
            // 备注
            array('remark', 1, array(
                array('len_max', '64', "备注不能超过64个字符"),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $modRet = $confMod->updateDbConf($attr['id'], $attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("修改外库配置 {$attr['id']}", $modRet['data'], AUTH_OPER_SYS_DATA_DBCONF_MGR);

        $this->ajaxReturn($retData);
    }

    /**
     * 删除外库配置
     * @author Carter
     */
    public function ajaxDelDbconf()
    {
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_SYS_DATA_DBCONF_MGR, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $confMod = new DatabasesConfModel();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('id', 0, array(
                array('require', null, '参数错误'),
                array('integer', null, "参数错误"),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $modRet = $confMod->deleteDbConf($attr['id']);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("删除外库配置 {$attr['id']}", array(), AUTH_OPER_SYS_DATA_DBCONF_MGR);

        $this->ajaxReturn($retData);
    }

    /**
     * 数据库结构
     * @author Carter
     */
    private function _dataDbshow(&$viewAssign)
    {
        $dbMod = new SysDbsqlModel();

        $modRet = $dbMod->querySysDbTableStruct();
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign['tb'] = $modRet['data'];
        }
    }

    /**
     * 数据库修改
     * @author Carter
     */
    private function _dataDbalter(&$viewAssign)
    {
        $vldSer = new ValidatorService();
        $dbMod = new SysDbsqlModel();
        $stmMod = new SysDbsqlStatementModel();
        $sysLgc = new SystemLogic();

        // 操作权限
        $oper = C('G_USER.operate');
        $viewAssign['checkFlag'] = in_array(AUTH_OPER_SYS_DATA_DBALT_CHECK, $oper) ? true : null;

        $modRet = $dbMod->querySysDbTableStruct();
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign['tableMap'] = $modRet['data'];
        }

        // 状态 map
        $viewAssign['statusMap'] = $dbMod->statusMap;

        // 语句状态 map
        $viewAssign['stmStatusMap'] = $stmMod->statusMap;

        // 参数校验
        $attr = I('get.');
        $rules = array(
            array('start_date', 1, array(
                array('date', null, '开始日期格式有误'),
            )),
            array('end_date', 1, array(
                array('date', null, '结束日期格式有误'),
                array('date_after', empty($attr['start_date']) ? '1970-01-01' : $attr['start_date'], '结束日期不能早于开始日期'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $viewAssign['errMsg'] = $vRet;
        } else {
            $lgcRet = $sysLgc->getSysSqlExceList($attr);
            if (ERRCODE_SUCCESS !== $lgcRet['code']) {
                $viewAssign['errMsg'] = $lgcRet['msg'];
            } else {
                $viewAssign['list'] = $lgcRet['data']['list'];
                $viewAssign['pagination'] = $lgcRet['data']['pagination'];
            }
        }
        $viewAssign['query'] = json_encode($attr);

        $viewAssign['uid'] = C('G_USER.uid');
    }

    /**
     * 数据库修改 - 获取数据库记录详情
     * @author Carter
     */
    public function iframeGetSqlInfo($id)
    {
        $viewAssign = array();

        $sysLgc = new SystemLogic();
        $sqlMod = new SysDbsqlModel();
        $stmMod = new SysDbsqlStatementModel();

        // 父状态映射表
        $viewAssign['statusMap'] = $sqlMod->statusMap;

        // 语句状态映射表
        $viewAssign['stmStatusMap'] = $stmMod->statusMap;

        if (empty($id)) {
            $viewAssign['errMsg'] = "缺失 id 参数";
        }

        $lgcRet = $sysLgc->getSysSqlInfoById($id);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $viewAssign['errMsg'] = $lgcRet['msg'];
        } else {
            $viewAssign['info'] = $lgcRet['data'];
        }

        layout(false);
        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * 数据库修改 - 申请 SQL 语句
     * @author Carter
     */
    public function doApplySqlStatement()
    {
        // ajax 校验
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $sysLgc = new SystemLogic();

        $attr = I('post.', '', 'trim');
        $rules = array(
            array('sql_statement', 0, array(
                array('require', null, '不能提交空语句'),
                array('len_max', '60000', '语句不能超过60000字符'),
            )),
            array('sql_describe', 0, array(
                array('require', null, '申请原因为空'),
                array('len_max', '512', '申请原因不能超过512字符'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $lgcRet = $sysLgc->addSqlApplyStatement($attr);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("申请执行数据库修改语句，id {$lgcRet['data']['id']}", $lgcRet['data']);

        $this->ajaxReturn($retData);
    }

    /**
     * 数据库修改 - 修改 SQL 语句
     * @author Carter
     */
    public function doUpdateSqlStatement()
    {
        // ajax 校验
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $sysLgc = new SystemLogic();

        $attr = I('post.', '', 'trim');
        $rules = array(
            array('sql_id', 0, array(
                array('require', null, 'id参数缺失'),
                array('integer', null, 'id参数格式错误'),
            )),
            array('statement_id', 0, array(
                array('require', null, 'id参数缺失'),
                array('integer', null, 'id参数格式错误'),
            )),
            array('sql_statement', 0, array(
                array('require', null, '语句不能为空'),
                array('len_max', '60000', '语句不能超过60000字符'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        // 修改语句
        $lgcRet = $sysLgc->editSqlStatement($attr);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("修改语句", $attr);

        $this->ajaxReturn($retData);
    }

    /**
     * 数据库修改 - 取消 SQL 语句申请
     * @author Carter
     */
    public function doCancelSqlStatement()
    {
        // ajax 校验
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $sqlMod = new SysDbsqlModel();

        $attr = I('post.', '', 'trim');
        $rules = array(
            array('id', 0, array(
                array('require', null, 'id参数缺失'),
                array('integer', null, 'id参数格式错误'),
            )),
            array('remark', 0, array(
                array('require', null, '必须备注好取消原因'),
                array('len_max', '512', '取消原因不能超过512字符'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $uid = C('G_USER.uid');

        // 修改语句
        $modRet = $sqlMod->exceCancelSysSqlApply($attr['id'], $uid, $attr['remark']);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("取消SQL语句申请", $attr);

        $this->ajaxReturn($retData);
    }

    /**
     * 数据库修改 - 执行 SQL 语句
     * @author Carter
     */
    public function doExecuteSqlStatement()
    {
        // 执行 SQL 语句不作时间限制
        set_time_limit(0);
        ignore_user_abort();

        // ajax 校验
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_SYS_DATA_DBALT_CHECK, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $sysLgc = new SystemLogic();

        $attr = I('post.', '', 'trim');
        $rules = array(
            array('id', 0, array(
                array('require', null, 'id参数缺失'),
                array('integer', null, 'id参数格式错误'),
            )),
            array('remark', 0, array(
                array('require', null, '必须填写备注'),
                array('len_max', '512', '备注不能超过512字符'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        // 执行语句
        $lgcRet = $sysLgc->executeSqlStatement($attr);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("执行语句 {$attr['id']}", $attr, AUTH_OPER_SYS_DATA_DBALT_CHECK);

        $this->ajaxReturn($retData);
    }

    /**
     * 数据库修改 - 驳回 SQL 语句
     * @author Carter
     */
    public function doRejectSqlStatement()
    {
        // ajax 校验
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_SYS_DATA_DBALT_CHECK, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $sqlMod = new SysDbsqlModel();

        $attr = I('post.', '', 'trim');
        $rules = array(
            array('id', 0, array(
                array('require', null, 'id参数缺失'),
                array('integer', null, 'id参数格式错误'),
            )),
            array('remark', 0, array(
                array('require', null, '必须填写备注'),
                array('len_max', '512', '备注不能超过512字符'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        // 执行语句
        $modRet = $sqlMod->execRejectSysSqlApply($attr['id'], $attr['remark']);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("驳回语句 {$attr['id']}", $attr, AUTH_OPER_SYS_DATA_DBALT_CHECK);

        $this->ajaxReturn($retData);
    }

    /************************************ 流水查询 ************************************/

    public function log()
    {
        $viewAssign = array();

        // 页面 title
        $viewAssign['title'] = "系统管理 | 流水查询";

        $third = I('get.third');
        if (empty($third)) {
            $third = 'operation';
        }
        switch ($third) {
            // 操作流水
            case 'operation':
                $this->_logOperation($viewAssign);
                $displayPage = "logOperation";
                break;

            // 系统错误
            case 'error':
                $this->_logError($viewAssign);
                $displayPage = "logError";
                break;

            // 定时器日志
            case 'crontab':
                $this->_logCrontab($viewAssign);
                $displayPage = "logCron";
                break;

            default:
                // 未知三级目录
                redirect('/Auth/logout');
        }

        // 自通过权限校验至今的时间，可视为程序执行时间，传给页面
        $viewAssign['exceTime'] = G('begin', 'end', 2);

        $this->assign($viewAssign);
        $this->display($displayPage);
    }

    /**
     * @author brady
     * @desc  接口流水
     * @time 2018/12/21
     */
    public function logApi()
    {
        $viewAssign = array();

        // 页面 title
        $viewAssign['title'] = "接口流水 | 流水查询";

        $vldSer = new ValidatorService();
        $logMod = new SysApiLogModel();

        $typeMap = $logMod->typeMap ;
        $codeMap = $logMod->typeUrlCodeMap ;

        $typeCodeMap = array();
        foreach ($typeMap as $k => $v) {
            $typeCodeMap[$k] = array(
                'name' => $v,
                'code' => array(),
            );
        }
        foreach ($codeMap as $list) {
            foreach ($list as $v) {
                $typeCodeMap[$v['type']]['code'][$v['code']] = $v['name'];
            }
        }
        $viewAssign['typeCodeMap'] = $typeCodeMap;

        // 验证
        $attr = I('get.', '', 'trim');
        $rules = array(
            array('start_date', 1, array(
                array('date', null, '开始日期格式有误'),
            )),
            array('end_date', 1, array(
                array('date', null, '结束日期格式有误'),
                array('date_after', empty($attr['start_date']) ? '1970-01-01' : $attr['start_date'], '结束日期不能早于开始日期'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $viewAssign['errMsg'] = $vRet;
        }
        $viewAssign['query'] = json_encode($attr);

        $modRet = $logMod->querySysApiLogByAttr($attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign['list'] = $modRet['data']['list'];
            $viewAssign['pagination'] = $modRet['data']['pagination'];
        }

        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * 操作流水
     * @author Carter
     */
    private function _logOperation(&$viewAssign)
    {
        $vldSer = new ValidatorService();
        $sysLgc = new SystemLogic();
        $gameMod = new GameModel();

        // 权限 map
        $authMap = array(
            0 => array('name' => '全部'),
        );
        $operMap = array();
        $navMap = C(C_ACCESS_NAV_MAP);
        foreach ($navMap as $mCode => $main) {
            // 一级权限
            $authMap[$mCode] = array('name' => $main['name']);

            if ($main['sublevel']) {
                $authMap[$mCode]['sublevel'][0] = array('name' => '全部');
                foreach ($main['sublevel'] as $sCode => $sub) {
                    // 二级权限
                    $authMap[$mCode]['sublevel'][$sCode] = array('name' => $sub['name']);

                    if ($sub['third']) {
                        $authMap[$mCode]['sublevel'][$sCode]['third'][0] = '全部';
                        foreach ($sub['third'] as $tCode => $third) {
                            // 三级权限
                            $authMap[$mCode]['sublevel'][$sCode]['third'][$tCode] = $third['name'];

                            // 三级操作权限
                            if (isset($third['oper'])) {
                                foreach ($third['oper'] as $operCode => $operName) {
                                    $operMap[$operCode] = $operName;
                                }
                            }
                        }
                    }

                    // 二级操作权限
                    if (isset($sub['oper'])) {
                        foreach ($sub['oper'] as $operCode => $operName) {
                            $operMap[$operCode] = $operName;
                        }
                    }
                }
            }
        }
        $viewAssign['authMap'] = $authMap;
        $viewAssign['operMap'] = $operMap;

        // 游戏 map
        $modRet = $gameMod->queryGameAllList(array(), 'game_id,game_name');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $gameMap = array();
        } else {
            $gameMap = array_combine(array_column($modRet['data'], 'game_id'), array_column($modRet['data'], 'game_name'));
        }
        $viewAssign['gameMap'] = $gameMap;

        // 查询参数
        $attr = I('get.', '', 'trim');
        $subMap = $authMap[$attr['main_code']]['sublevel'] ? : array();
        $thirdMap = $subMap[$attr['sublevel_code']]['third'] ? : array();
        $rules = array(
            array('uid', 1, array(
                array('integer', null, '操作用户id需要是一个整数'),
            )),
            array('game_id', 1, array(
                array('in', implode(",", array_keys($gameMap)), '参数错误'),
            )),
            array('main_code', 1, array(
                array('in', implode(",", array_keys($authMap)), '参数错误'),
            )),
            array('sublevel_code', 1, array(
                array('in', implode(",", array_keys($subMap)), '参数错误'),
            )),
            array('third_code', 1, array(
                array('in', implode(",", array_keys($thirdMap)), '参数错误'),
            )),
            array('start_date', 1, array(
                array('date', null, '开始时间格式有误'),
            )),
            array('end_date', 1, array(
                array('date', null, '结束时间格式有误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $viewAssign['errMsg'] = $vRet;
        }
        $viewAssign['query'] = json_encode($attr);

        // 流水列表
        $lgcRet = $sysLgc->getSysOperationLogList($attr);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $viewAssign['errMsg'] = $lgcRet['msg'];
        } else {
            $viewAssign['userMap'] = $lgcRet['data']['userMap'];
            $viewAssign['list'] = $lgcRet['data']['list'];
            $viewAssign['pagination'] = $lgcRet['data']['pagination'];
        }
    }

    /**
     * 系统错误
     * @author Carter
     */
    private function _logError(&$viewAssign)
    {
        $logMod = new SysErrlogModel();
        $vldSer = new ValidatorService();

        // 状态 map
        $viewAssign['statusMap'] = array(
            $logMod::STATUS_UNTREATED => array(
                'name' => '未处理',
                'label' => "label-warning",
            ),
            $logMod::STATUS_TREATED => array(
                'name' => '已处理',
                'label' => "label-success",
            ),
            $logMod::STATUS_IGNORE => array(
                'name' => '不处理',
                'label' => "label-default",
            ),
        );

        //验证
        $attr = I('get.', '', 'trim');
        if (is_null($attr['handle_status'])) {
            // 状态默认待处理
            $attr['handle_status'] = $logMod::STATUS_UNTREATED;
        }
        $rules = array(
            array('start_date', 1, array(
                array('date', null, '开始日期格式有误'),
            )),
            array('end_date', 1, array(
                array('date', null, '结束日期格式有误'),
                array('date_after', empty($attr['start_date']) ? '1970-01-01' : $attr['start_date'], '结束日期不能早于开始日期'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $viewAssign['errMsg'] = $vRet;
        }
        $viewAssign['query'] = json_encode($attr);

        $modRet = $logMod->querySysExceptionList($attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign['list'] = $modRet['data']['list'];
            $viewAssign['pagination'] = $modRet['data']['pagination'];
        }
    }

    /**
     * 获取错误流水详情
     * @author Carter
     */
    public function iframeGetErrorInfo($id)
    {
        $viewAssign = array();

        $sysLgc = new SystemLogic();
        $logMod = new SysErrlogModel();

        $viewAssign['statusMap'] = array(
            $logMod::STATUS_UNTREATED => '未处理',
            $logMod::STATUS_TREATED => '已处理',
            $logMod::STATUS_IGNORE => '不处理',
        );

        $lgcRet = $sysLgc->getSysErrorLogInfo($id);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $viewAssign['errMsg'] = $lgcRet['msg'];
        } else {
            $viewAssign['info'] = $lgcRet['data'];
        }

        layout(false);
        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * 获取批量错误流水
     * @author Carter
     */
    public function ajaxGetBatchData()
    {
        // ajax 校验
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $vldSer = new ValidatorService();
        $logMod = new SysErrlogModel();

        $attr = I("post.");
        $rules = array(
            array('id', 0, array(
                array('require', null, '参数错误'),
                array('integer', null, '参数错误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $modRet = $logMod->querySysExceptionBatch($attr['id']);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }
        $retData['data'] = $modRet['data'];

        $this->ajaxReturn($retData);
    }

    /**
     * 修改错误流水状态
     * @author Carter
     */
    public function ajaxEditException()
    {
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $logMod = new SysErrlogModel();

        $attr = I("post.", '', 'trim');
        $rules = array(
            array('id', 0, array(
                array('require', null, '参数错误'),
                array('integer', null, '参数错误'),
            )),
            array('handle_remark', 0, array(
                array('require', null, '请填写备注'),
                array('len_max', 1024, '备注不能超过1024字符'),
            )),
            array('handle_status', 0, array(
                array('require', null, '参数错误'),
                array('in', '2,3', '参数错误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $modRet = $logMod->updateSysException($attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("处理错误 {$attr['id']}", $modRet['data']);

        $this->ajaxReturn($retData);
    }

    /**
     * 批量修改错误流水状态
     * @author Carter
     */
    public function ajaxEditExceptionBatch()
    {
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $logMod = new SysErrlogModel();

        $attr = I("post.");
        $rules = array(
            array('id', 0, array(
                array('require', null, '参数错误'),
                array('integer', null, '参数错误'),
            )),
            array('handle_remark', 0, array(
                array('require', null, '请填写备注'),
                array('len_max', 1024, '备注不能超过1024字符'),
            )),
            array('handle_status', 0, array(
                array('require', null, '参数错误'),
                array('in', '2,3', '参数错误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $modRet = $logMod->updateSysExceptionBatch($attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("批量处理错误：", $modRet['data']);

        $this->ajaxReturn($retData);
    }

    /**
     * 定时器日志
     */
    private function _logCrontab(&$viewAssign)
    {
        $vldSer = new ValidatorService();
        $logMod = D('SysCronlog');

        $viewAssign['cronTypeMap'] = $logMod->cronTypeMap ;
        $viewAssign['retCodeMap'] = $logMod->retCodeMap ;

        // 验证
        $attr = I('get.', '', 'trim');
        $rules = array(
            array('start_date', 1, array(
                array('date', null, '开始日期格式有误'),
            )),
            array('end_date', 1, array(
                array('date', null, '结束日期格式有误'),
                array('date_after', empty($attr['start_date']) ? '1970-01-01' : $attr['start_date'], '结束日期不能早于开始日期'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $viewAssign['errMsg'] = $vRet;
        }
        $viewAssign['query'] = json_encode($attr);

        $modRet = $logMod->querySysCronlogList($attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign['list'] = $modRet['data']['list'];
            $viewAssign['pagination'] = $modRet['data']['pagination'];
        }
    }
}
