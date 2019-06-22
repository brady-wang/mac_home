<?php
namespace Common\Behavior;

/**
 * 用户登陆权限检测
 */
class CheckAuthBehavior
{
    // 行为扩展的执行入口必须是run
    public function run(&$params)
    {
        // 检测用户登陆权限
        $this->checkAuthorized();

        // 记录程序开始时间
        G('begin');
    }

    /**
     * 登陆检测
     * 检测用户是否已经登陆，检测失败的跳转至登陆页面
     * @access private
     * @return bool
     */
    private function checkAuthorized()
    {
        // 权限白名单，免校验控制器
        $whiteList = array(
            'Api',
            'Cli',
            'CronStat',
            'CronTool',
            // 以下定时器待转到正式定时器后，逐一删除
            'CrontabTool',
            'CrontabUser',
        );
        if (in_array(CONTROLLER_NAME, $whiteList)) {
            return true;
        }

        if (CONTROLLER_NAME == "Auth") {
            C('G_ACCESS_MAIN', 1);
            C('G_ACCESS_SUBLEVEL', 0);
            C('G_ACCESS_THIRD', 0);
            return true;
        }

        // 获取 cookie remember_token
        $rememberToken = cookie('remember_token');
        if (empty($rememberToken)) {
            // cookie 数据不存在，重定向到登陆页面
            $this->_loginRedirect();
        }

        // 获取 cookie sadmin_identify: uid
        $uid = think_decrypt(cookie('sadmin_identify'));
        if (false == $uid) {
            // 解密失败，退回登陆页面
            $this->_loginRedirect();
        }

        // 获取用户权限信息
        $actLog = new \Home\Logic\AccountLogic();
        $lgcRet = $actLog->getUserAuthInfoByUid($uid);
        if (ERRCODE_SUCCESS != $lgcRet['code']) {
            // 获取用户权限信息失败
            $this->_loginRedirect();
        }
        $userInfo = $lgcRet['data'];

        // token 必须一致
        if ($userInfo['remember_token'] !== $rememberToken) {
            // ajax 请求，返回 json
            if (IS_AJAX) {
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode(array(
                    'code' => ERRCODE_OPER_UNAUTH,
                    'msg' => '您的登录已失效，请重新登录后再执行操作',
                )));
            }

            // token 有所修改，做强制下线处理
            $this->_loginRedirect(2);
        }

        // 当前游戏地区
        $gameId = cookie('sadmin_gid');
        if (empty($gameId)) {
            $this->_loginRedirect();
        }

        // 获取角色信息
        $roleMod = new \Home\Model\SysRoleModel();
        $roleInfo = $roleMod->querySysRoleById($userInfo['role_id'], 'id,role_name');
        if (ERRCODE_SUCCESS != $roleInfo['code']) {
            // 获取用户角色信息失败
            $this->_loginRedirect();
        }

        // 授权游戏检测
        $gameAuth = session('game_auth');
        $roleId = $userInfo['role_id'] ;
        if (!$gameAuth[$roleId]) { // 权限不存在
            $gameAuthModel = new \Home\Model\SysGameAuthModel();
            $gameAuthList = $gameAuthModel->querySysGameAuthListByAttr(array('role_id' => $roleId));
            if ($gameAuthList['data']) {
                foreach ($gameAuthList['data'] as $auth) {
                    $regionList[] = $auth['game_id'];
                }
            }
            $gameAuth[$roleId] = $regionList;
            session('game_auth', $gameAuth);
        }
        // 无权限的游戏不让查看，重新登录获取新权限
        if (!in_array($gameId, $gameAuth[$roleId])) {
            $this->_loginRedirect();
            exit;
        }

        // 记录用户权限信息到全局变量
        $user = array(
            'uid' => $uid,
            'username' => $userInfo['username'],
            'rolename' => $roleInfo['data']['role_name'],
            'roleid' => $userInfo['role_id'],
            'gameid' => $gameId,
            'access' => $userInfo['access'],
            'operate' => $userInfo['operate'],
        );
        C('G_USER', $user);
        $accessMap = C('C_AUTH_ACCESS_MAP');
        $authMap = C('C_ACCESS_NAV_MAP');

        // 根据 URL PATHINFO 匹配出本次访问所对应的末级访问权限代号
        $thirdParam = I('request.third');
        if (empty($thirdParam)) {
            // 不存在三级权限，仅通过 MCA 就可确定末级代号
            if (isset($accessMap[MODULE_NAME][CONTROLLER_NAME][ACTION_NAME]['code'])) {
                $tailCode = $accessMap[MODULE_NAME][CONTROLLER_NAME][ACTION_NAME]['code'];
            }
        } else {
            // URL 中带 third 参数的话，要通过该参数一同确定末级代号
            if (isset($accessMap[MODULE_NAME][CONTROLLER_NAME][ACTION_NAME]['third'][$thirdParam])) {
                $tailCode = $accessMap[MODULE_NAME][CONTROLLER_NAME][ACTION_NAME]['third'][$thirdParam];
            }
        }
        if (is_null($tailCode)) {
            // 末级代号未定义，那 URL 无法定位出访问权限，退回登录页
            $this->_loginRedirect();
        }

        // 末级代号未授权，跳转到已授权的访问页面
        if (!in_array($tailCode, $user['access'])) {
            $this->_accessRedirect($user['access'], $authMap);
        }

        // 拿到末级代号后，就可以通过权限表追溯出全部三级权限代号
        $mstCode = $this->_getMSTCode($tailCode, $authMap, $userInfo['access']);
        if (false === $mstCode) {
            $this->_loginRedirect();
        }

        // 对 authMap(完整 nav 表)进行过滤，只留下用户拥有访问权限的项
        $nav = array();
        foreach ($authMap as $mCode => $main) {
            // 拥有权限，才可以加到导航表中
            if (in_array($mCode, $userInfo['access'])) {
                $nav[$mCode] = array(
                    'name' => $main['name'],
                    'url' => $main['url'],
                    'icon' => $main['icon'],
                    'sublevel' => array(),
                );
            }
            // 扫描二级目录
            foreach ($main['sublevel'] as $sCode => $sublevel) {
                if (in_array($sCode, $userInfo['access'])) {
                    $nav[$mCode]['sublevel'][$sCode] = array(
                        'name' => $sublevel['name'],
                        'url' => $sublevel['url'],
                        'third' => array(),
                    );
                }
                // 如果存在三级目录，则扫描第三级
                if (!empty($sublevel['third'])) {
                    foreach ($sublevel['third'] as $tCode => $third) {
                        if (in_array($tCode, $userInfo['access'])) {
                            $nav[$mCode]['sublevel'][$sCode]['third'][$tCode] = array(
                                'name' => $third['name'],
                                'url' => $third['url'],
                            );
                        }
                    }
                }
            }
        }

        // 若一级权限不在导航表中，说明该用户未授权访问该一级权限，进行页面重定向
        if (is_null($nav[$mstCode['m']])) {
            // 导航表都是已授权的页面，选第一张页面进行跳转
            $authorized = current($nav);
            redirect($authorized['url']);
        }
        // 二级
        if (is_null($nav[$mstCode['m']]['sublevel'][$mstCode['s']])) {
            $authorized = current($nav[$mstCode['m']]['sublevel']);
            redirect($authorized['url']);
        }
        // 三级
        if ($mstCode['t'] != 0 && is_null($nav[$mstCode['m']]['sublevel'][$mstCode['s']]['third'][$mstCode['t']])) {
            $authorized = current($nav[$mstCode['m']]['sublevel'][$mstCode['s']]['third']);
            redirect($authorized['url']);
        }

        // 记录该用户已授权的导航表
        C('G_NAV_MAP', $nav);

        // 记录三级权限代码
        C('G_ACCESS_MAIN', $mstCode['m']);
        C('G_ACCESS_SUBLEVEL', $mstCode['s']);
        C('G_ACCESS_THIRD', $mstCode['t']);

        return true;
    }

    /**
     * 校验失败，跳回登录页面
     */
    private function _loginRedirect($type = 1)
    {
        // clean cookie
        cookie("sadmin_identify", null);
        cookie("remember_token", null);
        cookie("sadmin_gid", null);
        session('game_auth', null);

        // set request uri for referer
        session('referer', I('server.REQUEST_URI'));

        // redirect
        switch ($type) {
            case 1:
                redirect('/Auth/login');

            case 2:
                // 强制下线
                redirect("/Auth/forceLogout");

            default:
                redirect('/Auth/login');
        }
    }

    /**
     * 跳转到拥有访问权限的页面
     */
    private function _accessRedirect($access, $authMap)
    {
        foreach ($authMap as $mainCode => $main) {
            if (!in_array($mainCode, $access)) {
                continue;
            }
            foreach ($main['sublevel'] as $subCode => $sub) {
                if (!in_array($subCode, $access)) {
                    continue;
                }
                if (isset($sub['third'])) {
                    foreach ($sub['third'] as $thirdCode => $third) {
                        if (!in_array($thirdCode, $access)) {
                            continue;
                        }
                        redirect($third['url']);
                    }
                }
                redirect($sub['url']);
            }
        }
        return false;
    }

    /**
     * 通过末级代号追溯权限表，获取全部一级、二级、三级权限代号
     */
    private function _getMSTCode($tailCode, $authMap, $userAccess)
    {
        foreach ($authMap as $mCode => $main) {
            foreach ($main['sublevel'] as $sCode => $sublevel) {
                // 存在三级权限
                if (isset($sublevel['third'])) {
                    // 如果二级页面等于本权限代码且存在三级页面，那么三级页面中必定存在
                    if ($tailCode == $sCode) {
                        foreach ($sublevel['third'] as $tCode => $third) {
                            // 遍历到第一个拥有权限的代号，作为三级权限代号
                            if (in_array($tCode, $userAccess)) {
                                return array('m' => $mCode, 's' => $sCode, 't' => $tCode);
                            }
                        }
                        // 原则上用户拥有二级权限，而该二级权限下面又存在三级权限的话，那么该用户必然拥有第三级权限
                        return false;
                    } else {
                        foreach ($sublevel['third'] as $tCode => $third) {
                            if ($tailCode == $tCode) {
                                return array('m' => $mCode, 's' => $sCode, 't' => $tCode);
                            }
                        }
                    }
                }
                // 不存在三级权限
                else {
                    if ($tailCode == $sCode) {
                        return array('m' => $mCode, 's' => $sCode, 't' => 0);
                    }
                }
            }
        }
        return false;
    }
}
