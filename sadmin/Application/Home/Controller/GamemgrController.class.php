<?php
namespace Home\Controller;

use Common\Service\ApiService;
use Common\Service\DbLoadConfigService;
use Common\Service\ValidatorService;
use Home\Logic\GamemgrLogic;
use Home\Model\GameLogDev\URoomActionLogModel;
use Home\Model\GameMailModel;
use Home\Model\GameModel;
use Home\Model\SysUserModel;
use Home\Model\GameChannelModel;

class GamemgrController extends BaseController
{
    private $mailnotice_reward_map = array(
        0 => '未选择',
        10008 => '钻石',
        //10009=>'元宝',
    );

    public function __construct()
    {
        parent::__construct();

        $this->assignBaseData();
    }

    /************************************ 日志查询 ************************************/

    /**
     * 日志查询
     */
    public function log()
    {
        $viewAssign = array();

        // 页面 title
        $viewAssign['title'] = "游戏管理 | 日志查询";

        $third = I('get.third');
        if (empty($third)) {
            $third = 'round';
        }
        switch ($third) {
            // 牌局日志
            case 'round':
                $this->_logRound($viewAssign);
                $displayPage = "logRound";
                break;
            // 牌局日志
            case 'showroundlog':
                $this->_showRoundLog($viewAssign);
                $displayPage = "showRoundLog";
                break;
            // 房间日志
            case 'room':
                $this->_logRoom($viewAssign);
                $displayPage = "logRoom";
                break;
            // 未知三级目录
            default:
                redirect('/Auth/logout');
        }

        $this->assign($viewAssign);
        $this->display($displayPage);
    }

    /**
     * 日志查询 - 牌局日志
     */
    private function _logRound(&$viewAssign)
    {
        $attr = array();

        $recodeLogic = new GamemgrLogic();

        $getStartTime = strtotime( I('get.start')); // 开始时间
        $getEndTime = strtotime(I('get.end')); // 结束时间
        if ($getStartTime) {
            $startTime =  $getStartTime ? date('Y-m-d H:i:s', $getStartTime) : date('Y-m-d H:i:s', time());
            $endTime = $getEndTime ? date('Y-m-d H:i:s', $getEndTime) : date('Y-m-d H:i:s', time());
            $viewAssign['start'] = $startTime;
            if ($getEndTime) {
               $viewAssign['end'] = $endTime;
            }

            $attr['starttime'] = $startTime;
            $attr['endtime'] = $endTime;
        }

        // 房间ID
        $attr['roomid'] = $roomid = I('get.roomid', '', 'trim');
        $viewAssign['roomid'] = $roomid;

        // 用户ID
        $attr['userid'] = $userid = I('get.userid', '', 'trim');
        $viewAssign['userid'] = $userid;

        // 参与人数
        $attr['usercount'] = $usercount = I('get.usercount', '', 'intval');
        $viewAssign['usercount'] = $usercount;

        // 房间类型
        $attr['roomtype'] = $roomtype =  I('get.roomtype', '', 'intval');
        $viewAssign['roomtype'] = $roomtype;

        // 是否正常结束
        $attr['isnormal'] = $isnormal =  I('get.isnormal', '', 'intval');
        $viewAssign['isnormal'] = $isnormal;

        $lgcRet = $recodeLogic->getPyjRecodeListLogic($attr);
        if ($lgcRet['code'] !== ERRCODE_SUCCESS) {
            $viewAssign['errMsg'] = $lgcRet['msg'] ? $lgcRet['msg'] : "查询数据异常";
        } else {
            $viewAssign['list'] = $lgcRet ['data']['list'];
            $viewAssign['pagination'] = $lgcRet ['data']['pagination'];
        }
    }

    /**
     * 查看日志信息
     */
    public function _showRoundLog(&$viewAssign){
        $id = I('id');

        //id
        if($id){
            $attr['id'] = $id ;
        }


        $recodeLogic = new GamemgrLogic();
        $result = $recodeLogic -> getPyjUserRecodeListLogic($attr);
        if($result ['code'] != ERRCODE_SUCCESS ){
            $viewAssign['errMsg'] = $result['msg'] ?  $result['msg'] : "查询数据异常" ;
        }else{
            $roomInfoResult =  $result['data'];

            $viewAssign['list'] = $roomInfoResult['list'];
            $viewAssign['gameCount'] = count($roomInfoResult['list']);
            $playBackNames = str_replace(array('[',']',' '), '', $roomInfoResult['roominfo']['playBackNames']) ;
            $roomInfoResult['roominfo']['playBackNames'] = explode(',', $playBackNames) ;
            $viewAssign['roominfo']  = $roomInfoResult['roominfo'];
        }

        $mod = new GameModel();
        $modRet = $mod->queryGameInfoById(C("G_USER.gameid"));
        if ($modRet['code'] == ERRCODE_SUCCESS) {
           $gameinfo = $modRet['data'];
            if($gameinfo['resource_ip']){
                $viewAssign['resource_url'] = $gameinfo['resource_port'] ?  "http://{$gameinfo['resource_ip']}:{$gameinfo['resource_port']}/pyjrecord/" : "http://{$gameinfo['resource_ip']}/pyjrecord/" ;
            }
        }

        $this->assign($viewAssign);
    }

    /**
     * 日志查询 - 房间日志
     * @author Carter
     */
    private function _logRoom(&$viewAssign)
    {
        $vldSer = new ValidatorService();
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'CONF_DBTYPE_GAME_LOG_DEV', 0)) {
            try {
                $logMod = new URoomActionLogModel();
            } catch (\Exception $e) {
                // --------------- 房间牌局后端未发版兼容 start ---------------
                // 用于获取 mysql errcode，如果为 1146 表示表不存在，表不存在表示后端未发布房间日志功能
                $eErrMsg = $e->getMessage();
                if ('1146' == current(explode(':', $eErrMsg, 2))) {
                    $viewAssign['unPubFlag'] = 1;
                }
                // --------------- 房间牌局后端未发版兼容 end ---------------

                $viewAssign['errMsg'] = "数据库连接失败，错误信息：".$e->getMessage();
            }
        } else {
            $viewAssign['errMsg'] = "数据库配置加载失败，请确认数据库配置信息";
        }

        // 查询参数
        $attr = I('get.', '', 'trim');
        $rules = array(
            array('userid', 1, array(
                array('integer', null, '玩家id需要是一个整数'),
            )),
            array('roomid', 1, array(
                array('integer', null, '房间号码需要是一个整数'),
            )),
            array('start', 1, array(
                array('date', null, '开始日期时间格式有误'),
            )),
            array('end', 1, array(
                array('date', null, '结束日期时间格式有误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $viewAssign['errMsg'] = $vRet;
        }
        $viewAssign['query'] = json_encode($attr);

        if (is_null($viewAssign['errMsg'])) {
            $viewAssign['actionTypeMap'] = $logMod->actionTypeMap;

            $modRet = $logMod->queryGameRoomActionLogByAttr($attr);
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $viewAssign['errMsg'] = $modRet['msg'];
            } else {
                $viewAssign['list'] = $modRet['data']['list'];
                $viewAssign['pagination'] = $modRet['data']['pagination'];
            }
        }
    }

    /************************************ 玩家管理 ************************************/

    // 用户信息
    public function ajaxQueryUser() {
        $this->checkIsAjax();
        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => ''
        );

        $gamelogic = new GamemgrLogic();
        $modRet = $gamelogic->getUserDataById(I('POST.'));
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $retData['code'] = ERRCODE_DATA_ERR;
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }
        $retData['data'] = $modRet['data'];

        $this->ajaxReturn($retData);
    }
    /**
     * 玩家管理
     */
    public function user()
    {
        $viewAssign = array();

        // 页面 title
        $viewAssign['title'] = "游戏管理 | 玩家管理";

        $vldSer = new ValidatorService();

        $third = I('get.third');
        if (empty($third)) {
            $third = 'info';
        }
        switch ($third) {
            // 用户信息
            case 'info':
                $this->_userInfo($viewAssign);
                $displayPage = "userInfo";
                break;
            // 解散房间
            case 'terminal':
                $this->_userTerminal($viewAssign);
                $displayPage = "userTerminal";
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
     * 赠送元宝
     * @author daniel
     */
    public function ajaxGiveGold()
    {
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_MN_GUSER_GIVE_YUANBAO, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $vldSer = new ValidatorService();
        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => ''
        );

        // 查询参数
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('gold', 0, array(
                array('require', null, '赠送元宝数量缺失'),
                array('integer', null, '赠送元宝数量必须为整数'),
                array('not_in', '0', '赠送元宝数量不能为0')
            )),
            array('userId', 0, array(
                array('require', null, '玩家id缺失'),
                array('numeric', null, '玩家id必须为整数'),
            ))
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_DATA_ERR;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        // 调用服务端接口，发放元宝
        $apiSer = new ApiService();
        $gameId = C('G_USER.gameid');
        $goldItem = 10009;
        $serRet = $apiSer->dsSvrApiGetQuery($gameId, '/console/?act=addprop&id=' . $attr['userId'] . '&item=' . $goldItem . '&sum=' . $attr['gold']);
        if (ERRCODE_SUCCESS !== $serRet['code']) {
            $retData['code'] = ERRCODE_DATA_ERR;
            $retData['msg'] = '发放元宝失败';
            $this->ajaxReturn($retData);
        }
        // 记录操作流水
        $record = [
            '操作人' => C("G_USER.username"),
            '赠送用户' => $attr['userId'],
            '赠送元宝数量' => $attr['gold']
        ];
        set_operation("赠送元宝", $record);
        $this->ajaxReturn($retData);
    }

    /**
     * 玩家管理 - 用户信息
     */
    private function _userInfo(&$viewAssign)
    {
        // EKEY_USERINFO 兼容
        // --------------start---------
        if (true != is_function_enable("EKEY_USERINFO")) {
            $viewAssign['errMsg'] = "本游戏未兼容此功能， 更新至最新版本后才支持配置开启此功能";
        }
        // --------------end-----------
    }

    /**
     * 查询房间信息
     * @author liyao
     */
    private function _apiGetRoomInfo($roomId, &$viewAssign)
    {
        $apiSer = new ApiService();
        $gameId = C('G_USER.gameid');
        // 调用服务端接口，查询房间信息
        $serRet = $apiSer->dsSvrApiGetQuery($gameId, '/console/?act=roominfo&roomid='.$roomId);
        if (ERRCODE_SUCCESS !== $serRet['code']) {
            $viewAssign['errMsg'] = $serRet['msg'];
            return false;
        }
        $info = json_decode($serRet["data"]);
        if (isset($info, $info->result) && $info->result == 0) {
            return $info;
        } else {
            $viewAssign['errMsg'] = "未查询到房间号信息";
            return false;
        }
    }

    /**
     * 查询玩家信息
     * @author liyao
     */
    private function _apiGetUserInfo($userId, &$viewAssign)
    {
        $apiSer = new ApiService();
        $gameId = C('G_USER.gameid');
        // 调用服务端接口，查询房间信息
        $serRet = $apiSer->dsSvrApiGetQuery($gameId, '/api/room/?act=getRoomByUserId&userId='.$userId);
        if (ERRCODE_SUCCESS !== $serRet['code']) {
            $viewAssign['errMsg'] = $serRet['msg'];
            return false;
        }
        $info = json_decode($serRet["data"]);
        if (isset($info, $info->privateRoomIdInDB, $info->privateRoomInMemory) &&
            (!empty($info->privateRoomIdInDB) || !empty($info->privateRoomInMemory))) {
            return $info;
        } else {
            $viewAssign['errMsg'] = "未查询到该用户的房间信息";
            return false;
        }
    }

    /**
     * 解散房间
     * @author liyao
     */
    public function ajaxRemoveRoom() {
        $this->checkIsAjax();
        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => ''
        );
        // 操作权限校验
        if (!in_array(AUTH_OPER_MN_GUSER_TERMINAL, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }
        $roomId = I('POST.roomid');
        $apiSer = new ApiService();
        $gameId = C('G_USER.gameid');
        // 调用服务端接口，查询房间信息
        $serRet = $apiSer->dsSvrApiGetQuery($gameId, '/console/?act=roomoff&roomid='.$roomId);
        if (ERRCODE_SUCCESS !== $serRet['code']) {
            $retData['code'] = ERRCODE_DATA_ERR;
            $retData['msg'] = $serRet['msg'];
            $this->ajaxReturn($retData);
            return;
        }
        $info = json_decode($serRet["data"]);
        if (isset($info, $info->result) && $info->result == 0) {
            set_operation("解散房间", I("POST."));
            $this->ajaxReturn($retData);
        } else {
            $retData['code'] = ERRCODE_DATA_ERR;
            $retData['msg'] = "解散房间失败，请确认房间号是否存在";
            $this->ajaxReturn($retData);
            return;
        }
    }

    /**
     * 重置玩家房间信息
     * @author liyao
     */
    public function ajaxResetUser() {
        $this->checkIsAjax();
        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => ''
        );

        $vldSer = new ValidatorService();
        $apiSer = new ApiService();
        // 操作权限校验
        if (!in_array(AUTH_OPER_MN_GUSER_TERMINAL, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }
        $userId = I('POST.userId');

        // 校验输入
        $attr = array("userId" => $userId);
        $rules = array(
            array('userId', 0, array(
                array('numeric', null, '玩家id必须是数字'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_DATA_ERR;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
            return;
        }
        $gameId = G('G_USER.gameid');
        // 调用服务端接口，查询房间信息
        $serRet = $apiSer->dsSvrApiGetQuery($gameId, '/api/user/?act=resetUserRoom&userId='.$userId.'&force=false');
        if (ERRCODE_SUCCESS !== $serRet['code']) {
            $retData['code'] = ERRCODE_DATA_ERR;
            $retData['msg'] = $serRet['msg'];
            $this->ajaxReturn($retData);
            return;
        }
        $info = json_decode($serRet["data"]);
        if (isset($info, $info->result) && $info->result == 0) {
            set_operation("重置玩家房间信息", I("POST."));
            $this->ajaxReturn($retData);
        } else {
            $retData['code'] = ERRCODE_DATA_ERR;
            $retData['msg'] = "重置玩家信息失败，请确认玩家ID";
            $this->ajaxReturn($retData);
            return;
        }
    }

    /**
     * 玩家管理 - 解散房间
     */
    private function _userTerminal(&$viewAssign)
    {
        $vldSer = new ValidatorService();

        // EKEY_TERMINAL 兼容
        // --------start---------
        if (true !== is_function_enable("EKEY_TERMINAL")) {
            $viewAssign['compatibleMsg'] = "本游戏未兼容此功能， 更新至最新版本后才支持配置开启此功能";
        }
        // ---------end----------

        // 校验输入
        $attr = I('get.', '', 'trim');
        $rules = array(
            array('userId', 1, array(
                array('numeric', null, '玩家id必须是数字'),
            )),
            array('roomId', 1, array(
                array('numeric', null, '房间号必须是数字'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $viewAssign['errMsg'] = $vRet;
            return;
        }

        $viewAssign["dbList"] = $viewAssign["memList"] = $viewAssign["roomInfo"] = "";
        // 根据玩家id得到房间信息
        if (isset($attr["btnUserId"])) {
            $viewAssign["userId"] = $attr["userId"];
            $userInfo = $this->_apiGetUserInfo($attr["userId"], $viewAssign);
            if (!$userInfo) {
                return;
            }
            // 数据库中房间信息
            $arr = array();
            foreach ($userInfo->privateRoomIdInDB as $v) {
                $users = explode("&", $v->playerInfo);
                $uinfo = array();
                foreach ($users as $vv) {
                    $sep = explode("|", $vv);
                    $sep2 = explode(",", $sep[1]);
                    if ($sep[0] != $v->ownerId) {
                        $uinfo[] = array("userId" => $sep[0], "enterTime" => $sep2[2]);
                    } else {
                        $ownerTime = $sep2[2];
                    }
                }
                $time = explode(".", $v->createTime);
                $arr[] = array("roomId" => $v->roomId, "ownerId" => $v->ownerId, "ownerEnterTime" => $ownerTime, "createTime" =>$time[0], "userInfo" => $uinfo);
            }
            $viewAssign["dbList"] = $arr;
            // 内存中房间信息
            $arr = array();
            foreach ($userInfo->privateRoomInMemory as $v) {
                $config = $v->roomData->privateConfig;
                $uinfo = array();
                $users = explode("&", $config->playerInfo);
                foreach ($users as $vv) {
                    $sep = explode("|", $vv);
                    $sep2 = explode(",", $sep[1]);
                    if ($sep[0] != $v->ownerId) {
                        $uinfo[] = array("userId" => $sep[0], "enterTime" => $sep2[2]);
                    } else {
                        $ownerTime = $sep2[2];
                    }
                }
                $arr[] = array(
                    "roomId" => $config->roomId,
                    "ownerId" => $config->ownerId,
                    "ownerEnterTime" => $ownerTime,
                    "createTime" =>$config->createTime,
                    "roundSum" => $config->roundSum,
                    "nowRound" => intval($config->nowRound + 1),
                    "wanfa" =>str_replace("|", "<br/>", $config->wanfa),
                    "userInfo" => $uinfo
                );
            }
            $viewAssign["memList"] = $arr;
        }
        // 根据房间号得到房间信息
        if (isset($attr["btnRoomId"])) {
            $viewAssign["roomId"] = $attr["roomId"];
            $roomInfo = $this->_apiGetRoomInfo($attr['roomId'], $viewAssign);
            if (!$roomInfo) {
                return;
            }
            $roomInfo = $roomInfo->roominfo;
            $arr = array();
            $arr["roomId"] = $roomInfo->room_id;
            $arr["roundSum"] = $roomInfo->roundSum;
            $arr["nowRound"] = $roomInfo->nowRound + 1;
            $arr["ownerId"] = $roomInfo->ownerid;
            $arr["owner"] = $roomInfo->owner;
            $arr["createTime"] = date("Y-m-d H:i:s", intval($roomInfo->create_time / 1000));
            $tmp = get_object_vars($roomInfo);
            $idx = 1;
            $uinfo = array();
            while (isset($tmp, $tmp["player".$idx."id"])) {
                if ($tmp["player".$idx."id"] != $roomInfo->ownerid) {
                    $uinfo[] = array("userId" => $tmp["player".$idx."id"], "nick" => $tmp["player".$idx]);
                }
                $idx++;
            }
            $arr["userInfo"] = $uinfo;
            $viewAssign["roomInfo"] = $arr;
        }

        // 检查是否有解散房间权限
        $viewAssign['disbandPower'] = in_array(AUTH_OPER_MN_GUSER_TERMINAL, C("G_USER.operate")) ? 1 : 0;
    }

    /************************************ 用户通知 ************************************/


    // 发送邮件
    public function ajaxSendTimerMail() {
        $this->checkIsAjax();
        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => ''
        );
        // 操作权限校验
        if (!in_array(AUTH_OPER_MN_MAILNOTICE_SEND, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }
        $model = new GameMailModel();
        $modRet = $model->addMailTimer(I('POST.'));
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $retData['code'] = ERRCODE_DB_UPDATE_ERR;
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }
        set_operation("设置邮件", I("POST."));
        $this->ajaxReturn($retData);
    }

    // 审核邮件
    public function ajaxVerifyMail() {
        $this->checkIsAjax();
        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => ''
        );
        // 操作权限校验
        if (!in_array(AUTH_OPER_MN_MAILNOTICE_VERIFY, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }
        $model = new GameMailModel();
        $modRet = $model->updateMailStatus(I('POST.id'), I('POST.flag'));
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $retData['code'] = ERRCODE_DB_UPDATE_ERR;
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }
        set_operation("审核邮件", I("POST."));
        $this->ajaxReturn($retData);
    }

    // 取消发送邮件
    public function ajaxDelTimerMail() {
        $this->checkIsAjax();
        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => ''
        );
        // 操作权限校验
        if (!in_array(AUTH_OPER_MN_MAILNOTICE_DEL, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }
        $model = new GameMailModel();
        $modRet = $model->updateMailStatus(I('POST.id'), 3);
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $retData['code'] = ERRCODE_DB_DELETE_ERR;
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }
        set_operation("取消发送邮件", I("POST."));
        $this->ajaxReturn($retData);
    }

    /**
     * 点击显示邮件详细信息
     * @author liyao
     */
    public function iframeGetMailInfo($id)
    {
        $viewAssign = array();

        $modChannel = new GameChannelModel();
        $mailMod = new GameMailModel();
        $userMod = new SysUserModel();

        // 渠道映射
        $viewAssign['codeTypeMap'] = array();
        $modRet2 = $modChannel->getChannelCode();
        if (ERRCODE_SUCCESS !== $modRet2['code']) {
            $viewAssign['errMsg'] = $modRet2['msg'];
        } else {
            $list = $modRet2["data"]["list"];
            foreach ($list as $v) {
                $viewAssign['codeTypeMap'][] = array('code' => $v['code'], 'name' => $v['name']);
            }
        }

        if (empty($id)) {
            $viewAssign['errMsg'] = "缺失 id 参数";
        }

        $modRet = $mailMod->queryMailDataById($id);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $d = $this->_parseMailData(array($modRet['data']));
            $viewAssign['info'] = $d[0];
        }

        // 用户名称 map。用户名称做成映射，只查询一次，不要一条记录查一次用户
        $modRet = $userMod->queryAllSysUserByAttr([], "uid,username");
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $userMap = array();
        foreach ($modRet['data'] as $v) {
            $userMap[$v['uid']] = $v['username'];
        }
        $viewAssign["userMap"] = $userMap;

        layout(false);
        $this->assign($viewAssign);
        $this->display();
    }

    // 下载模板
    public function downloadTemplate() {
        header("Content-Disposition: attachment; filename=\"mail.csv\"");
        header("Content-Type: application/octet-stream");
        echo "玩家ID,钻石数量\r\n13354343,10\r\n34312412,20\r\n56586595,20";
    }

    // 批量上传玩家
    public function ajaxBatchUserUpload() {
        $this->checkIsAjax();
        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => ''
        );
        // 操作权限校验
        if (!in_array(AUTH_OPER_MN_MAILNOTICE_ATTREWARD, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '你没有发送补偿物品的权限';
            $this->ajaxReturn($retData);
        }
        // 上传路径，先存放于临时路劲，等待广告创建的时候再上传至图片服务器
        $uploadPath = ROOT_PATH."FileUpload/BatchUser/";
        if (!is_dir($uploadPath)) {
            if (!mkdir($uploadPath, 0777, true)) {
                $retData['code'] = ERRCODE_SYSTEM;
                $retData['msg'] = "mkdir {$uploadPath} failed";
                $this->ajaxReturn($retData);
            }
        }
        // upload 类获取上传文件
        $config = array(
            'maxSize'    => C('IMG_MAX_UPLOAD_SIZE') * 1024 * 1024,
            'rootPath'   => $uploadPath,
            'exts'       => array(),
            'subName'    => false,
            'saveName'   => array('uniqid')
        );
        $upload = new \Think\Upload($config);
        $uploadInfo = $upload->upload();
        if (false === $uploadInfo || empty( $uploadInfo )  ) {
            $retData['code'] = ERRCODE_UPLOAD_FAILED;
            $retData['msg'] = $upload->getError();
            $this->ajaxReturn($retData);
        }

        $uploadData = array_shift($uploadInfo);
        $filePath = $uploadPath.$uploadData['savename'];
        $fp = @fopen($filePath, "r");
        if (!$fp) {
            $retData['code'] = ERRCODE_UPLOAD_FAILED;
            $retData['msg'] = '文件上传失败';
            $this->ajaxReturn($retData);
        }
        $skipOne = true;
        $fmtInvalid = false;
        $fmtError = '';
        $arrUsers = array();
        while (!feof($fp)) {
            $buf = fgets($fp);
            if (empty(trim($buf)))
                continue;
            $arr = explode(",", trim($buf));
            if (count($arr) != 2) {
                $fmtError = '上传文件格式错误';
                $fmtInvalid = true;
                break;
            }
            $uid = trim($arr[0]);
            $val = trim($arr[1]);
            if ($skipOne) {
                if (ord(substr($uid, 0, 1)) == 0xef && ord(substr($uid, 1, 1)) == 0xbb && ord(substr($uid, 2, 1)) == 0xbf) {
                    $uid = substr($uid, 3);
                } else {
                    if (!is_utf8($uid)) {
                        $uid = iconv("gbk", "utf-8", $uid);
                    }
                    if (!is_utf8($val)) {
                        $val = iconv("gbk", "utf-8", $val);
                    }
                }
                if ($uid != "玩家ID" || $val != "钻石数量") {
                    $fmtError = '请使用模板文件';
                    $fmtInvalid = true;
                    break;
                }
                $skipOne = false;
                continue;
            }
            if (!preg_match('/^[0-9]*$/', $uid) || !preg_match('/^[0-9]*$/', $val)) {
                $fmtError = '用户ID和物品数量只能为数字';
                $fmtInvalid = true;
                break;
            }
            if (isset($arrUsers[$uid])) {
                $fmtError = '玩家ID不能重复';
                $fmtInvalid = true;
                break;
            }
            $val = intval($val);
            if ($val == 0) {
                $fmtError = '补偿物品数量不正确';
                $fmtInvalid = true;
                break;
            }
            $arrUsers[$uid] = $val;
        }
        fclose($fp);
        if ($fmtInvalid) {
            $retData['code'] = ERRCODE_DATA_ERR;
            $retData['msg'] = $fmtError;
            $this->ajaxReturn($retData);
        }
        if (count($arrUsers) <= 0) {
            $retData['code'] = ERRCODE_DATA_ERR;
            $retData['msg'] = '没有包含玩家数据';
            $this->ajaxReturn($retData);
        }

        $retData['data'] = array(
            'fileUrl' => "/FileUpload/BatchUser/".$uploadData['savename'],
            'saveName' => $uploadData['savename'],
        );

        $this->ajaxReturn($retData);
    }
    /**
     * 用户通知
     */
    public function notify()
    {
        $viewAssign = array();

        // 页面 title
        $viewAssign['title'] = "游戏管理 | 邮件信息";

        $third = I('get.third');
        if (empty($third)) {
            $third = 'mail';
        }
        switch ($third) {
            // 邮件信息
            case 'mail':
                $this->_notifyMail($viewAssign);
                $displayPage = "notifyMail";
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
     * 用户通知 - 邮件信息
     * 此action过滤条件与ajaxExportMail过滤条件一致，若更改页面过滤选项，需更改以上两个action的过滤条件
     */
    private function _notifyMail(&$viewAssign)
    {
        $viewAssign['payTypeMap'] = $this->mailnotice_reward_map;
        $viewAssign['endtime'] = date("Y-m-d 00:00:00", strtotime("+1 month"));
        $viewAssign['mailsendFlag'] = in_array(AUTH_OPER_MN_MAILNOTICE_SEND, C("G_USER.operate")) ? true : null;
        $viewAssign['maildeleteFlag'] = in_array(AUTH_OPER_MN_MAILNOTICE_DEL, C("G_USER.operate")) ? true : null;
        $viewAssign['mailattFlag'] = in_array(AUTH_OPER_MN_MAILNOTICE_ATTREWARD, C("G_USER.operate")) ? true : null;
        $viewAssign['mailverifyFlag'] = in_array(AUTH_OPER_MN_MAILNOTICE_VERIFY, C("G_USER.operate")) ? true : null;
        if (!empty(I('GET.tab'))) {
            $viewAssign['tab'] = I("GET.tab");
        } else {
            $viewAssign['tab'] = '';
        }
        $mailModel = new GameMailModel();
        $userMod = new SysUserModel();

        $where = array('game_id'=>C('G_USER.gameid'));
        $attr = I('request.', '', 'trim');
        if (!empty($attr['attQuery'])) {
            if ($attr['attQuery'] == 1) { // 有补偿
                $where['pay'] = array('neq', '');
            }
            if ($attr['attQuery'] == 2) { // 无补偿
                $where['pay'] = array('eq', '');
            }
        }
        if (!empty($attr['mailStatus'])) {
            if ($attr['mailStatus'] != 0) { // 非全部选择
                if ($attr['mailStatus'] == 5) { // 已发送
                    $where['send_flag'] = $mailModel::MAIL_HAS_SEND;
                } else {
                    $where['mail_status'] = $attr['mailStatus'] - 1;
                    $where['send_flag'] = $mailModel::MAIL_HAS_NOT_SEND;
                }
            }
        }
        if (!empty($attr['userQuery'])) {
            if ($attr['userQuery'] == 1) {  // 全服玩家
                $where['user_type'] = $mailModel::MAIL_ALL_USER;
            } else if ($attr['userQuery'] == 2) {   //指定玩家
                $where['user_type'] = $mailModel::MAIL_APPOINT_USER;
            } else {   // 渠道玩家
                $where['user_type'] = $mailModel::MAIL_CHANNEL_USER;
                $where['channel_code'] = $attr['userQuery'];
            }
        }
        if (!empty($attr['operator'])) {
            $modRet = $userMod->querySysUserByUsername($attr['operator'], 'uid');
            if(ERRCODE_SUCCESS !== $modRet['code']){
                $retData['code'] = $modRet['code'];
                $retData['msg'] = $modRet['msg'];
                $this->ajaxReturn($retData);
            }
            $userInfo = $modRet['data'];
            if ($userInfo) {
                $where["operator_id"] = $userInfo['uid'];
            } else {
                $where["operator_id"] = 0;
            }
            $viewAssign["operator"] = $attr['operator'];
        }
        if (!empty($attr['stime'])) {
            $where["stime"][] = array('egt', strtotime($attr['stime']));
            $viewAssign["stime"] = $attr['stime'];
        }
        if (!empty($attr['etime'])) {
            $where["stime"][] = array('elt', strtotime($attr['etime']));
            $viewAssign["etime"] = $attr['etime'];
        }
        $pageData = $mailModel->queryPageMailData($where);
        if ($pageData['code'] != ERRCODE_SUCCESS) {
            $viewAssign['errMsg'] = $pageData['msg'];
            return;
        }
        $viewAssign['query'] = $attr;
        $viewAssign["pageshow"] = $pageData["data"]["pagination"];
        $viewAssign['senderlist'] = $this->_parseMailData($pageData['data']['list']);
        $modChannel = new GameChannelModel();
        $osRet = $modChannel->getOsType();
        if ($osRet['code'] != ERRCODE_SUCCESS) {
            $viewAssign['errMsg'] = $osRet['msg'];
            return;
        }
        $osType = $osRet['data']['list'];
        $osMap = array();
        foreach ($osType as $v) {
            switch ($v['os']) {
                case '1':
                    $osMap['1'] = 'Android';
                    break;
                case '2':
                    $osMap['2'] = 'IOS';
                    break;
                case '3':
                    $osMap['3'] = 'IOS企业签';
                    break;
            }
        }
        $viewAssign['osTypeMap'] = $osArr = $osMap;
        foreach ($osArr as $k => $v) {
            $viewAssign['codeTypeMap' . $k] = array();
            $channelInfoOnce = $modChannel->getChannelCode(array('os' => $k));
            if (ERRCODE_SUCCESS !== $channelInfoOnce['code']) {
                $viewAssign['errMsg'] = $channelInfoOnce['msg'];
            } else {
                $list = $channelInfoOnce["data"]["list"];
                foreach ($list as $v) {
                    $viewAssign['codeTypeMap' . $k][] = array('code' => $v['code'], 'name' => $v['name']);
                }
            }
        }
        $viewAssign['codeTypeMap'] = array();
        $viewAssign['channelTypeMap'] = array();
        $channelInfo = $modChannel->getChannelCode();
        if (ERRCODE_SUCCESS !== $channelInfo['code']) {
            $viewAssign['errMsg'] = $channelInfo['msg'];
        } else {
            $list = $channelInfo["data"]["list"];
            foreach ($list as $v) {
                $viewAssign['codeTypeMap'][] = array('code' => $v['code'], 'name' => $v['name']);
                $viewAssign['channelTypeMap'][$v['code']] = $v['name'];
            }
        }
        if (count($viewAssign['codeTypeMap']) <= 0) {
            $viewAssign["channel_radio"] = "disabled";
        }
        $enable = is_function_enable("EKEY_NOCHANNEL");
        if (!$enable) {
            $viewAssign["nochannel_users"] = "disabled";
        }
    }

    /**
     * 导出邮件信息
     * @author daniel
     */
    public function iframeExportMail()
    {
        $orderUserPayThing = '钻石'; // 指定玩家赔偿物品
        $mailModel = new GameMailModel();
        $userModel = new SysUserModel();
        $where = ['game_id' => C('G_USER.gameid')];
        $attr = I('post.', '', 'trim');

        if (!empty($attr['attQuery'])) {
            if ($attr['attQuery'] == 1) { // 有补偿
                $where['pay'] = ['neq', ''];
            } elseif ($attr['attrQuery'] == 2) { // 无补偿
                $where['pay'] = ['eq', ''];
            }
        }

        if (!empty($attr['mailStatus'])) {
            if ($attr['mailStatus'] != 0) { // 非全部选择
                if ($attr['mailStatus'] == 5) { // 已发送
                    $where['send_flag'] = $mailModel::MAIL_HAS_SEND;
                } else {
                    $where['mail_status'] = $attr['mailStatus'] - 1;
                    $where['send_flag'] = $mailModel::MAIL_HAS_NOT_SEND;
                }
            }
        }

        if (!empty($attr['userQuery'])) {
            if ($attr['userQuery'] == 1) { // 全服玩家
                $where['user_type'] = $mailModel::MAIL_ALL_USER;
            } elseif ($attr['userQuery'] != 2) { // 指定玩家
                $where['user_type'] = $mailModel::MAIL_APPOINT_USER;
            } else { // 渠道玩家
                $where['user_type'] = $mailModel::MAIL_CHANNEL_USER;
                $where['channel_code'] = $attr['userQuery'];
            }
        }

        if (!empty($attr['operator'])) {
            $userNameRet = $userModel->querySysUserByUsername($attr['operator'], 'uid');
            if (ERRCODE_SUCCESS !== $userNameRet['code']) {
                $retData['code'] = $userNameRet['code'];
                $retData['msg'] = $userNameRet['msg'];
                $this->ajaxReturn($retData);
            }
            $userInfo = $userNameRet['data'];
            if ($userInfo) {
                $where['operator_id'] = $userInfo['uid'];
            } else {
                $where['operator_id'] = 0;
            }
        }

        if (!empty($attr['stime'])) {
            $where['stime'][] = ['egt', strtotime($attr['stime'])];
        }

        if (!empty($attr['etime'])) {
            $where['stime'][] = ['elt', strtotime($attr['etime'])];
        }
        $allMail = $mailModel->queryAllMailData($where);
        if (ERRCODE_SUCCESS !== $allMail['code']) {
            $viewAssign['errMsg'] = $allMail['msg'];
        } else {
            // 整理邮件格式
            $title = ['ID', '标题', '邮件内容', '补偿物品', '发送时间', '失效时间', '状态', '玩家人数', '发送玩家'];
            $exportData = [];
            foreach ($allMail['data']['list'] as $mail) {
                // 邮件状态
                $status = "";
                switch ($mail['mail_status']) {
                    case $mailModel::MAIL_STATUS_UNAUDITED:
                        $status = "待审核";
                        break;
                    case $mailModel::MAIL_STATUS_PASS:
                        $status = "待发送";
                        break;
                    case $mailModel::MAIL_STATUS_UNPASS:
                        $status = "审核拒绝";
                        break;
                    case $mailModel::MAIL_STATUS_CANCEL:
                        $status = "已取消";
                        break;
                    default:
                        if ($mail['send_flag'] == $mailModel::MAIL_HAS_SEND) {
                            $status = "已发送";
                        }
                        break;
                }

                // 邮件发放对象
                if ($mail['user_type'] == $mailModel::MAIL_ALL_USER) {
                    $userNum = "-";
                    $users = "全服玩家";
                } else if ($mail['user_type'] == $mailModel::MAIL_CHANNEL_USER) {
                    $userNum = "-";
                    $users = "渠道玩家";
                } else if ($mail['user_type'] == $mailModel::MAIL_BATCH_USER) {
                    // 具体玩家id
                    $userList = json_decode($mail['users'], true);
                    $userNum = count($userList);
                    $users = '';
                    if (!empty($userList) && is_array($userList)) {
                        $users .= '"';
                        foreach ($userList as $list) {
                            $users .= $list['uid'] . "*" . $list['num'] . $orderUserPayThing . PHP_EOL;
                        }
                        $users .= '"';
                    }
                } else {
                    $userNum = count(explode(',', $mail['users']));
                    $users = '"' . str_replace(',', PHP_EOL, $mail['users']) . '"';
                }

                // 补偿
                if (empty($mail['pay'])) {
                    $payThing = "无";
                } else {
                    $payInfo = explode(":", $mail['pay']);
                    $payThing = $this->mailnotice_reward_map[$payInfo[0]] . "*" . $payInfo[1];
                }

                $tmpData = [
                    $mail['id'],                                    // ID
                    $mail['subj'],                                  // 标题
                    "\"" . $mail['cont'] . "\"",                    // 邮件内容
                    $payThing,                                      // 补偿物品
                    date("Y-m-d H:i:s", $mail['stime']),     // 发送时间
                    date("Y-m-d H:i:s", $mail['etime']),     // 失效时间
                    $status,                                        // 状态
                    $userNum,                                       // 玩家人数
                    $users                                          // 发送玩家
                ];
                $exportData[] = $tmpData;
            }
            $filename = '邮件信息统计_' . $attr['stime'] . '-'. $attr['etime'] . '.csv';
            export_file($filename, $title, $exportData);
        }
    }

    /**
     * 解析邮件数据
     */
    private function _parseMailData($data) {
        $mailModel = new GameMailModel();
        $list = array();
        foreach ($data as $v) {
            $arr = array();
            foreach ($v as $kk=>$vv) {
                if ($kk == 'pay') {
                    $payarr = array();
                    $t = explode("|", $vv);
                    foreach ($t as $tt) {
                        $lt = explode(":", $tt);
                        if (count($lt) == 2) {
                            $payarr[] = $this->mailnotice_reward_map[$lt[0]].'*'.$lt[1];
                        }
                    }
                    $arr[$kk] = implode(',', $payarr);
                } else if ($kk == 'users' && $arr['user_type'] == $mailModel::MAIL_BATCH_USER) {
                    $arr[$kk] = json_decode($vv);
                } else {
                    $arr[$kk] = $vv;
                }
            }
            $list[] = $arr;
        }
        return $list;
    }
}
