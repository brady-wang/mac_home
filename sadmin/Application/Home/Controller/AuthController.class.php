<?php
namespace Home\Controller;

use Common\Service\ValidatorService;
use Home\Logic\AccountLogic;
use Home\Model\SysUserModel;

class AuthController extends BaseController
{
    /****************************** 登录 ******************************/

    /**
     * 登录页面
     * @author Carter
     */
    public function login()
    {
        $viewAssign = array();

        // 页面 title
        $viewAssign['title'] = "史蒂夫后台 | 登录";

        // 登录地区 map
        $viewAssign['regionMap'] = get_region_map();

        // 登录后跳转页面
        $referer = session('referer') == '/' ? "/Stat/realtimeOnline" : session('referer') ;
        $viewAssign['referer'] = $referer  ? $referer : "/Stat/realtimeOnline";

        layout(false);
        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * 登录
     * @author Carter
     */
    public function ajaxLogin()
    {
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $userMod = new SysUserModel();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('username', 0, array(
                array('require', null, "请输入用户名"),
            )),
            array('password', 0, array(
                array('require', null, "请输入密码"),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $modRet = $userMod->querySysUserByUsername($attr['username'], 'role_id');
        if(ERRCODE_SUCCESS !== $modRet['code']){
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }
        $userInfo = $modRet['data'];

        $regionMap = get_region_map($userInfo['role_id']);
        if (!isset($regionMap[$attr['region']])) {
            $retData['code'] = ERRCODE_OPER_UNAUTH ;
            $retData['msg'] = '您没有权限登录该游戏';
            $this->ajaxReturn($retData);
        }

        // 执行用户登陆
        $modRet = $userMod->execUserLogin($attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("用户登陆");

        $this->ajaxReturn($retData);
    }

    /**
     * 退出登录
     * @author Carter
     */
    public function logout()
    {
        cookie('sadmin_gid', null);
        cookie('sadmin_identify', null);
        cookie('remember_token', null);
        redirect('/Auth/login');
    }

    /**
     * 强制登出接口
     * @author Carter
     */
    public function forceLogout()
    {
        $viewAssign = array();

        // 页面 title
        $viewAssign['title'] = "史蒂夫后台 | 登录";

        // 登录地区 map
        $viewAssign['regionMap'] = get_region_map();

        // 登录后跳转页面
        $viewAssign['referer'] = session('?referer') ? session('referer') : "/";

        // 强制下线
        $viewAssign['forceLogoutFlag'] = 1;

        cookie('sadmin_gid', null);
        cookie('sadmin_identify', null);
        cookie('remember_token', null);

        layout(false);
        $this->assign($viewAssign);
        $this->display("login");
    }

    /**
     * 切换游戏地区
     * @author Carter
     */
    public function switchRegion($gameId)
    {
        $regionMap = get_region_map();

        if (is_null($regionMap[$gameId])) {
            redirect('/Auth/logout');
        }

        // 简单处理，不管用户是否记住密码，都对该 cookie 赋予七天有效期
        cookie('sadmin_gid', $gameId, 604800);
        $url = '/';
        $refer = $_SERVER['HTTP_REFERER'];
        $pos = 0;
        if (strpos($refer, 'http://') === 0) {
            $pos = strlen('http://');
        }
        if (strpos($refer, 'https://') === 0) {
            $pos = strlen('https://');
        }
        $pos = strpos($refer, '/', $pos);
        if ($pos !== false) {
            $url = substr($refer, $pos);
        }
        redirect($url);
    }

    /****************************** 设置信息 ******************************/

    /**
     * 用户信息设置
     * @author Carter
     */
    public function userSet()
    {
        $actLgc = new AccountLogic();

        $viewAssign = array();

        // 页面 title
        $viewAssign['title'] = '设置信息 | 修改密码';

        // Auth 不通过 Behavior CheckAuth 作权限验证，也获取不到用户导航栏等信息，这一步需要单独处理
        $lgcRet = $actLgc->getUserAuthInfoAfterVertify();
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            // 未通过校验或获取权限信息失败，退出重登录
            redirect('/Auth/logout');
        }
        $user = $lgcRet['data'];

        // 用户信息
        C('G_USER', array('uid' => $user['uid'], 'username' => $user['username']));

        // 当前游戏地区 id
        $viewAssign['gameId'] = $user['gameId'];

        // 游戏地区 map
        $viewAssign['regionMap'] = get_region_map($user['roleId']);

        // 导航栏
        $viewAssign['nav'] = $user['nav'];

        // 回退路径
        $viewAssign['referer'] = get_referer();

        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * 执行信息修改
     * @author Carter
     */
    public function ajaxUserSet()
    {
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $userMod = new SysUserModel();

        // uid
        $uid = think_decrypt(I('cookie.sadmin_identify'));
        if (false == $uid) {
            // 解密失败
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '遗失用户身份数据';
            $this->ajaxReturn($retData);
        }

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('password', 0, array(
                array('require', 'null', '请填写新密码'),
                array('len_between', "6,24", '密码长度必须控制在6-24位之间'),
            )),
            array('password_confirm', 0, array(
                array('confirmed', "password", '确认密码与新密码不一致'),
            )),
            array('role_id', 1, array(
                array('exclude', null, '存在非法赋值字段'),
            )),
            array('username', 1, array(
                array('exclude', null, '存在非法赋值字段'),
            )),
            array('status', 1, array(
                array('exclude', null, '存在非法赋值字段'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $modRet = $userMod->updateSysUserInfo($uid, $attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 为操作流水接口参数赋值
        C('G_USER.uid', $uid);
        C('G_USER.gameid', 0);
        C('G_ACCESS_MAIN', 1);
        C('G_ACCESS_SUBLEVEL', 0);
        C('G_ACCESS_THIRD', 0);

        // 记录操作流水
        set_operation("设置新密码");

        $this->ajaxReturn($retData);
    }
}
