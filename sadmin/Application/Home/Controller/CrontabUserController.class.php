<?php
namespace Home\Controller;

use Common\Service\DbLoadConfigService;
use Home\Model\StatUserBehaveModel;
use Home\Model\StatUserRegisterModel;
use Home\Model\StatUserRemainModel;
use Home\Model\StatUserTotalModel;
use Home\Model\StatUserChannelModel;
use Home\Model\SysCronlogModel;
use Home\Model\ClubLog\LogClubRoomcardModel;

ignore_user_abort();
set_time_limit(0);
ini_set('memory_limit', '2G');

/**
 * 定时任务，同步用户统计数据
 * @author liyao
 */
class CrontabUserController extends BaseController
{
    const ORG_DATE = '';

    private $_gameid = 0;
    private $_dbdev = null;
    private $_dblogdev = null;
    private $_refresh = "";
    private $_synctb = '';
    private $cron_run_start_time ;

    public function __construct() {
        parent::__construct();

        if( APP_STATUS == 'production' ){
            $this->checkIsCli();
        }

        $ip = get_client_ip();
        $this->cron_run_start_time = time();
    }

    /*
     * 定时同步每天的游戏日志
     */
    public function stat() {
        //得到要同步的数据库id
        $sync_game = $this->_getGame();

        $refresh = I('get.refresh');
        if (!empty($refresh)) {
            $this->_refresh = $refresh;
        } else {
            $this->_refresh = "";
        }

        $synctb = I('get.synctb');
        if (!empty($synctb)) {
            $this->_synctb = $synctb;
        } else {
            $this->_synctb = "";
        }

        $gameid = I('get.game');
        if (!empty($gameid)) {
            $this->_gameid = $gameid;
            $this->_syncGame();
        } else {
            foreach ($sync_game as $gameid) {
                $this->_gameid = $gameid;
                $this->_syncGame();
            }
        }
    }

    private function _syncGame() {
        try {
            $this->_getDbGameLogDev();
            $this->_getDbGameDev();

            if ($this->_dbdev && $this->_dblogdev) {
                if (!$this->_synctb || $this->_synctb == 'total')
                    $this->_syncTotal();
                if (!$this->_synctb || $this->_synctb == 'remain')
                    $this->_syncRemain();
                if (!$this->_synctb || $this->_synctb == 'behave')
                    $this->_syncBehave();
                if (!$this->_synctb || $this->_synctb == 'register')
                    $this->_syncRegister();
                if (!$this->_synctb || $this->_synctb == 'channel')
                    $this->_syncChannel();
            }
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[_syncGame] sync game: ".$e->getMessage());
        }
    }

    /**
     * 得到统计的起始和结束日期
     */
    private function _checkSyncDate($type, &$startDate, &$endDate) {
        $mod = null;
        $orgDate = self::ORG_DATE;
        switch ($type) {
            case "total":
                $mod = new StatUserTotalModel();
                break;
            case "remain":
                $mod = new StatUserRemainModel();
                break;
            case "behave":
                $mod = new StatUserBehaveModel();
                break;
            case "register":
                $mod = new StatUserRegisterModel();
                break;
            case "channel":
                $mod = new StatUserChannelModel();
                break;
            default:
                break;
        }
        if (!$mod) {
            set_exception(__FILE__, __LINE__, "skip mod {$type}");
            return false;
        }
        if ($mod)
            $maxDate = $mod->queryStatUserMaxDate($this->_gameid);
        if (!empty($maxDate)) {
            $startDate = strtotime ("+1 day", $maxDate);
        } else if (!empty($orgDate)) {
            $startDate = strtotime($orgDate);
        } else {
            $startDate = strtotime(date("Y-m-d", strtotime("-1 day")));
        }
        if (!empty(I('get.begin'))) {
            $startDate = strtotime(I('get.begin'));
        }
        $endDate = strtotime("-1 day");
        if (!empty(I('get.end'))) {
            $endDate = strtotime(I('get.end'));
        }
        if ($this->_refresh == $type) {
            $startDate = $mod->queryStatUserMinDate($this->_gameid);
            $endDate = $mod->queryStatUserMaxDate($this->_gameid);
        } else if (!empty($this->_refresh)) {
            return false;
        }
        if (empty($startDate)) {
            return false;
        }
        return true;
    }

    /**
     * 同步累计数据
     */
    private function _syncTotal() {
        //得到要同步的起始日期及截至日期
        $mod = new StatUserTotalModel();
        $ret = $this->_checkSyncDate("total", $startDate, $endDate);
        if (!$ret) {
            return false;
        }
        //同步日期区间内的数据
        for ($curDate = $startDate; $curDate <= $endDate; $curDate += 24 * 3600) {
            $nextDay = date("Y-m-d 00:00:00", strtotime("+1 day", $curDate));
            $item = array();
            $item["game_id"] = $this->_gameid;
            $item["data_time"] = $curDate;

            $where = array();
            $where['type'] = 4;
            $where['createTime'] = array("lt", $nextDay);
            $total_user = $this->_dbdev->table('u_user_info')->where($where)->count();
            $item["register_num"] = floor($total_user);

            $where = array();
            $where["serviceArea"] = $this->_gameid;
            $where["createTime"] = array("lt", $nextDay);
            $item["agent_num"] = 0;

            $item["agent_charge_count"] = $item["agent_charge_price"] = 0;

            // 游戏消耗钻石
            $where = array("way" => 100);
            $where["createTime"] = array("lt", $nextDay);
            $game_prop = floor($this->_dblogdev->table('u_props_log')->where($where)->sum("propsNum"));
            //俱乐部扣钻
            $confSer = new DbLoadConfigService();
            $dbStatus = $confSer->load($this->_gameid, 'CONF_DBTYPE_CLUB_LOG', 0);
            if (true === $dbStatus) {
                try {
                    $clubRoomCard = new LogClubRoomcardModel() ;
                } catch (\Exception $e) {
                    set_exception(__FILE__, __LINE__, "[_syncTotal] sync club roomcard: ".$e->getMessage());
                    $this->_writeCronRunLog('total', $this->cron_run_start_time , time() , 'RET_CODE_WARNING' , "[Crontab total] 创建LogClubRoomcardModel模型异常 ".$e->getMessage());
                }
            } else {
                set_exception(__FILE__, __LINE__, "[_syncTotal] get db failure ");
                $this->_writeCronRunLog('total', $this->cron_run_start_time , time() , 'RET_CODE_WARNING' , "[Crontab total] LogClubRoomcardModel模型不存在 ");
                return;
            }
            $clubCardWhere = array(
            'gameId' => $this->_gameid ,
            'createTime' => array("lt", $nextDay)
            );
            $clubData =  $clubRoomCard->queryClubRoomCardNumberByWhere($clubCardWhere);
            $club_prop = floor($clubData['data']);
            $item["consume_prop"] = $game_prop + $club_prop;

            $mod->insertStatTotal($item, $this->_gameid, $curDate);
            $this->_writeCronRunLog( 'total' , $this->cron_run_start_time , time() , 'RET_CODE_SUCCESS' , "[Crontab total] 累计统计执行成功" );
        }
    }

    /*
     * 同步用户留存
     */
    private function _syncRemain() {
        //得到要同步的起始日期及截至日期
        $mod = new StatUserRemainModel();
        $ret = $this->_checkSyncDate("remain", $startDate, $endDate);
        if (!$ret)
            return;
        //同步日期区间内的数据
        for ($curDate = $startDate; $curDate <= $endDate; $curDate += 24 * 3600) {
            $add_user = 0;
            $sDateTime = date("Y-m-d", $curDate);
            $eDateTime = date("Y-m-d", strtotime("+1 day", $curDate));
            $item = array();
            $item["game_id"] = $this->_gameid;
            $item["data_time"] = $curDate;

            if (intval($add_user) == 0) {
                $where = array();
                $where['type'] = 4;
                $where['createTime'][] = array('egt', $sDateTime);
                $where['createTime'][] = array('lt', $eDateTime);
                $add_user = $this->_dbdev->table('u_user_info')->where($where)->count();
            }
            $add_user = intval($add_user);
            $item["add_register"] = $add_user;

            $arr = array(1, 3, 7, 15, 30);
            for ($idx = 0; $idx < count($arr); $idx++) {
                $inter = $arr[$idx];
                $tarr = array();
                $stm = strtotime("-".$inter." day", $curDate);
                if ($inter-1 > 0) {
                    $etm = strtotime("-".($inter-1)." day", $curDate);
                } else {
                    $etm = $curDate;
                }

                $where = array();
                $where['type'] = 4;
                $where['createTime'][] = array('egt', date("Y-m-d", $stm));
                $where['createTime'][] = array('lt', date("Y-m-d", $etm));
                $reg_user = intval($this->_dbdev->table('u_user_info')->where($where)->count());

                $sql = "select count(*) as c from (select userId, inter from (select userId, DATEDIFF(loginTime, registerTime) as inter from u_user_info_log ".
                    "where registerTime >= '".date("Y-m-d", $stm)."' AND registerTime < '".date("Y-m-d", $etm)."') as sub where inter={$inter} group by userId) as sub2";
                $data = $this->_dblogdev->query($sql);
                $remainNum = intval($data[0]['c']);
                $tarr["keep_day".$inter] = empty($reg_user) ? 0 : $remainNum/$reg_user*100;
                if ($tarr["keep_day".$inter] > 100) {
                    $tarr["keep_day".$inter] = 0;
                }
                $tarr["keep_day".$inter."_num"] = $remainNum;
                $mod->insertStatUserRemain($tarr, $this->_gameid, $stm, false);
            }

            $mod->insertStatUserRemain($item, $this->_gameid, $curDate);
            $this->_writeCronRunLog( 'remain', $this->cron_run_start_time , time() , 'RET_CODE_SUCCESS' , "[Crontab remain] 用户留存统计执行成功 " );
        }
    }

    /*
     * 同步行为统计
     */
    private function _syncBehave() {
        //得到要同步的起始日期及截至日期
        $mod = new StatUserBehaveModel();
        $ret = $this->_checkSyncDate("behave", $startDate, $endDate);
        if (!$ret)
            return;
        //同步日期区间内的数据
        for ($curDate = $startDate; $curDate <= $endDate; $curDate += 24 * 3600) {
            $sDateTime = date("Y-m-d H:i:s", $curDate);
            $eDateTime = date("Y-m-d H:i:s", strtotime("+1 day", $curDate));
            $item = array();
            $item["game_id"] = $this->_gameid;
            $item["data_time"] = $curDate;

            $where = array();
            $where["gameStartTime"][] = array("egt", $sDateTime);
            $where["gameStartTime"][] = array("lt", $eDateTime);
            //$data = $this->_dblogdev->table('u_pyj_user_record')->where($where)->group('userID')->field('userID')->select();
            //$item["active_user"] = count($data);
            $data = $this->_dblogdev->query("select count(*) as c from (select userId from u_user_info_log where loginTime >= '" .
                    $sDateTime . "' and loginTime < '" . $eDateTime . "' group by userId) as sub1");
            $item["login_user"] = intval($data[0]["c"]);
            $userMap = array();
            $page = 0;
            $pageLimit = 10000;
            while (true) {
                $data = $this->_dblogdev->table("u_pyj_record_log")->field('userId1,userId2,userId3,userId4')->where("gameStartTime >= '" .$sDateTime . "' and gameStartTime < '" . $eDateTime . "'")->order('id ASC')->limit($page*$pageLimit, $pageLimit)->select();
                foreach ($data as $v) {
                    for ($i = 1; $i <= 4; $i++) {
                        $uid = isset($v['userId'.$i])?$v['userId'.$i]:$v['userid'.$i];
                        if ($uid != 0)
                            $userMap[$uid] = 1;
                    }
                }
                $page++;
                if (count($data) < $pageLimit)
                    break;
            }
            $item["active_user"] = count($userMap);
            /*$userMap = array();
            $data = $this->_dblogdev->query("select userID from u_pyj_user_record where gameStartTime >= '" .
                    $sDateTime . "' and gameStartTime < '" . $eDateTime . "'");
            foreach ($data as $v) {
                $uid = isset($v['userID']) ? $v['userID'] : $v['userid'];
                $userMap[$uid] = 1;
            }
            $item["active_user"] = count($userMap);*/

            //$item["share_games"] = $item["share_rooms"] = $item["invite_ids"] = $item["invite_friends"] = 0;
            try {
                $data = $this->_dblogdev->query("select count(*) as c from u_share_log where way = 1 and createTime >= '".$sDateTime."' and createTime < '".$eDateTime."'");
                $item["share_games"] = intval($data[0]["c"]);
                $data = $this->_dblogdev->query("select count(*) as c from u_share_log where way = 2 and createTime >= '".$sDateTime."' and createTime < '".$eDateTime."'");
                $item["invite_friends"] = intval($data[0]["c"]);
                $data = $this->_dblogdev->query("select count(*) as c from u_share_log where way = 3 and createTime >= '".$sDateTime."' and createTime < '".$eDateTime."'");
                $item["share_rooms"] = intval($data[0]["c"]);
                $data = $this->_dbdev->query("select count(*) as c from u_inviters where createTime >= '".$sDateTime."' and createTime < '".$eDateTime."'");
                $item["invite_ids"] = intval($data[0]["c"]);
            } catch (\Exception $e) {
                set_exception(__FILE__, __LINE__, "[_syncBehave] sync share invite: ".$e->getMessage());
                $this->_writeCronRunLog( 'behave'  , $this->cron_run_start_time , time() , 'RET_CODE_WARNING' , "[Crontab behave] 行为统计sql异常 ".$e->getMessage() );
            }

            $mod->insertStatUserBehave($item, $this->_gameid, $curDate);
            $this->_writeCronRunLog( 'behave' , $this->cron_run_start_time , time() , 'RET_CODE_SUCCESS' , "[Crontab behave] 行为统计执行成功");
        }
    }

    /*
     * 同步注册来源
     */
    private function _syncRegister() {
        //得到要同步的起始日期及截至日期
        $mod = new StatUserRegisterModel();
        $ret = $this->_checkSyncDate("register", $startDate, $endDate);
        if (!$ret)
            return;
        //同步日期区间内的数据
        for ($curDate = $startDate; $curDate <= $endDate; $curDate += 24 * 3600) {
            $item = array();
            $item["game_id"] = $this->_gameid;
            $item["data_time"] = $curDate;

            $where = array();
            $where['type'] = 4;
            $where["spId"] = 10002;
            $where['createTime'][] = array('egt', date("Y-m-d", $curDate));
            $where['createTime'][] = array('lt', date("Y-m-d", strtotime("+1 day", $curDate)));
            $val = intval($this->_dbdev->table('u_user_info')->where($where)->count());
            $item["appstore"] = floor($val);

            $where = array();
            $where['type'] = 4;
            $where["spId"] = 10001;
            $where['createTime'][] = array('egt', date("Y-m-d", $curDate));
            $where['createTime'][] = array('lt', date("Y-m-d", strtotime("+1 day", $curDate)));
            $val = intval($this->_dbdev->table('u_user_info')->where($where)->count());
            $item["app_store"] = floor($val);
            $item["down_page"] = 0;

            $mod->insertStatUserRegister($item, $this->_gameid, $curDate);
            $this->_writeCronRunLog( 'register' , $this->cron_run_start_time , time() , 'RET_CODE_SUCCESS' , "[Crontab register] 注册来源统计执行成功 " );
        }
    }

    /*
     * 同步渠道统计
     */
    private function _syncChannel()
    {
        //得到要同步的起始日期及截至日期
        $mod = new StatUserChannelModel();
        $ret = $this->_checkSyncDate("channel", $startDate, $endDate);
        if (!$ret) {
            return;
        }

        //同步日期区间内的数据
        $codeArr = array();
        $mod2 = new \Home\Model\GameChannelModel();
        $modRet = $mod2->getChannelCode(array(), $this->_gameid);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $this->_writeCronRunLog( 'channel'  , $this->cron_run_start_time , time() , 'RET_CODE_FAIL' , "[Crontab channel] 未得到渠道数据 ".$modRet["msg"]);
            return;
        } else {
            $list = $modRet["data"]["list"];
        }
        for ($curDate = $startDate; $curDate <= $endDate; $curDate += 24 * 3600) {
            $sDateTime = date("Y-m-d", $curDate);
            $eDateTime = date("Y-m-d", strtotime("+1 day", $curDate));
            $item = array();
            $item["game_id"] = $this->_gameid;
            $item["data_time"] = $curDate;
            foreach ($list as $iv) {
                $item['os'] = $iv['os'];
                $item['code'] = $iv['code'];
                $item["login_number"] = 0;
                $item["register_number"] = 0;

                try {
                    $data = $this->_dbdev->query("select count(*) as c from u_user_ext where updateAreaTime >= '".$sDateTime.
                        "' and updateAreaTime < '".$eDateTime."' and identificationCode='".$iv['code']."'");
                    $item["register_number"] = empty($data[0]["c"]) ? 0 : $data[0]["c"];
                    $data = $this->_dblogdev->query("select count(*) as c from (select userId from u_user_info_log where loginTime >= '".$sDateTime.
                        "' and loginTime < '".$eDateTime."' and identificationCode='".$iv['code']."' and isChosenArea=1 group by userId) as sub1");
                    $item["login_number"] = empty($data[0]["c"]) ? 0 : $data[0]["c"];
                } catch (\Exception $e) {
                    set_exception(__FILE__, __LINE__, "[_syncChannel] sync channel failure: ".$e->getMessage());
                    $this->_writeCronRunLog( 'channel'  , $this->cron_run_start_time , time() , 'RET_CODE_WARNING' , "[Crontab channel] 渠道统计异常 ".$e->getMessage() );
                }
                $mod->insertStatUserChannel($item, $this->_gameid, $curDate, $iv['code']);
            }

            $this->_writeCronRunLog( 'channel' , $this->cron_run_start_time , time() , 'RET_CODE_SUCCESS' , "[Crontab channel] 渠道统计执行成功 " );
        }
    }

    /*
     * 得到要同步的游戏id
     */
    private function _getGame() {
        $data = M("Game")->where(array("game_status" => 1))->select();
        $game = array();
        foreach ($data as $v) {
            $game[] = $v["game_id"];
        }
        return $game;
    }

    /*
     * 得到游戏数据库日志对象
     */
    private function _getDbGameLogDev() {
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load($this->_gameid, 'CONF_DBTYPE_GAME_LOG_DEV', 0)) {
            try {
                $this->_dblogdev = new \Think\Model('', '', 'CONF_DBTYPE_GAME_LOG_DEV');
            } catch (\Exception $e) {
                set_exception(__FILE__, __LINE__, "[_getDbGameLogDev] get devlog: ".$e->getMessage());
                $this->_dblogdev = null;
            }
        } else {
            set_exception(__FILE__, __LINE__, "[_getDbGameLogDev] get devlog configure failure");
            $this->_dblogdev = null;
        }
    }

    /*
     * 得到游戏数据库信息对象
     */
    private function _getDbGameDev() {
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load($this->_gameid, 'GAME_DEV_DB', 0)) {
            try {
                $this->_dbdev = new \Think\Model('', '', 'GAME_DEV_DB');
            } catch (\Exception $e) {
                set_exception(__FILE__, __LINE__, "[_getDbGameDev] get dev: ".$e->getMessage());
                $this->_dbdev = null;
            }
        } else {
            set_exception(__FILE__, __LINE__, "[_getDbGameDev] get dev configure failure");
            $this->_dbdev = null;
        }
    }

    /**
     * 记录定时器流水日志
     */
    private function _writeCronRunLog($cronType, $startTime, $endTime , $status , $info)
    {
        $logModel = new SysCronlogModel();
        switch ($cronType){

            case 'daily':// 同步每日简报
                $cronTypeCode = $logModel::CRON_TYPE_STAT_USER_GAME_DAILY;
                break;
            case 'total':// 用户累加
                $cronTypeCode = $logModel::CRON_TYPE_STAT_USER_GAME_TOTAL;
                break;
            case 'remain':// 同步用户留存
                $cronTypeCode = $logModel::CRON_TYPE_STAT_USER_GAME_REMAIN;
                break;
            case 'behave':// 行为统计
                $cronTypeCode = $logModel::CRON_TYPE_STAT_USER_GAME_BEHAVE;
                break;
            case 'register':// 同步注册来源
                $cronTypeCode = $logModel::CRON_TYPE_STAT_USER_GAME_REGEISTER;
                break;
            case 'channel':// 渠道统计
                $cronTypeCode = $logModel::CRON_TYPE_SYNC_USER_GAME_CHANNEL;
                break;
            default :
                $cronTypeCode = 0 ;
                break;
        }

        switch ($status){
            case 'RET_CODE_SUCCESS': // 10
                $retCode = $logModel::RET_CODE_SUCCESS;
                break;
            case 'RET_CODE_WARNING': //90
                $retCode = $logModel::RET_CODE_WARNING;
                break;
            case 'RET_CODE_FAIL': // 99
                $retCode = $logModel::RET_CODE_FAIL;
                break;
        }

        $logModel->insertSysCronlog($cronTypeCode, $startTime, $endTime, (int)$retCode, $info);
    }
}
