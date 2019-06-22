<?php
namespace Home\Controller;

use Home\Logic\StatLogic;
use Home\Model\GameModel;
use Home\Model\StatClubPromoterModel;
use Home\Model\StatDiamondProduceModel;
use Home\Model\StatGameShareModel;
use Home\Model\StatUserDailyModel;
use Home\Model\StatUserRankModel;
use Home\Model\SysCacheModel;
use Home\Model\SysCronlogModel;

class CronStatController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        // 正式服仅支持CLI模式
        if ('production' === APP_STATUS) {
            $this->checkIsCli();
        }

        // 统计脚本要确保脚本可完整执行，不能中途中断
        ignore_user_abort();
        set_time_limit(0);
        ini_set('memory_limit', '2G');
    }

    /**
     * 统计亲友圈代理概况数据
     * 执行时间：每天凌晨一点半执行
     * cron:
     * 30 1 * * * flock -xn /tmp/sad_cron_stat_club_promoter.lock -c
     *    'php /rootdir/index.php Home/CronStat/statClubPromoter > /dev/null 2>&1'
     * @author Carter
     */
    public function statClubPromoter()
    {
        $cronMod = new SysCronlogModel();
        $gameMod = new GameModel();
        $statMod = new StatClubPromoterModel();
        $statLgc = new StatLogic();

        // 页面模式与命令行模式使用各自的换行符
        if (IS_CLI) {
            $cr = "\n";
        } else {
            $cr = "<br/>";
        }

        // 脚本开始时间
        $startTime = time();

        $cronType = $cronMod::CRON_TYPE_STAT_CLUB_PROMOTER;
        $retCode = $cronMod::RET_CODE_SUCCESS;
        $retData = "[Info] 统计脚本开始统计，时间".date('Y-m-d H:i:s', $startTime).$cr;

        // 统计截止时间，截止到昨天
        $cutoffTime = strtotime('-1 day midnight', $startTime);

        // 获取有效游戏列表，仅获取有效的
        $attr = array('game_status' => $gameMod::GAME_STATUS_ON);
        $field = 'game_id';
        $modRet = $gameMod->queryGameAllList($attr, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retCode = $cronMod::RET_CODE_FAIL;
            $retData = "[Error] 获取有效游戏列表失败：{$modRet['msg']}{$cr}";
            goto CRON_END;
        }
        $gameList = array_column($modRet['data'], 'game_id');

        // 遍历游戏列表，对每个游戏进行统计
        foreach ($gameList as $gameId) {
            // 获取游戏最新一条统计数据
            $modRet = $statMod->queryStatPromoterLastRow($gameId);
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $retCode = $cronMod::RET_CODE_FAIL;
                $retData .= "[Error] 游戏{$gameId}获取最新数据失败：{$modRet['msg']}";
                goto CRON_END;
            }
            // 若无数据，需要先进行数据初始化
            if (empty($modRet['data'])) {
                $retCode = $cronMod::RET_CODE_WARNING;
                $retData .= "[Warning] 游戏{$gameId}需要执行 Home/Cli/initStatClubPromoterData 进行数据初始化{$cr}";
                continue;
            }
            $latestInfo = $modRet['data'];

            while ($latestInfo['stat_time'] < $cutoffTime) {
                // 记录每个游戏每一天的统计时长，用于记录日志，开始时间锚点
                $anchorTime = microtime(true);

                $sDate = date('Y-m-d', strtotime('+1 day', $latestInfo['stat_time']));

                // 执行俱乐部概况统计的主逻辑，从最后一条记录统计至昨天为止
                $lgcRet = $statLgc->statMarketClubPromoter($latestInfo, $gameId, $sDate);
                if (ERRCODE_SUCCESS !== $lgcRet['code']) {
                    $echoMsg = "[Warning] 游戏{$gameId}统计至".date('Y-m-d H:i:s', $cutoffTime)."失败：{$lgcRet['msg']}{$cr}";
                    $retCode = $cronMod::RET_CODE_WARNING;
                    $retData .= $echoMsg;
                    continue;
                }
                $latestInfo = $lgcRet['data'];

                $timeOffset = round(microtime(true) - $anchorTime, 3);
                $retData .= "[Info] 游戏 {$gameId} {$sDate} 统计完毕，统计 id {$latestInfo['id']}，耗时 {$timeOffset} 秒{$cr}";
            }
        }

        $retData .= "[Info] 全部统计完毕{$cr}";

    CRON_END:
        // 脚本结束时间
        $endTime = time();

        echo $retData;

        // 记录定时器流水
        $cronMod->insertSysCronlog($cronType, $startTime, $endTime, $retCode, $retData);

        return true;
    }

    /**
     * 在线人数统计
     * 执行时间： 每5分钟执行一次
     * cron:
     * [0-59]/5 * * * * flock -xn /tmp/sad_cron_stat_online.lock -c
     *    'php /rootdir/index.php Home/CronStat/statGameOnline  > /dev/null 2>&1'
     * 统计指定游戏在线人数：
     *    php /rootdir/index.php Home/CronStat/statGameOnline/game_id/4156
     */
    public function statGameOnline()
    {
        $cronMod = new SysCronlogModel();   //定时任务日志model
        $gameMod = new GameModel();         //游戏model
        $statLgc = new StatLogic();         //数据统计逻辑

        //游戏产品ID
        $gameId = (int) I('get.game_id') ;
        if( empty($gameId) ){
            $logCode = $cronMod::RET_CODE_SUCCESS;

            // 脚本开始时间
            $cronExecStartTime = time();

            $logMsg = "[Info] 统计脚本开始统计，时间：".date('Y-m-d H:i:s', $cronExecStartTime)."\n";

            // 获取有效游戏列表，仅获取有效的
            $attr = array('game_status' => $gameMod::GAME_STATUS_ON);
            $field = 'game_id';
            $modRet = $gameMod->queryGameAllList($attr, $field);

            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $logCode = $cronMod::RET_CODE_FAIL;
                $logMsg = '[Error] 获取有效游戏列表失败：'.$modRet['msg'];
                goto CRON_END ;
            }
            $gameList = array_column($modRet['data'], 'game_id');
            $logMsgString = '' ;
            foreach($gameList as $gameId){
                $result = $statLgc->statOneGameOnlineLogic($gameId);

                if( $result['code'] != ERRCODE_SUCCESS ){
                    $logCode = $cronMod::RET_CODE_WARNING;  //异常
                    $logMsgString .= "[Warning] 游戏：{$gameId} - ".date('Y-m-d',time() )." 在线处理异常。" .$result['msg']."<br > " ;
                }else{
                    $logCode = $cronMod::RET_CODE_SUCCESS;  //单个游戏处理完成
                    $logMsgString .= "[Info] 游戏：{$gameId} - ".date('Y-m-d',time() )."  在线处理完成。" . $result['msg']."<br > " ;
                }

            }
        } else {
            $result = $statLgc->statOneGameOnlineLogic($gameId);
            if ($result['code'] != ERRCODE_SUCCESS) {
                $logCode = $cronMod::RET_CODE_WARNING;  //异常
                $logMsgString .= "[Warning] 游戏：{$gameId} - ".date('Y-m-d',time() )." 在线处理异常。" .$result['msg']."<br > " ;
            } else {
                $logCode = $cronMod::RET_CODE_SUCCESS;  //单个游戏处理完成
                $logMsgString .= "[Info] 游戏：{$gameId} - ".date('Y-m-d',time() )."  在线处理完成。" . $result['msg']."<br > " ;
            }
        }

    CRON_END:
        // 脚本结束时间
        $cronExecEndTime = time();

        $logMsg = date('Y-m-d', time()).'在线人数定时任务处理完成<br >'.$logMsgString;
        // 记录定时器流水
        $cronMod->insertSysCronlog($cronMod::CRON_TYPE_SYNC_GAMEONLINE , $cronExecStartTime, $cronExecEndTime, $logCode, $logMsg);

        return true;
    }

    /**
     * 统计每日简报数据，每小时统计一次，统计到当天当前时间为止
     * 执行时间： 每小时
     * cron:
     * 0 * * * * flock -xn /tmp/sad_cron_stat_user_daily.lock -c
     *    'php /rootdir/index.php Home/CronStat/statUserDaily > /dev/null 2>&1'
     * @author Carter
     */
    public function statUserDaily()
    {
        $cronMod = new SysCronlogModel();
        $cacheMod = new SysCacheModel();
        $gameMod = new GameModel();
        $dailyMod = new StatUserDailyModel();
        $statLgc = new StatLogic();

        // 页面模式与命令行模式使用各自的换行符
        if (IS_CLI) {
            $cr = "\n";
        } else {
            $cr = "<br/>";
        }

        $cronType = $cronMod::CRON_TYPE_STAT_USER_GAME_DAILY;
        $retCode = $cronMod::RET_CODE_SUCCESS;
        $retData = "";

        // 脚本开始时间
        $startTime = time();

        $retData = "[Info] 统计脚本开始统计，时间 ".date('Y-m-d H:i:s', $startTime)."{$cr}";

        // 获取有效游戏列表，仅获取有效的
        $attr = array('game_status' => $gameMod::GAME_STATUS_ON);
        $field = 'game_id';
        $modRet = $gameMod->queryGameAllList($attr, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retCode = $cronMod::RET_CODE_FAIL;
            $retData .= '[Error] 获取有效游戏列表失败：'.$modRet['msg'];
            goto CRON_END;
        }
        $gameList = array_column($modRet['data'], 'game_id');

        // 统计截止时间：由于后端是异步写入战绩流水，可能存在5分钟左右的延迟，截止时间要置前10分钟，错过这个时间窗口
        $cutoffTime = $startTime - 600;

        // 遍历所有待统计游戏，开始统计流程
        foreach ($gameList as $gameId) {
            // 记录每个游戏的统计时长，用于记录日志，开始时间锚点
            $anchorTime = microtime(true);

            // 获取游戏最新一条统计数据
            $modRet = $dailyMod->queryStatDailyLatestInfo($gameId);
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $retCode = $cronMod::RET_CODE_FAIL;
                $retData .= "[Error] 游戏{$gameId}获取最新数据失败：{$modRet['msg']}";
                goto CRON_END;
            }
            // 若无数据，需要先进行数据初始化
            if (empty($modRet['data'])) {
                $retCode = $cronMod::RET_CODE_WARNING;
                $retData .= "[Warning] 游戏{$gameId}需要执行Home/Cli/initStatUserDailyData进行数据初始化{$cr}";
                continue;
            }
            $latestDailyInfo = $modRet['data'];

            // 获取统计开始时间
            $modRet = $cacheMod->querySysCacheByKey($gameId, 'DBCACHE_STAT_USER_DAILY_CUTOFFTIME');
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $retCode = $cronMod::RET_CODE_FAIL;
                $retData .= "[Error] 游戏{$gameId}获取统计时间失败：{$modRet['msg']}";
                goto CRON_END;
            }
            // 若cache丢失，清掉最新统计的数据，从该天开始统计
            if (empty($modRet['data'])) {
                $modRet = $dailyMod->updateStatUserDaily($latestDailyInfo['id'], 0, 0, 0);
                if (ERRCODE_SUCCESS !== $modRet['code']) {
                    $retCode = $cronMod::RET_CODE_FAIL;
                    $retData .= "[Error] 游戏{$gameId}更新{$latestDailyInfo['id']}失败：{$modRet['msg']}";
                    goto CRON_END;
                }
                $statStartTime = $latestDailyInfo['data_time'];
            } else {
                $statStartTime = $modRet['data']['cache_sting'];
            }

            // 统计数据
            $lgcRet = $statLgc->statUserDailyByTime($gameId, $statStartTime, $cutoffTime);
            if (ERRCODE_SUCCESS !== $lgcRet['code']) {
                $retCode = $cronMod::RET_CODE_FAIL;
                $retData .= "[Error] 游戏{$gameId}统计{$statStartTime}-{$cutoffTime}失败：{$lgcRet['msg']}";
                goto CRON_END;
            }

            // 记录CacheTime
            $cacheKey = 'DBCACHE_STAT_USER_DAILY_CUTOFFTIME';
            $remark = '数据统计用户每日简报最近统计时间的时间戳';
            $modRet = $cacheMod->exceSetSysCache($gameId, $cacheKey, $cutoffTime, $remark);
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $retCode = $cronMod::RET_CODE_FAIL;
                $retData .= "[Error] 游戏{$gameId}更新缓存统计时间失败：{$lgcRet['msg']}";
                goto CRON_END;
            }

            $timeOffset = round(microtime(true) - $anchorTime, 3);
            $retData .= "[Info] 游戏{$gameId}，时间 ".date('YmdHis', $statStartTime);
            $retData .= " - ".date('YmdHis', $cutoffTime);
            $retData .= "，耗时 {$timeOffset} 秒{$cr}";
        }

        $retData .= "[Info] 全部统计完毕{$cr}";

    CRON_END:
        // 脚本结束时间
        $endTime = time();

        echo $retData;

        // 记录定时器流水
        $cronMod->insertSysCronlog($cronType, $startTime, $endTime, $retCode, $retData);

        return true;
    }

    /**
     * 对局统计定时器，不输入任何参数则统计前一天数据，输入相应参数可以针对指定游戏的指定日期进行统计
     * 执行时间： 每天凌晨两点
     * cron:
     * 0 2 * * * flock -xn /tmp/sad_cron_stat_game_round.lock -c
     *    'php /rootdir/index.php Home/CronStat/statGameRound > /dev/null 2>&1'
     * 统计指定游戏的指定日期命令：
     *     php index.php Home/CronStat/statGameRound/gameId/xxxx/date/xxxxxxxx
     * Ex: php index.php Home/CronStat/statGameRound/gameId/4156/date/20180501
     * @author Carter
     */
    public function statGameRound()
    {
        /**************************************************
         * 由于后端战绩日志只能保存3天，所以这个统计逻辑不能自适应去统计连续日期
         * 现在只提供两种统计方式：
         * 1.默认，统计所有游戏前一天的数据
         * 2.同时输入 gameId 和 date 参数，统计指定游戏指定日期的数据
         **************************************************/

        $cronMod = new SysCronlogModel();
        $gameMod = new GameModel();
        $statLgc = new StatLogic();

        // 页面模式与命令行模式使用各自的换行符
        if (IS_CLI) {
            $cr = "\n";
        } else {
            $cr = "<br/>";
        }

        // 脚本开始时间
        $startTime = time();

        $cronType = $cronMod::CRON_TYPE_STAT_GAME_ROUND;
        $retCode = $cronMod::RET_CODE_SUCCESS;
        $retData = "[Info] 统计脚本开始统计，时间".date('Y-m-d H:i:s', $startTime).$cr;

        // 获取有效游戏列表，仅获取有效的
        $attr = array('game_status' => $gameMod::GAME_STATUS_ON);
        $field = 'game_id';
        $modRet = $gameMod->queryGameAllList($attr, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retCode = $cronMod::RET_CODE_FAIL;
            $retData = "[Error] 获取有效游戏列表失败：{$modRet['msg']}{$cr}";
            goto CRON_END;
        }
        $gameList = array_column($modRet['data'], 'game_id');

        // 大圣南京、老淮北和大圣不进行统计
        $idx = array_search('3425', $gameList);
        if (false !== $idx) {
            unset($gameList[$idx]);
        }
        $idx = array_search('10020', $gameList);
        if (false !== $idx) {
            unset($gameList[$idx]);
        }
        $idx = array_search('5542', $gameList);
        if (false !== $idx) {
            unset($gameList[$idx]);
        }

        // 默认统计前一天的数据
        $statTime = strtotime('-1 day midnight', $startTime);

        // 带参场景，游戏id和统计日期必须同时传入
        $paramGameId = I('get.gameId');
        if ($paramGameId) {
            // 游戏必须是有效清单内的，不能随便传一个阿猫阿狗进来
            if (!in_array($paramGameId, $gameList)) {
                $retCode = $cronMod::RET_CODE_WARNING;
                $retData .= "[Warning] 未知游戏 {$paramGameId}，退出统计{$cr}";
                goto CRON_END;
            }
            // 把游戏统计清单替换成传入的单个游戏，后续遍历的时候就只需要去遍历该游戏
            $gameList = array($paramGameId);

            // 如果不传入统计日期或格式不正确，照样把脚本干掉
            $paramStatDate = I('get.date');
            if (empty($paramStatDate)) {
                $retCode = $cronMod::RET_CODE_WARNING;
                $retData .= "[Warning] 未传入统计日期，退出统计{$cr}";
                goto CRON_END;
            }
            $paramStatTimestamp = strtotime($paramStatDate);
            if (false === $paramStatTimestamp) {
                $retCode = $cronMod::RET_CODE_WARNING;
                $retData .= "[Warning] 传入日期 {$paramStatDate} 格式不正确，退出统计{$cr}";
                goto CRON_END;
            }
            $statTime = strtotime('midnight', $paramStatTimestamp);
        }

        // 遍历游戏列表，对每个游戏进行统计
        foreach ($gameList as $gameId) {
            // 记录每个游戏的统计时长，用于记录日志，开始时间锚点
            $anchorTime = microtime(true);

            // 对局数据统计主逻辑，按游戏id和日期进行统计
            $lgcRet = $statLgc->statGameRoundByDate($gameId, $statTime);
            if (ERRCODE_SUCCESS !== $lgcRet['code']) {
                $echoMsg = "[Warning] 游戏{$gameId}统计".date('Y-m-d H:i:s', $statTime)."失败：{$lgcRet['msg']}{$cr}";
                $retCode = $cronMod::RET_CODE_WARNING;
                $retData .= $echoMsg;
                continue;
            }

            $timeOffset = round(microtime(true) - $anchorTime, 3);
            $retData .= "[Info] 游戏 {$gameId}，".date('Y-m-d', $statTime)." 统计完毕，id {$lgcRet['data']['id']}，耗时 {$timeOffset} 秒{$cr}";
        }

        $retData .= "[Info] 全部统计完毕{$cr}";

    CRON_END:
        // 脚本结束时间
        $endTime = time();

        echo $retData;

        // 记录定时器流水
        $cronMod->insertSysCronlog($cronType, $startTime, $endTime, $retCode, $retData);

        return true;
    }

    /**
     * 玩法统计定时器，不输入任何参数则统计前一天数据，输入相应参数可以针对指定游戏的指定日期进行统计
     * 执行时间： 每天凌晨一点
     * cron:
     * 0 1 * * * flock -xn /tmp/sad_cron_stat_game_room.lock -c
     *    'php /rootdir/index.php Home/CronStat/statGameRoom > /dev/null 2>&1'
     * 统计指定游戏的指定日期命令：
     *     php index.php Home/CronStat/statGameRoom/gameId/xxxx/date/xxxxxxxx
     * Ex: php index.php Home/CronStat/statGameRoom/gameId/4156/date/20180501
     * @author Carter
     */
    public function statGameRoom()
    {
        /**************************************************
         * 提供两种统计方式：
         * 1.默认，统计所有游戏前一天的数据
         * 2.同时输入 gameId 和 date 参数，统计指定游戏指定日期的数据
         **************************************************/

        $cronMod = new SysCronlogModel();
        $gameMod = new GameModel();
        $statLgc = new StatLogic();

        // 页面模式与命令行模式使用各自的换行符
        if (IS_CLI) {
            $cr = "\n";
        } else {
            $cr = "<br/>";
        }

        // 脚本开始时间
        $startTime = time();

        $cronType = $cronMod::CRON_TYPE_STAT_GAME_ROOM;
        $retCode = $cronMod::RET_CODE_SUCCESS;
        $retData = "[Info] 统计脚本开始统计，时间".date('Y-m-d H:i:s', $startTime).$cr;

        // 获取有效游戏列表，仅获取有效的
        $attr = array('game_status' => $gameMod::GAME_STATUS_ON);
        $field = 'game_id';
        $modRet = $gameMod->queryGameAllList($attr, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retCode = $cronMod::RET_CODE_FAIL;
            $retData = "[Error] 获取有效游戏列表失败：{$modRet['msg']}{$cr}";
            goto CRON_END;
        }
        $gameList = array_column($modRet['data'], 'game_id');

        // 默认统计前一天的数据
        $statTime = strtotime('-1 day midnight', $startTime);

        // 带参场景，游戏id和统计日期必须同时传入
        $paramGameId = I('get.gameId');
        if ($paramGameId) {
            // 游戏必须是有效清单内的，不能随便传一个阿猫阿狗进来
            if (!in_array($paramGameId, $gameList)) {
                $retCode = $cronMod::RET_CODE_WARNING;
                $retData .= "[Warning] 未知游戏 {$paramGameId}，退出统计{$cr}";
                goto CRON_END;
            }
            // 把游戏统计清单替换成传入的单个游戏，后续遍历的时候就只需要去遍历该游戏
            $gameList = array($paramGameId);

            // 如果不传入统计日期或格式不正确，照样把脚本干掉
            $paramStatDate = I('get.date');
            if (empty($paramStatDate)) {
                $retCode = $cronMod::RET_CODE_WARNING;
                $retData .= "[Warning] 未传入统计日期，退出统计{$cr}";
                goto CRON_END;
            }
            $paramStatTimestamp = strtotime($paramStatDate);
            if (false === $paramStatTimestamp) {
                $retCode = $cronMod::RET_CODE_WARNING;
                $retData .= "[Warning] 传入日期 {$paramStatDate} 格式不正确，退出统计{$cr}";
                goto CRON_END;
            }
            $statTime = strtotime('midnight', $paramStatTimestamp);
        }

        // 遍历游戏列表，对每个游戏进行统计
        foreach ($gameList as $gameId) {
            // 记录每个游戏的统计时长，用于记录日志，开始时间锚点
            $anchorTime = microtime(true);

            // 玩法数据统计主逻辑，按游戏id和日期进行统计
            $lgcRet = $statLgc->statGameRoomByDate($gameId, $statTime);
            if (ERRCODE_SUCCESS !== $lgcRet['code']) {
                $echoMsg = "[Warning] 游戏 {$gameId} ".date('Y-m-d', $statTime)." 统计失败：{$lgcRet['msg']}{$cr}";
                $retCode = $cronMod::RET_CODE_WARNING;
                $retData .= $echoMsg;
                continue;
            }

            $timeOffset = round(microtime(true) - $anchorTime, 3);
            $retData .= "[Info] 游戏 {$gameId}，".date('Y-m-d', $statTime)." 统计完毕，";
            if (isset($lgcRet['data']['insertId'])) {
                $retData .= "新增数据id ".implode(', ', $lgcRet['data']['insertId'])."，";
            }
            if (isset($lgcRet['data']['updateId'])) {
                $retData .= "更新数据id ".implode(', ', $lgcRet['data']['updateId'])."，";
            }
            $retData .= "耗时 {$timeOffset} 秒{$cr}";
        }

        $retData .= "[Info] 全部统计完毕{$cr}";

    CRON_END:
        // 脚本结束时间
        $endTime = time();

        echo $retData;

        // 记录定时器流水
        $cronMod->insertSysCronlog($cronType, $startTime, $endTime, $retCode, $retData);

        return true;
    }

    /**
     * 统计每个游戏钻石产出数据，统计到截止当前时间的前一天为止
     * 执行时间： 每天凌晨三点
     * cron:
     * 0 3 * * * flock -xn /tmp/sad_cron_stat_diamond_produce.lock -c
     *    'php /rootdir/index.php Home/CronStat/statDiamondProduce > /dev/null 2>&1'
     * @author Carter
     */
    public function statDiamondProduce()
    {
        $cronMod = new SysCronlogModel();
        $gameMod = new GameModel();
        $dpStatMod = new StatDiamondProduceModel();
        $statLgc = new StatLogic();

        $cronType = $cronMod::CRON_TYPE_SYNC_GAMEMAKEDIAMOND;
        $retCode = $cronMod::RET_CODE_SUCCESS;
        $retData = "";

        // 脚本开始时间
        $startTime = time();

        echo "[Info] 统计脚本开始统计，时间".date('Y-m-d H:i:s', $startTime)."\n";

        // 获取有效游戏列表，仅获取有效的
        $attr = array('game_status' => $gameMod::GAME_STATUS_ON);
        $field = 'game_id';
        $modRet = $gameMod->queryGameAllList($attr, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retCode = $cronMod::RET_CODE_FAIL;
            $retData = '[Error] 获取有效游戏列表失败：'.$modRet['msg'];
            goto CRON_END;
        }
        $gameList = array_column($modRet['data'], 'game_id');

        // 统计截止日期
        $cutoffTime = strtotime(date('Y-m-d'));

        // 若前一天或其后的数据已经被统计，那可能是被命令行测试脚本已经把数据统计出来，那么要清除掉重新统计
        $field = 'id,game_id,stat_time';
        $attr = array('start_time' => strtotime('-1 day', $cutoffTime));
        $modRet = $dpStatMod->queryStatDiamondProduceAllList($attr, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retCode = $cronMod::RET_CODE_FAIL;
            $retData = '[Error] 获取异常统计列表失败：'.$modRet['msg'];
            goto CRON_END;
        }
        if (!empty($modRet['data'])) {
            foreach ($modRet['data'] as $v) {
                $modRet = $dpStatMod->deleteDiamondProduceStatById($v['id']);
                if (ERRCODE_SUCCESS !== $modRet['code']) {
                    $retCode = $cronMod::RET_CODE_FAIL;
                    $retData = "[Error] 删除统计记录{$v['id']}失败：{$modRet['msg']}";
                    goto CRON_END;
                }

                $echoMsg = "[Warning] 游戏{$v['game_id']}于".date('Y-m-d', $v['stat_time'])."已统计数据，清除 {$v['id']}记录\n";
                $retCode = $cronMod::RET_CODE_WARNING;
                $retData .= $echoMsg;
            }
        }

        // 遍历游戏列表，对每个游戏进行统计，只统计当前时间的前一天到数据库中最后一次统计日期之间的
        foreach ($gameList as $gameId) {
            // 获取钻石产出统计表中最后一天统计的时间
            $lgcRet = $statLgc->getDiamondProduceLastStatTime($gameId);
            if (ERRCODE_SUCCESS !== $lgcRet['code']) {
                $retCode = $cronMod::RET_CODE_WARNING;
                $retData .= "[Warning] 获取游戏{$gameId}最后统计时间失败：{$lgcRet['msg']}";
                goto CRON_END;
            }
            $lastStatTime = $lgcRet['data'];

            echo "[Info] 开始统计游戏 {$gameId}，该游戏最后统计时间为".date('Y-m-d H:i:s', $lastStatTime)."\n";

            $statTime = $lastStatTime + 86400;
            while ($statTime < $cutoffTime) {
                $lgcRet = $statLgc->statDiamondProduceByGameAndTime($gameId, $statTime);
                if (ERRCODE_SUCCESS !== $lgcRet['code']) {
                    $echoMsg = "[Warning] 游戏{$gameId}统计".date('Y-m-d H:i:s', $statTime)."失败：{$lgcRet['msg']}\n";
                    echo $echoMsg;
                    $retCode = $cronMod::RET_CODE_WARNING;
                    $retData .= $echoMsg;
                    goto CRON_END;
                } else {
                    $echoMsg = "[Info] 游戏 {$gameId}，".date('Y-m-d', $statTime)." 统计完毕，id {$lgcRet['data']}\n";
                    echo $echoMsg;
                    $retData .= $echoMsg;
                }

                $statTime += 86400;
            }
        }

        echo "[Info] 全部统计完毕\n";

    CRON_END:
        // 脚本结束时间
        $endTime = time();

        // 记录定时器流水
        $cronMod->insertSysCronlog($cronType, $startTime, $endTime, $retCode, $retData);

        return true;
    }

    /**
     * 统计每个游戏钻石消耗数据，统计到截止当前时间的前一天为止
     * 执行时间： 每天凌晨两点
     * cron:
     * 0 2 * * * flock -xn /tmp/sad_cron_stat_diamond_consume_4156.lock -c
     *    'php /rootdir/index.php Home/CronStat/statDiamondConsume/game_id/4156 > /dev/null 2>&1'
     * 统计指定游戏指定日期：
     * php /rootdir/index.php Home/CronStat/statDiamondConsume/game_id/4156/start/2018-03-01/end/2018-03-01
     * @author tangjie
     */
    public function statDiamondConsume()
    {
        $cronMod = new SysCronlogModel(); //定时任务日志model
        $statLgc = new StatLogic();       //数据统计逻辑

        // 脚本开始时间
        $cronExecStartTime = time();

        // 游戏产品ID
        $gameId = (int) I('get.game_id') ;

        // 定时任务开始时间、结束时间
        $getStartTime = I('get.start')  ;
        $getEndTime = I('get.end') ;

        // 必填参数判断
        if ($gameId  == 0) {
            $logCode = $cronMod::RET_CODE_WARNING;
            $logMsg = '[Warning]'.$_SERVER['SERVER_ADDR'].'定时任务缺少必填参数gameId,任务终止未执行';
            set_exception(__FILE__, __LINE__, "[statDiamondConsume] {$gameId} {$logMsg}");
            goto CRON_END ;
        }

        if ($getStartTime) {
            $cronStartTime = strtotime($getStartTime) ? strtotime($getStartTime) : strtotime(date('Y-m-d', time())); // 开始时间：格式2017-06-03
            $cronEndTime = strtotime($getEndTime) ? strtotime($getEndTime) + 86399 : $cronStartTime + 86399; // 统计结束时间 格式2017-06-05
        } else {
            $cronStartTime = strtotime(date('Y-m-d 00:00:00', strtotime('-1 day')));
            $cronEndTime = $cronStartTime + 86399 ;
        }

        // 统计时间
        if ($cronEndTime < $cronStartTime) {
            $logCode = $cronMod::RET_CODE_WARNING;
            $logMsg = '[Warning]'.$_SERVER['SERVER_ADDR'].'统计时间区间不正确，开始时间：'.date('Y-m-d H:i:s',$cronStartTime) .'-'.date('Y-m-d H:i:s',$cronEndTime);
            set_exception(__FILE__, __LINE__, "[statDiamondConsume] {$gameId} {$logMsg}");
            goto CRON_END ;
        }

        // 定时任务执行时间跨度有多少天
        $dayNumber = ceil(($cronEndTime - $cronStartTime) / 86400);

        try {
            //按天循环统计数据
            for($i = 0 ; $i < $dayNumber ;$i ++  ){

                $startTime = $cronStartTime + $i * 86400 ; //当天开始时间
                $endTime = $startTime + 86399 ;

                $result = $statLgc->statGameDiamondLogic($gameId, $startTime, $endTime);
                if($result['code'] === ERRCODE_SYSTEM ){
                    $logCode = $cronMod::RET_CODE_FAIL ; //致命错误
                    $logMsg = '[Fail]'.$_SERVER['SERVER_ADDR'].'游戏ID:'.$gameId.'定时任务执行错误：'.$result['msg'] ;  //致命日志
                    set_exception(__FILE__, __LINE__, " statDiamondConsume {$gameId}".$logMsg );
                }else if($result['code'] === ERRCODE_SUCCESS){
                    $logCode = $cronMod::RET_CODE_SUCCESS ; //执行完成
                    $logMsg = '[Success]'.$_SERVER['SERVER_ADDR'].'游戏ID:'.$gameId.'定时任务执行结束，最终更新SQL：'.$result['msg'] ;  //完成日志
                }else{
                    $logCode = $cronMod::RET_CODE_WARNING ; //异常
                    $logMsg = '[Warning]'.$_SERVER['SERVER_ADDR'].'游戏ID:'.$gameId.'定时任务执行异常：'.$result['msg'] ;  //异常日志
                    set_exception(__FILE__, __LINE__, " statDiamondConsume {$gameId}".$logMsg );
                }
            }
        }  catch (\Exception $e){
            $logCode = $cronMod::RET_CODE_FAIL ; //失败
            $logMsg = '[Fail] '.$_SERVER['SERVER_ADDR'].'游戏ID:'.$gameId.'，时间:'.date('Y-m-d H:i:s',$cronStartTime) .'-'.date('Y-m-d H:i:s',$cronEndTime).$e->getMessage() ;  //异常日志
            set_exception(__FILE__, __LINE__, " statDiamondConsume {$gameId}".$logMsg );
        }

    CRON_END:
        // 脚本结束时间
        $cronExecEndTime = time();

        // 记录定时器流水
        $cronMod->insertSysCronlog($cronMod::CRON_TYPE_SYNC_GAMEDIAMOND , $cronExecStartTime, $cronExecEndTime, $logCode, $logMsg);

        return true;
    }

    /**
     * 统计每个游戏用户排行数据，统计到截止当前时间的前一天为止
     * 执行时间： 每天凌晨四点
     * cron:
     * 0 4 * * * flock -xn /tmp/sad_cron_stat_user_rank.lock -c
     *    'php /rootdir/index.php Home/CronStat/statUserRank > /dev/null 2>&1'
     * @author Carter
     */
    public function statUserRank()
    {
        $cronMod = new SysCronlogModel();
        $gameMod = new GameModel();
        $urStatMod = new StatUserRankModel();
        $statLgc = new StatLogic();

        // 页面模式与命令行模式使用各自的换行符
        if (IS_CLI) {
            $cr = "\n";
        } else {
            $cr = "<br/>";
        }

        $cronType = $cronMod::CRON_TYPE_STAT_USER_GAME_RANK;
        $retCode = $cronMod::RET_CODE_SUCCESS;
        $retData = "";

        // 脚本开始时间
        $startTime = time();

        $retData = "[Info] 统计脚本开始统计，时间 ".date('Y-m-d H:i:s', $startTime)."{$cr}";

        // 获取有效游戏列表，仅获取有效的
        $attr = array('game_status' => $gameMod::GAME_STATUS_ON);
        $field = 'game_id';
        $modRet = $gameMod->queryGameAllList($attr, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retCode = $cronMod::RET_CODE_FAIL;
            $retData .= '[Error] 获取有效游戏列表失败：'.$modRet['msg'];
            goto CRON_END;
        }
        $gameList = array_column($modRet['data'], 'game_id');

        // 大圣南京、老淮北和大圣不进行统计
        $idx = array_search('3425', $gameList);
        if (false !== $idx) {
            unset($gameList[$idx]);
        }
        $idx = array_search('10020', $gameList);
        if (false !== $idx) {
            unset($gameList[$idx]);
        }
        $idx = array_search('5542', $gameList);
        if (false !== $idx) {
            unset($gameList[$idx]);
        }

        // 统计截止日期
        $cutoffTime = strtotime(date('Y-m-d'));

        // 遍历游戏列表，对每个游戏进行统计，只统计当前时间的前一天到数据库中最后一次统计日期之间的
        foreach ($gameList as $gameId) {
            // 若前一天或其后的数据已经被统计，那可能是被命令行测试脚本已经把数据统计出来，那么要清除掉重新统计
            $attr = array('game_id' => $gameId, 'start_time' => strtotime('-1 day', $cutoffTime));
            $modRet = $urStatMod->queryStatUserRankAllList($attr, 'id,data_time');
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $retCode = $cronMod::RET_CODE_FAIL;
                $retData .= '[Error] 获取异常统计列表失败：'.$modRet['msg'];
                goto CRON_END;
            }
            if (!empty($modRet['data'])) {
                foreach ($modRet['data'] as $v) {
                    $uModRet = $urStatMod->deleteUserRankStatById($v['id']);
                    if (ERRCODE_SUCCESS !== $uModRet['code']) {
                        $retCode = $cronMod::RET_CODE_FAIL;
                        $retData .= "[Error] 删除统计记录{$v['id']}失败：{$uModRet['msg']}";
                        goto CRON_END;
                    }
                }
                $retCode = $cronMod::RET_CODE_WARNING;
                $retData .= "[Warning] 游戏{$gameId}于".date('Y-m-d', $v['data_time'])."已统计数据，清除 ".count($modRet['data'])."行记录{$cr}";
            }

            // 获取用户排行榜统计表中最后一天统计的时间
            $modRet = $urStatMod->queryStatUserRankLastTime($gameId);
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $retCode = $cronMod::RET_CODE_WARNING;
                $retData .= "[Warning] 获取游戏{$gameId}最后统计时间失败：{$modRet['msg']}";
                goto CRON_END;
            }
            if (empty($modRet['data'])) {
                $lastStatTime = strtotime('-2 days', $cutoffTime);
            } else {
                $lastStatTime = $modRet['data']['data_time'];
            }
            $retData .= "[Info] 开始统计游戏 {$gameId}，该游戏最后统计时间为".date('Y-m-d H:i:s', $lastStatTime).$cr;

            $statTime = $lastStatTime + 86400;
            while ($statTime < $cutoffTime) {
                $lgcRet = $statLgc->statUserRankByGameAndTime($gameId, $statTime);
                if (ERRCODE_SUCCESS !== $lgcRet['code']) {
                    $retCode = $cronMod::RET_CODE_WARNING;
                    $retData .= "[Warning] 游戏{$gameId}统计".date('Y-m-d H:i:s', $statTime)."失败：{$lgcRet['msg']}{$cr}";
                    goto CRON_END;
                } else {
                    $retData .= "[Info] 游戏 {$gameId}，".date('Y-m-d', $statTime)." 统计完毕，插入".count($lgcRet['data'])."行数据{$cr}";
                }

                $statTime += 86400;
            }
        }

        $retData .= "[Info] 全部统计完毕{$cr}";

    CRON_END:
        // 脚本结束时间
        $endTime = time();

        // 记录定时器流水
        $cronMod->insertSysCronlog($cronType, $startTime, $endTime, $retCode, $retData);
        echo $retData;

        return true;
    }

    /**
     * 分享场景用户数据统计
     * 执行时间：每天凌晨三点半
     * cron:
     * 30 3 * * * flock -xn /tmp/sad_cron_stat_game_share.lock -c
     *    'php /rootdir/index.php Home/CronStat/statGameShare > /dev/null 2>&1'
     */
    public function statGameShare()
    {
        $cronMod = new SysCronlogModel();
        $gameMod = new GameModel();
        $shStatMod = new StatGameShareModel();
        $statLgc = new StatLogic();

        // 页面模式与命令行模式使用各自的换行符
        if (IS_CLI) {
            $cr = "\n";
        } else {
            $cr = "<br/>";
        }

        $cronType = $cronMod::CRON_TYPE_STAT_USER_GAME_SHARE;
        $retCode = $cronMod::RET_CODE_SUCCESS;
        $retData = "";

        // 脚本开始时间
        $startTime = time();

        $retData = "[Info] 统计脚本开始统计，时间".date('Y-m-d H:i:s', $startTime).$cr;

        // 获取有效游戏列表，仅获取有效的
        $attr = array('game_status' => $gameMod::GAME_STATUS_ON);
        $field = 'game_id';
        $modRet = $gameMod->queryGameAllList($attr, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retCode = $cronMod::RET_CODE_FAIL;
            $retData .= "[Error] 获取有效游戏列表失败：{$modRet['msg']}{$cr}";
            goto CRON_END;
        }
        $gameList = array_column($modRet['data'], 'game_id');

        // 统计截止日期
        $cutoffTime = strtotime(date('Y-m-d'));

        // 若前一天或其后的数据已经被统计，那可能是被命令行测试脚本已经把数据统计出来，那么要清除掉重新统计
        $field = 'id,game_id,stat_time';
        $attr = array('start_time' => strtotime('-1 day', $cutoffTime));
        $modRet = $shStatMod->queryStatGameShareAllList($attr, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retCode = $cronMod::RET_CODE_FAIL;
            $retData .= "[Error] 获取异常统计列表失败：{$modRet['msg']}{$cr}";
            goto CRON_END;
        }
        if (!empty($modRet['data'])) {
            foreach ($modRet['data'] as $v) {
                $modRet = $shStatMod->deleteGameShareStatById($v['id']);
                if (ERRCODE_SUCCESS !== $modRet['code']) {
                    $retCode = $cronMod::RET_CODE_FAIL;
                    $retData .= "[Error] 删除统计记录{$v['id']}失败：{$modRet['msg']}{$cr}";
                    goto CRON_END;
                }

                $retCode = $cronMod::RET_CODE_WARNING;
                $retData .= "[Warning] 游戏{$v['game_id']}于".date('Y-m-d', $v['stat_time'])."已统计数据，清除 {$v['id']}记录{$cr}";
            }
        }

        // 遍历游戏列表，对每个游戏进行统计，只统计当前时间的前一天到数据库中最后一次统计日期之间的
        foreach ($gameList as $gameId) {
            // 获取钻石产出统计表中最后一天统计的时间
            $modRet = $shStatMod->queryStatGameShareLastTime($gameId);
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $retCode = $cronMod::RET_CODE_WARNING;
                $retData .= "[Warning] 获取游戏{$gameId}最后统计时间失败：{$modRet['msg']}{$cr}";
                goto CRON_END;
            }
            // 未存在则从前三天开始统计
            if (empty($modRet['data'])) {
                $lastStatTime = strtotime('-4 days', $cutoffTime);
            } else {
                $lastStatTime = $modRet['data']['stat_time'];
            }

            $retData .= "[Info] 开始统计游戏 {$gameId}，该游戏最后统计时间为".date('Y-m-d H:i:s', $lastStatTime).$cr;

            $statTime = $lastStatTime + 86400;
            while ($statTime < $cutoffTime) {
                $lgcRet = $statLgc->statGameShareData($gameId, $statTime);
                if (ERRCODE_SUCCESS !== $lgcRet['code']) {
                    $retCode = $cronMod::RET_CODE_WARNING;
                    $retData .= "[Warning] 游戏{$gameId}统计".date('Y-m-d H:i:s', $statTime)."失败：{$lgcRet['msg']}{$cr}";
                    goto CRON_END;
                } else {
                    $retData .= "[Info] 游戏 {$gameId}，".date('Y-m-d', $statTime)." 统计完毕，id {$lgcRet['data']}{$cr}";
                }

                $statTime += 86400;
            }
        }

        $retData .= "[Info] 全部统计完毕{$cr}";

    CRON_END:
        // 脚本结束时间
        $endTime = time();

        echo $retData;

        // 记录定时器流水
        $cronMod->insertSysCronlog($cronType, $startTime, $endTime, $retCode, $retData);

        return true;
    }
}
