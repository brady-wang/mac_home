<?php

namespace Home\Controller;

use Common\Service\DbLoadConfigService;
use Home\Model\GameMailModel;
use Home\Model\SysCronlogModel;
use Common\Service\ApiService;
use Home\Model\GameDev\DevActivityModel;
use Home\Model\DsqpDict\ActivityModel;

ignore_user_abort();
set_time_limit(0);
ini_set('memory_limit', '2G');

/**
 * 定时邮件发送，每分钟启动一次
 * 1.如果达到定时时间则调用发送邮件接口
 * 2.检测新年拉新的时间，根据新年拉新活动时间修改邀请好友状态
 */
class CrontabToolController extends BaseController {
    public function __construct() {
        parent::__construct();

        if( APP_STATUS == 'production' ){
            $this->checkIsCli();
        }
        $this->cron_run_start_time = time();
    }

    /**
     * 执行定时邮件任务
     */
    public function execTimerMail() {
        //得到要同步的数据库id
        $gameids = $this->_getGame();

        $gameid = I('get.game');
        if (!empty($gameid)) {
            $gameids = array($gameid);
        }
        $tmnow = time();
        $cronMod = new SysCronlogModel();
        $cronType = $cronMod::CRON_TYPE_SYNC_GAME_MAIL_TIMER;
        $retCode = $cronMod::RET_CODE_SUCCESS;
        $retData = "定时邮件任务：";
        foreach ($gameids as $gameid) {
            try {
                $retData .= " | 包=$gameid,";
                $model = new GameMailModel();
                $where = array("send_flag" => 0, 'game_id'=>$gameid, "stime"=>array('elt', $tmnow), "_string" => "mail_status = 0 OR mail_status = 1");
                $modRet = $model->queryAllMailData($where);
                if ($modRet['code'] == ERRCODE_SUCCESS) {
                    $list = $modRet['data']['list'];
                    foreach ($list as $v) {
                        if ($v['mail_status'] == 1 && $v['etime'] > $tmnow) {     // 审核通过
                            $ret = $model->sendMail($v, $gameid);
                            if ($ret['code'] == ERRCODE_SUCCESS) {
                                $modRet = $model->updateMailSender($v['id']);
                                if ($modRet['code'] != ERRCODE_SUCCESS) {
                                    $retCode = $cronMod::RET_CODE_FAIL;
                                    $retData .= "更新发送状态失败".$modRet['msg'];
                                    break;
                                } else {
                                    $retData .= $v['id'].",";
                                }
                            } else {
                                $retCode = $cronMod::RET_CODE_FAIL;
                                $retData .= "发送邮件失败".$ret['msg'];
                                break;
                            }
                        } else {
                            if ($v['etime'] < $tmnow) {     // 发送邮件时间过期仍未审核
                                $modRet = $model->updateMailStatus($v['id'], 2);
                                if ($modRet['code'] != ERRCODE_SUCCESS) {
                                    $retCode = $cronMod::RET_CODE_FAIL;
                                    $retData .= "更新审核状态失败".$modRet['msg'];
                                    break;
                                }
                            }
                        }
                    }
                } else {
                    $retCode = $cronMod::RET_CODE_FAIL;
                    $retData .= "查询定时邮件列表失败".$modRet['msg'];
                }
            } catch (\Exception $e) {
                $retData .= "(包".$gameid."异常),".$e->getMessage();
            }
        }
        CRON_END:
        // 脚本结束时间
        $endTime = time();
        echo $retData;
        // 记录定时器流水
        $cronMod->insertSysCronlog($cronType, $this->cron_run_start_time, $endTime, $retCode, $retData);
    }

    /**
     * 执行新年拉新定时任务
     */
    public function execInviteFrd() {
        //得到要同步的数据库id
        $gameids = $this->_getGame();

        $gameid = I('get.game');
        if (!empty($gameid)) {
            $gameids = array($gameid);
        }
        $tmnow = time();
        $cronMod = new SysCronlogModel();
        $cronType = $cronMod::CRON_TYPE_SYNC_CRON_TIME;
        $retCode = $cronMod::RET_CODE_SUCCESS;
        $retData = "更改新年拉新配置为";
        foreach ($gameids as $gameid) {
            $retData .= " | 包=$gameid,";
            $confSer = new DbLoadConfigService();
            if (true === $confSer->load($gameid, 'GAME_DEV_DB', 0)) {
                try {
                    $devMod = new DevActivityModel();
                } catch (\Exception $e) {
                    $retCode = $cronMod::RET_CODE_FAIL;
                    $retData .= $e->getMessage();
                    //goto CRON_END;
                    continue;
                }
            } else {
                $retCode = $cronMod::RET_CODE_FAIL;
                $retData .= '数据库加载失败';
                //goto CRON_END;
                continue;
            }
            if (true === $confSer->load($gameid, 'GAME_DICT_DB', 0)) {
                try {
                    $modActive = new ActivityModel();
                } catch (\Exception $e) {
                    $retCode = $cronMod::RET_CODE_FAIL;
                    $retData .= $e->getMessage();
                    //goto CRON_END;
                    continue;
                }
            } else {
                $retCode = $cronMod::RET_CODE_FAIL;
                $retData .= '数据库加载失败';
                //goto CRON_END;
                continue;
            }

            $modRet = $devMod->getActivityList();
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $retCode = $cronMod::RET_CODE_FAIL;
                $retData .= $modRet['msg'];
                //goto CRON_END;
                continue;
            }
            $activityList = $modRet['data'];
            $activeId = 0;
            $actActiveId = 0;
            $status = $actStatus = 0;
            $starttime = $endtime = '';
            $devId = 0;
            foreach($activityList as $v) {
                $field = '';
                $modRet = $modActive->queryInfoById($v['activityId']);
                if ($modRet['code'] != ERRCODE_SUCCESS) {
                    $retCode = $cronMod::RET_CODE_FAIL;
                    $retData .= "查询列表失败".$modRet['msg'];
                    //goto CRON_END;
                    break;
                }
                $info = $modRet['data'];
                if ($info['type'] == 200 && $info['flag'] != 998) {
                    $devId = $v['id'];
                    $activeId = $v['activityId'];
                    if ($info['status'] == 1 && $v['status'] == 1) {
                        $status = 1;
                    }
                }
                if ($info['type'] == 200 && $info['gameId'] == $gameid && $info['flag'] == 998) {
                    $actStatus = $info['status'];
                    $starttime = strtotime($info['startTime']);
                    $endtime = strtotime($info['endTime']);
                    $actActiveId = $v['activityId'];
                }
            }
            if ($actActiveId > 0 && $activeId > 0) {
                $active = 1;
                if ($actStatus == 1 && $tmnow >= $starttime && $tmnow < $endtime) {
                    $active = 0;
                }
                if ($active != $status) {
                    $tmpdata = array('status'=>$active);
                    $modRet = $devMod->updateInfo($devId, $tmpdata, $gameid);
                    if ($modRet['code'] != ERRCODE_SUCCESS) {
                        $retCode = $cronMod::RET_CODE_FAIL;
                        $retData .= "更新dev库".$modRet['msg'];
                        //goto CRON_END;
                        continue;
                    }
                    $modRet = $modActive->updateInfo($activeId, $tmpdata, $gameid);
                    if ($modRet['code'] != ERRCODE_SUCCESS) {
                        $retCode = $cronMod::RET_CODE_FAIL;
                        $retData .= "更新活动库".$modRet['msg'];
                        //goto CRON_END;
                        continue;
                    }
                    $retData .= "游戏id=".$gameid.",active=$active,";
                    $apiSer = new ApiService();
                    // 调用服务端接口，刷新缓存
                    $serRet = $apiSer->kaifangApiQuery('/consoleactivity/?act=reload', $gameid);
                    if (ERRCODE_SUCCESS !== $serRet['code']) {
                        $retCode = $cronMod::RET_CODE_FAIL;
                        $retData .= "调用接口失败".$serRet['msg'];
                        //goto CRON_END;
                        continue;
                    }
                }
            }
        }
        CRON_END:
        // 脚本结束时间
        $endTime = time();

        // 记录定时器流水
        $cronMod->insertSysCronlog($cronType, $this->cron_run_start_time, $endTime, $retCode, $retData);
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
}
