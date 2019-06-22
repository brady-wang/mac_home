<?php
namespace Home\Logic;

use Common\Service\DbLoadConfigService;
use Home\Model\GameDev\UUserInfoModel;
use Home\Model\GameDev\UUserExtModel;
use Home\Model\GameDev\UUserPointModel;
use Home\Model\GameDev\UUserRecordModel;
use Home\Model\GameLogDev\UPyjRecordLogModel as PyjRecodeModel;
use Home\Model\GameLogDev\PyjUserRecordModel;
use Home\Model\GameLogDev\UPropsLogModel;
use Home\Model\GameLogDev\UUserInfoLogModel;
use Home\Model\GameDev\SPrivateroomInfoModel;
use Home\Model\Club\ClubUserModel;
use Home\Model\DsqpDict\DictPlaceModel;
use Home\Model\StatUserRankModel;

/**
 * 游戏管理逻辑
 * @author tangjie
 */
class GamemgrLogic
{
    /**
     * 根据用户id获取用户数据
     */
    public function getUserDataById($data)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DEV_DB', 0)) {
            try {
                $userMod = new UUserInfoModel();
                $extMod = new UUserExtModel();
                $pointMod = new UUserPointModel();
                $recordMod = new UUserRecordModel();
            } catch (\Exception $e) {
                $ret['code'] = ERRCODE_DB_SELECT_ERR;
                $ret['msg'] = "数据库连接失败，错误信息：".$e->getMessage();
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = "数据库配置加载失败，请确认数据库配置信息";
            return $ret;
        }

        $userid = $data['userid'];
        $uinfo = array();

        $modRet = $userMod->queryDevUserInfoByUid($userid);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $info = $modRet['data'];
        if (!$info) {
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = "未查询到此用户，请检查用户ID";
            return $ret;
        }
        $uinfo["nick"] = $info["nickName"];
        $uinfo["userid"] = $info["userId"];
        $uinfo["createtime"] = $info["createTime"];
        $uinfo["truename"] = $info["compellation"];
        $uinfo["idcard"] = $info["IDcard"];
        $uinfo["phone"] = $info["phoneNum"];
        // 注册地区
        $modRet = $extMod->queryDevUserExtInfoByAttr(array("userId" => $userid), "preferredCity");
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $info = $modRet['data'];
        if ($info) {
            if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DICT_DB', 0)) {
                try {
                    $placeMod = new DictPlaceModel();
                } catch (\Exception $e) {
                    $ret['code'] = ERRCODE_DB_SELECT_ERR;
                    $ret['msg'] = "数据库连接失败，错误信息：".$e->getMessage();
                    return $ret;
                }
            } else {
                $ret['code'] = ERRCODE_DB_SELECT_ERR;
                $ret['msg'] = "数据库配置加载失败，请确认数据库配置信息";
                return $ret;
            }
            $modRet = $placeMod->queryDsqpPlaceByPlaceId($info["preferredCity"], "placeName");
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $ret['code'] = $modRet['code'];
                $ret['msg'] = $modRet['msg'];
                return $ret;
            }
            if (count($modRet["data"]) > 0) {
                $uinfo["region"] = $modRet["data"][0]["placeName"];
            }
        }
        // 剩余钻石、元宝信息
        $modRet = $pointMod->queryDevUserPointInfoByAttr(array("userId" => $userid), "privateRoomDiamond,paper");
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $info = $modRet['data'];
        if ($info) {
            $uinfo["leftdiamod"] = $info["privateRoomDiamond"];
            $uinfo["leftgold"] = $info["paper"];
        }
        // 游戏局数、活跃天数
        $modRet = $recordMod->queryDevUserRecordSumByAttr(array("userId" => $userid), "playAmount");
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        if (count($modRet["data"]) > 0) {
            $uinfo["playcounts"] = intval($modRet["data"][0]["s"]);
        }       
        $modRet = $extMod->queryDevUserExtInfoByAttr(array("userId" => $userid, "gameId" => C('G_USER.gameid')), "activeAmount");
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $info = $modRet['data'];
        if ($info) {
            $uinfo["playdays"] = $info["activeAmount"];
        }
        
        if (true === $confSer->load(C('G_USER.gameid'), 'CONF_DBTYPE_GAME_LOG_DEV', 0)) {
            try {
                $propMod = new UPropsLogModel();
                $logMod = new UUserInfoLogModel();
            } catch (\Exception $e) {
                $ret['code'] = ERRCODE_DB_SELECT_ERR;
                $ret['msg'] = "数据库连接失败，错误信息：".$e->getMessage();
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = "数据库配置加载失败，请确认数据库配置信息";
            return $ret;
        }
        // 消耗的钻石，元宝总数
        $modRet = $propMod->queryPropsCount(array("userId" => $userid, "propsId" => 10008));
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        if (count($modRet["data"]) > 0) {
            $uinfo["totaldiamod"] = intval($modRet["data"][0]["prop"]);
        }
        $modRet = $propMod->queryPropsCount(array("userId" => $userid, "propsId" => 10009));
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        if (count($modRet["data"]) > 0) {
            $uinfo["totalgold"] = intval($modRet["data"][0]["prop"]);
        }
        
        // 最近登录时间
        $modRet = $extMod->queryDevUserExtInfoByAttr(array("userId" => $userid), "lastLoginTime");
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $info = $modRet["data"];
        if ($info) {
            $uinfo["loginrecenttime"] = $info["lastLoginTime"];
        }
        // 最近登陆IP
        $modRet = $logMod->queryDevLogUserInfoRecent(array("userId" => $userid), "loginIP");
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $info = $modRet["data"];
        if ($info) {
            $uinfo["loginrecentip"] = $info["loginIP"];
        }
        
        // 俱乐部信息
        if (true === $confSer->load(C('G_USER.gameid'), 'CONF_DBTYPE_CLUB', 0)) {
            try {
                $clubMod = new ClubUserModel();
            } catch (\Exception $e) {
                $ret['code'] = ERRCODE_DB_SELECT_ERR;
                $ret['msg'] = "数据库连接失败，错误信息：".$e->getMessage();
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = "数据库配置加载失败，请确认数据库配置信息";
            return $ret;
        }
        $modRet = $clubMod->getUserInfoByUserId($userid);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $info = $modRet['data'];
        if ($info) {
            $uinfo["isproxy"] = empty($info["isProxy"])?"否":"是";
            $uinfo["clubNames"] = $info["clubNames"];
        }

        // 是否有赠送元宝的权限
        $uinfo['isgivegold'] = in_array(AUTH_OPER_MN_GUSER_GIVE_YUANBAO, C("G_USER.operate")) ? true : false;

        $ret['data'] = $uinfo;
        return $ret;
    }

    /**
     * 获取游戏管理
     * @param array $param 筛选参数
     * @return array 逻辑处理的结果
     */
    public function getPyjRecodeListLogic($param)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        if (strtotime($param['starttime']) > 0 && strtotime($param['endtime']) > 0) {
            $recodeWhere .= "AND gameStartTime between '{$param['starttime']}' and '{$param['endtime']}' ";
        } else if (strtotime($param['starttime']) > 0) {
            $recodeWhere .= "AND gameStartTime > '{$param['starttime']}'  ";
        }

        //房间ID
        if ($param['roomid']) {
            $recodeWhere .= "AND roomId ='{$param['roomid']}' ";
        }

        //用户ID
        if ($param['userid']) {
            $recodeWhere .= "AND ( userId1 = {$param['userid']} OR  userId2 = {$param['userid']} OR  userId3 = {$param['userid']} OR  userId4 = {$param['userid']} ) ";
        }

        //参与用户数
        switch ($param['usercount']) {
            case 4: //4人
                $recodeWhere .= "AND userId4 > 0 ";
                break;
            case 3:
                $recodeWhere .= "AND userId3 > 0 AND userId4 = 0 ";
                break;
            case 2:
                $recodeWhere .= "AND userId2 > 0 AND userId3 = 0 ";
                break;
        }

        //房间类型
        if ($param['roomtype'] == 1) { //普通开房
            $recodeWhere .= "AND  clubId = 0 ";
        } else if ($param['roomtype'] == 2) { //俱乐部房间
            $recodeWhere .= "AND  clubId > 0 ";
        }

        //正常结束
        if ($param['isnormal'] == 1) {
            $recodeWhere .= "AND  gameNum = roundCount  "; //正常结束
        } else if ($param['isnormal'] == 2) {
            $recodeWhere .= "AND  gameNum > roundCount "; //非正常结束
        }

        //查询游戏主库用户信息
        $gameid = C('G_USER.gameid');
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load($gameid, 'CONF_DBTYPE_GAME_LOG_DEV', 0)) {
            try {
                $pyjUserLogModel = new PyjRecodeModel();
                $room = $pyjUserLogModel->queryPyjRecorListByAttr(ltrim($recodeWhere, 'AND'));
                if ($room['code'] != ERRCODE_SUCCESS) { //异常
                    return $room;
                }
                $ret['msg'] = '查询成功';
                $ret['data'] = $room['data'];
                return $ret;
            } catch (\Exception $e) {
                $ret['code'] = ERRCODE_DB_SELECT_ERR;
                $ret['msg'] = "数据库查询失败，错误信息：" . $e->getMessage();
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = "游戏日志数据库配置加载失败，请确认数据库配置信息";
            return $ret;
        }
    }

    /**
     * 获取朋友局开房战绩信息
     */
    public function getPyjUserRecodeListLogic($param)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $recodeWhere = array() ;

        //id
        if ($param['id']) {
            $recodeWhere['id'] = $param['id'];
        }

        //查询游戏主库用户信息
        $gameid = C('G_USER.gameid');
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load($gameid, 'CONF_DBTYPE_GAME_LOG_DEV', 0)) {
            try {
                $pyjLogModel = new PyjRecodeModel();
                $room = $pyjLogModel->queryPyjRecorInfoByAttr($recodeWhere);
                $roominfo  = $room['data'] ;
                if ($room['code'] != ERRCODE_SUCCESS || empty($roominfo['roomId']) ) { //异常
                    $room['msg'] = '数据不存在或参数异常';
                    return $room;
                }

                $pyjUserLogModel = new PyjUserRecordModel();
                $roomid = $roominfo['roomId'] ;

                //房间扩展信息
                $roomExtend = $this->roomExtendDataLogic($roomid) ;
                if($roomExtend['code'] != ERRCODE_SUCCESS){
                    return $roomExtend;
                }

                //房间人数
                if($roominfo['userId4'] > 0){
                    $usernumber = 4 ;
                }else if($roominfo['userId3'] > 0){
                    $usernumber = 3 ;
                }else if($roominfo['userId2'] > 0){
                    $usernumber = 2 ;
                }
                $roominfo['userCount'] = $usernumber ;

                switch ($roominfo['payType']){
                    case 3: //AA付费
                        $roominfo['roomFee']  = ceil($roominfo['gameCard'] / $usernumber) ;
                        break;
                    case 2:
                        $roominfo['roomFee']  = $roominfo['gameCard'] ;
                        break;
                    case 1:
                        $roominfo['roomFee']  = $roominfo['gameCard'] ;
                        break;
                    case 0:
                        $roominfo['roomFee']  = $roominfo['gameCard'] ;
                        break;
                }

                //牌局战绩列表
                $map = array(
                    'roomID' => $roomid ,
                    'gameStartTime' => array('egt',$roominfo['gameStartTime']),
                    'date' => array('elt',$roominfo['gameStopTime']),
                );
                $userRecoud = $pyjUserLogModel ->getGameRoomRecordByWhere($map) ;
                //没有牌局信息
                if ($userRecoud['code'] != ERRCODE_SUCCESS ) {
                    $userRecoud['msg'] = '查询牌局信息错误';
                    return $userRecoud;
                }

                $ret['msg'] = '查询成功';
                $list = $userRecoud['data'];
                $roomCount = array(); //房间统计

                //牌局战绩数据处理和总分计算
                foreach ($list as $key => $value ){
                    $tempWinnerUserId = 0 ;
                    $tempWinnerUserDiff = 0 ;

                    if(!empty($value['userInfo1']) && $value['userInfo1'] != 'null'){
                        $user1 = json_decode($value['userInfo1']);
                        $list[ $key ]['userName1'] = $user1->nickName ;
                        $list[ $key ]['userId1'] = $user1->userId ;
                        $list[ $key ]['userCashDiff1'] = $user1->cashDiff ;

                        $roomCount[ 'uid_'.$user1->userId ]['CashDiff'] += $user1->cashDiff ;
                        $roomCount[ 'uid_'.$user1->userId ]['userName1'] = $user1->nickName ;

                        if($roomCount[ 'uid_'.$user1->userId ]['CashDiff'] > $tempWinnerUserDiff ){
                            $tempWinnerUserDiff = $roomCount[ 'uid_'.$user1->userId ]['CashDiff'] ;
                            $tempWinnerUserId = $user1->userId ;
                        }
                    }

                    if(!empty($value['userInfo2']) && $value['userInfo2'] != 'null'){
                        $user2 = json_decode($value['userInfo2']);
                        $list[ $key ]['userName2'] = $user2->nickName ;
                        $list[ $key ]['userId2'] = $user2->userId ;
                        $list[ $key ]['userCashDiff2'] = $user2->cashDiff ;
                        $roomCount[ 'uid_'.$user2->userId ]['CashDiff'] += $user2->cashDiff ;
                        $roomCount[ 'uid_'.$user2->userId ]['userName'] = $user2->nickName ;

                        if($roomCount[ 'uid_'.$user2->userId ]['CashDiff'] > $tempWinnerUserDiff ){
                            $tempWinnerUserDiff = $roomCount[ 'uid_'.$user2->userId ]['CashDiff'] ;
                            $tempWinnerUserId = $user2->userId ;
                        }
                    }

                    if(!empty($value['userInfo3']) && $value['userInfo3'] != 'null'){
                        $user3 = json_decode($value['userInfo3']);
                        $list[ $key ]['userName3'] = $user3->nickName ;
                        $list[ $key ]['userId3'] = $user3->userId ;
                        $list[ $key ]['userCashDiff3'] = $user3->cashDiff ;
                        $roomCount[ 'uid_'.$user3->userId ]['CashDiff'] += $user3->cashDiff ;
                        $roomCount[ 'uid_'.$user3->userId ]['userName'] = $user3->nickName ;

                        if($roomCount[ 'uid_'.$user3->userId ]['CashDiff']> $tempWinnerUserDiff ){
                            $tempWinnerUserDiff = $roomCount[ 'uid_'.$user3->userId ]['CashDiff'];
                            $tempWinnerUserId = $user3->userId ;
                        }
                    }

                    if(!empty($value['userInfo4']) && $value['userInfo4'] != 'null' ){
                        $user4 = json_decode($value['userInfo4']);
                        $list[ $key ]['userName4'] = $user4->nickName ;
                        $list[ $key ]['userId4'] = $user4->userId ;
                        $list[ $key ]['userCashDiff4'] = $user4->cashDiff ;
                        $roomCount[ 'uid_'.$user4->userId ]['CashDiff'] += $user4->cashDiff ;
                        $roomCount[ 'uid_'.$user4->userId ]['userName'] = $user4->nickName ;

                        if($roomCount[ 'uid_'.$user4->userId ]['CashDiff'] > $tempWinnerUserDiff ){
                            $tempWinnerUserDiff = $roomCount[ 'uid_'.$user4->userId ]['CashDiff'] ;
                            $tempWinnerUserId = $user4->userId ;
                        }
                    }
                }

                $ret['data']['list'] = $list;

                $roominfo['gameCount'] = $roomCount;

                //判断显示付费的人
                switch ( $roominfo['payType'] ){
                    case $pyjLogModel::PYJ_PAY_TYPE_CLUBLE :
                        $roominfo['showFreeUser'] = 'club';
                        break;
                    case $pyjLogModel::PYJ_PAY_TYPE_OWNER :
                        $roominfo['showFreeUser'] = $roominfo['ownerId'] ;
                        break;
                    case $pyjLogModel::PYJ_PAY_TYPE_WINNER :
                        $roominfo['showFreeUser'] = $tempWinnerUserId ;
                        break;
                    case $pyjLogModel::PYJ_PAY_TYPE_AA :
                        $roominfo['showFreeUser'] = 'AA' ;
                        break;
                }

                $ret['data']['roominfo']  = $roominfo ;

                return $ret;
            } catch (\Exception $e) {
                $ret['code'] = ERRCODE_DB_SELECT_ERR;
                $ret['msg'] = "数据库查询失败，错误信息：" . $e->getMessage();
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = "游戏日志数据库配置加载失败，请确认数据库配置信息";
            return $ret;
        }
    }

    public function roomExtendDataLogic ($roomid)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        //游戏库
        //查询游戏主库用户信息
        $gameid = C('G_USER.gameid');
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load($gameid, 'GAME_DEV_DB', 0)) {
            try {
                $extendWhere = array(
                    'roomid' => $roomid
                );
                $roomExtendModel = new SPrivateroomInfoModel();
                $roomExtendInfo = $roomExtendModel -> queryGameRoomExtendInfoByWhere($extendWhere);

                //异常处理
                if($roomExtendInfo['code'] != ERRCODE_SUCCESS ){
                    $ret['msg'] = $roomExtendInfo['msg'];
                    return $ret ;
                }

                $ret['data'] = $roomExtendInfo['data'];
                return $ret ;

            } catch (\Exception $e) {
                $ret['code'] = ERRCODE_DB_SELECT_ERR;
                $ret['msg'] = "游戏数据库查询失败，错误信息：" . $e->getMessage();
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = "游戏数据库配置加载失败，请确认数据库配置信息";
            return $ret;
        }
    }
}
