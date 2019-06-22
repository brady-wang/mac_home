<?php
namespace Home\Controller;

use Common\Service\DbLoadConfigService;
use Home\Logic\StatLogic;
use Home\Model\Club\PromoterModel;
use Home\Model\GameModel;
use Home\Model\StatClubPromoterModel;
use Home\Model\StatDiamondProduceModel;
use Home\Model\StatGameShareModel;
use Home\Model\StatUserDailyModel;
use Home\Model\SysCacheModel;

class CliController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        // 正式服仅支持CLI模式
        if ('production' === APP_STATUS) {
            $this->checkIsCli();
        }

        ignore_user_abort();
        set_time_limit(0);
    }

    /**
     * 统计钻石产出指定日期的统计数据（供测试用，不建议正式服用）
     * php index.php Home/Cli/statDiamondProduceTodayDataForTest/gameId/[game id]/statDate/[date]
     * 不填第二个参数表示统计当天的
     * php index.php Home/Cli/statDiamondProduceTodayDataForTest/gameId/[game id]
     * @author Carter
     */
    public function statDiamondProduceTodayDataForTest()
    {
        $gameMod = new GameModel();
        $statLgc = new StatLogic();

        // 页面模式与命令行模式使用各自的换行符
        if (IS_CLI) {
            $cr = "\n";
        } else {
            $cr = "<br/>";
        }

        // 输入参数
        $param = I('get.');

        if (empty($param['gameId'])) {
            echo "[Error] 游戏id不能为空{$cr}";
            exit;
        }
        $gameId = $param['gameId'];

        // 判断日期参数，若无该参数则统计当天的
        if (isset($param['statDate']) && strtotime($param['statDate'])) {
            $statTime = strtotime(date('Y-m-d', strtotime($param['statDate'])));
        } else {
            $statTime = strtotime(date('Y-m-d'));
        }

        echo "[Info] 脚本开始执行，统计日期".date('Y-m-d', $statTime)."{$cr}";

        // 判断游戏有效性
        $modRet = $gameMod->queryGameInfoById($gameId, 'game_status');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            echo "[Error] {$modRet['msg']}{$cr}";
            exit;
        }
        if (empty($modRet['data'])) {
            echo "[Error] 无效的游戏id {$gameId}，后台不存在该游戏{$cr}";
            exit;
        }
        if ($gameMod::GAME_STATUS_ON != $modRet['data']['game_status']) {
            echo "[Error] 游戏状态{$modRet['data']['game_status']}有误，只有正常状态可以进行统计{$cr}";
            exit;
        }

        $lgcRet = $statLgc->statDiamondProduceByGameAndTime($gameId, $statTime);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            echo "[Error] 统计失败：{$lgcRet['msg']}{$cr}";
            exit;
        }

        echo "[Info] 游戏{$gameId}，".date('Y-m-d', $statTime)."统计完毕，id {$lgcRet['data']}{$cr}";

        echo "[Info] 脚本执行完毕{$cr}";
        exit;
    }

    /**
     * 删除最后一天的钻石产出记录，为了防止统计出现日期断层，只能从最后日期一天一天删
     * php index.php Home/Cli/deleteLastDiamondProduceData/gameId/[game id]
     * @author Carter
     */
    public function deleteLastDiamondProduceData($gameId)
    {
        $gameMod = new GameModel();
        $statMod = new StatDiamondProduceModel();

        // 页面模式与命令行模式使用各自的换行符
        if (IS_CLI) {
            $cr = "\n";
        } else {
            $cr = "<br/>";
        }

        echo "[Info] 脚本开始执行{$cr}";

        if (empty($gameId)) {
            echo "[Error] 游戏id不能为空{$cr}";
            exit;
        }

        // 判断游戏有效性
        $modRet = $gameMod->queryGameInfoById($gameId, 'game_status');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            echo "[Error] {$modRet['msg']}{$cr}";
            exit;
        }
        if (empty($modRet['data'])) {
            echo "[Error] 无效的游戏id {$gameId}，后台不存在该游戏{$cr}";
            exit;
        }

        $modRet = $statMod->queryStatDiamondProduceLastTime($gameId);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            echo "[Error] 获取最后一天数据失败：{$modRet['msg']}{$cr}";
            exit;
        }
        $lastDateId = $modRet['data']['id'];
        $lastDate = date('Y-m-d', $modRet['data']['stat_time']);

        $modRet = $statMod->deleteDiamondProduceStatById($lastDateId);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            echo "[Error] 删除最后一天数据失败：{$modRet['msg']}{$cr}";
            exit;
        }

        echo "[Info] 游戏{$gameId}，删除最后一天{$lastDate}统计数据，id {$lastDateId}{$cr}";

        echo "[Info] 脚本执行完毕{$cr}";
        exit;
    }

    /**
     * 初始化市场数据代理概况的数据表，当接入一个新游戏时，该游戏的代理概况是空的，要做一次初始化才能开始正常定时统计，每次初始化只统计有新增代理的那一天
     * php index.php Home/Cli/initStatClubPromoterData/gameId/[game id]
     *   例：
     *   php index.php Home/Cli/initStatClubPromoterData/gameId/4156
     * @author Carter
     */
    public function initStatClubPromoterData($gameId)
    {
        // 页面模式与命令行模式使用各自的换行符
        if (IS_CLI) {
            $cr = "\n";
        } else {
            $cr = "<br/>";
        }

        $gameMod = new GameModel();
        $statMod = new StatClubPromoterModel();
        $statLgc = new StatLogic();
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load($gameId, 'CONF_DBTYPE_CLUB', 0)) {
            try {
                $prmMod = new PromoterModel();
            } catch (\Exception $e) {
                $conMsg = "CONF_DBTYPE_CLUB ".var_export(C('CONF_DBTYPE_CLUB'), true);
                echo "[Error] 数据库连接失败，错误信息：".$e->getMessage().", {$conMsg}{$cr}";
                exit;
            }
        } else {
            echo "[Error] 数据库配置加载失败，请确认数据库配置信息{$cr}";
            exit;
        }

        echo "[Info] 脚本开始执行{$cr}";

        $anchorTime = microtime(true);

        if (empty($gameId)) {
            echo "[Error] 游戏id不能为空{$cr}";
            exit;
        }

        // 判断游戏有效性
        $modRet = $gameMod->queryGameInfoById($gameId, 'game_status');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            echo "[Error] {$modRet['msg']}{$cr}";
            exit;
        }
        if (empty($modRet['data'])) {
            echo "[Error] 无效的游戏id {$gameId}，后台不存在该游戏{$cr}";
            exit;
        }
        if ($gameMod::GAME_STATUS_ON != $modRet['data']['game_status']) {
            echo "[Error] {$gameId}游戏为下架状态{$cr}";
            exit;
        }

        // 判断该游戏是否已经统计记录，已存在记录的不能重复进行数据初始化
        $modRet = $statMod->queryStatPromoterLastRow($gameId, 'id');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            echo "[Error] {$modRet['msg']}{$cr}";
            exit;
        }
        if (!empty($modRet['data'])) {
            echo "[Warning] {$gameId} 已存在统计记录，不能重复进行初始化进程{$cr}";
            exit;
        }

        // 查询首条代理新增日期，作为初始化统计的日期
        $modRet = $prmMod->queryClubPromoterFirstRow($gameId);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            echo "[Error] {$modRet['msg']}{$cr}";
            exit;
        }
        if (empty($modRet['data'])) {
            echo "[Warning] {$gameId} 游戏未有任何代理商，初始化进程不再继续进行{$cr}";
            exit;
        }
        // 统计日期
        $statDate = $modRet['data']['createDate'];

        $lastStat = array(
            'promoter_amount' => 0,
            'transfer_amount' => 0,
            'active_promoter' => 0,
            'recharge_amount' => 0,
            'promoter_count' => 0,
            'transfer_count' => 0,
            'effective_transfer' => 0,
            'effective_active' => 0,
            'effective_recharge' => 0,
            'club_active' => 0,
            'club_recharge' => 0,
            'retail_active' => 0,
            'retail_recharge' => 0,
        );

        // 统计数据
        $lgcRet = $statLgc->statMarketClubPromoter($lastStat, $gameId, $statDate);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            echo "[Error] {$lgcRet['msg']}{$cr}";
            exit;
        }

        $timeOffset = round(microtime(true) - $anchorTime, 3);

        echo "[Info] 游戏{$gameId} 数据初始化完毕，统计id {$lgcRet['data']['id']}，耗时 {$timeOffset} 秒{$cr}";

        echo "[Info] 脚本执行完毕{$cr}";
        exit;
    }

    /**
     * 该工具仅供测试用，切莫在线上执行。用于刷新今日目前为止代理数统计数据
     * php index.php Home/Cli/updateTodayClubPromoterStatData/gameId/[game id]
     *   例：
     *   php index.php Home/Cli/updateTodayClubPromoterStatData/gameId/4156
     * @author Carter
     */
    public function updateTodayClubPromoterStatData($gameId)
    {
        // 页面模式与命令行模式使用各自的换行符
        if (IS_CLI) {
            $cr = "\n";
        } else {
            $cr = "<br/>";
        }

        $gameMod = new GameModel();
        $statMod = new StatClubPromoterModel();
        $statLgc = new StatLogic();

        echo "[Info] 脚本开始执行{$cr}";

        $anchorTime = microtime(true);

        // 统计日期
        $statDate = date('Y-m-d');

        if (empty($gameId)) {
            echo "[Error] 游戏id不能为空{$cr}";
            exit;
        }

        // 判断游戏有效性
        $modRet = $gameMod->queryGameInfoById($gameId, 'game_status');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            echo "[Error] {$modRet['msg']}{$cr}";
            exit;
        }
        if (empty($modRet['data'])) {
            echo "[Error] 无效的游戏id {$gameId}，后台不存在该游戏{$cr}";
            exit;
        }
        if ($gameMod::GAME_STATUS_ON != $modRet['data']['game_status']) {
            echo "[Error] {$gameId}游戏为下架状态{$cr}";
            exit;
        }

        // 判断该游戏是否已存在今日数据
        $modRet = $statMod->queryStatPromoterLastRow($gameId, '*');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            echo "[Error] {$modRet['msg']}{$cr}";
            exit;
        }
        if (empty($modRet['data'])) {
            echo "[Warning] {$gameId} 未存在任何统计记录，要先进行初始化进程 Home/Cli/initStatClubPromoterData{$cr}";
            exit;
        }
        $lastStat = $modRet['data'];

        // 今日数据已存在，删除今日数据然后重新获取昨日数据进行统计
        if ($lastStat['stat_date'] == $statDate) {
            $modRet = $statMod->deleteStatClubPromoterById($lastStat['id']);
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                echo "[Error] {$modRet['msg']}{$cr}";
                exit;
            }
            $modRet = $statMod->queryStatPromoterLastRow($gameId, '*');
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                echo "[Error] {$modRet['msg']}{$cr}";
                exit;
            }
            $lastStat = $modRet['data'];
        }
        // 不存在今日数据，但是最后一条记录也不是昨日数据，要先执行定时器脚本把数据刷全
        else if ($lastStat['stat_date'] != date('Y-m-d', strtotime('-1 day', strtotime($statDate)))) {
            echo "[Error] 目前最近一条统计不是昨日数据，需要执行 Home/CronStat/statClubPromoter 把统计数据刷连续{$cr}";
            exit;
        }

        // 统计数据
        $lgcRet = $statLgc->statMarketClubPromoter($lastStat, $gameId, $statDate);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            echo "[Error] {$lgcRet['msg']}{$cr}";
            exit;
        }

        $timeOffset = round(microtime(true) - $anchorTime, 3);

        echo "[Info] 游戏{$gameId} 数据初始化完毕，统计id {$lgcRet['data']['id']}，耗时 {$timeOffset} 秒{$cr}";

        echo "[Info] 脚本执行完毕{$cr}";
        exit;
    }

    /**
     * 该工具仅供测试用，切莫在线上执行。用于刷新当日用户分享统计数据
     * php index.php Home/Cli/updateTodayGameShareStatData/gameId/[game id]
     *   例：
     *   php index.php Home/Cli/updateTodayGameShareStatData/gameId/4156
     * @author Carter
     */
    public function updateTodayGameShareStatData($gameId)
    {
        // 页面模式与命令行模式使用各自的换行符
        if (IS_CLI) {
            $cr = "\n";
        } else {
            $cr = "<br/>";
        }

        $gameMod = new GameModel();
        $statMod = new StatGameShareModel();
        $statLgc = new StatLogic();

        echo "[Info] 脚本开始执行{$cr}";

        $anchorTime = microtime(true);

        // 统计日期
        $statTime = strtotime(date('Y-m-d'));

        if (empty($gameId)) {
            echo "[Error] 游戏id不能为空{$cr}";
            exit;
        }

        // 判断游戏有效性
        $modRet = $gameMod->queryGameInfoById($gameId, 'game_status');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            echo "[Error] {$modRet['msg']}{$cr}";
            exit;
        }
        if (empty($modRet['data'])) {
            echo "[Error] 无效的游戏id {$gameId}，后台不存在该游戏{$cr}";
            exit;
        }
        if ($gameMod::GAME_STATUS_ON != $modRet['data']['game_status']) {
            echo "[Error] {$gameId}游戏为下架状态{$cr}";
            exit;
        }

        // 判断该游戏是否已存在今日数据
        $modRet = $statMod->queryStatGameShareLastTime($gameId);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            echo "[Error] {$modRet['msg']}{$cr}";
            exit;
        }
        if (empty($modRet['data'])) {
            echo "[Warning] {$gameId} 未存在任何统计记录，要先进行初始化统计 Home/CronStat/statGameShare{$cr}";
            exit;
        }
        $lastStat = $modRet['data'];

        // 今日数据已存在，删除今日数据然后重新获取昨日数据进行统计
        if ($lastStat['stat_time'] == $statTime) {
            $modRet = $statMod->deleteGameShareStatById($lastStat['id']);
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                echo "[Error] {$modRet['msg']}{$cr}";
                exit;
            }
        }
        // 不存在今日数据，但是最后一条记录也不是昨日数据，要先执行定时器脚本把数据刷全
        else if ($lastStat['stat_time'] != strtotime('-1 day', $statTime)) {
            echo "[Error] 目前最近一条统计不是昨日数据，需要执行 Home/CronStat/statClubPromoter 把统计数据刷连续{$cr}";
            exit;
        }

        // 统计数据
        $lgcRet = $statLgc->statGameShareData($gameId, $statTime);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            echo "[Error] {$lgcRet['msg']}{$cr}";
            exit;
        }

        $timeOffset = round(microtime(true) - $anchorTime, 3);

        echo "[Info] 游戏{$gameId} 数据初始化完毕，统计id {$lgcRet['data']['id']}，耗时 {$timeOffset} 秒{$cr}";

        echo "[Info] 脚本执行完毕{$cr}";
        exit;
    }

    /**
     * 初始化用户统计每日简报的数据表，当接入一个新游戏时，该游戏的每日简报是空的，要做一次初始化才能开始正常定时统计
     * php index.php Home/Cli/initStatUserDailyData/gameId/[game id]/startDate/[date]
     *   例：
     *   php index.php Home/Cli/initStatUserDailyData/gameId/4156/startDate/2018-01-01
     * @author Carter
     */
    public function initStatUserDailyData($gameId, $startDate)
    {
        $cacheMod = new SysCacheModel();
        $gameMod = new GameModel();
        $statMod = new StatUserDailyModel();
        $statLgc = new StatLogic();

        ini_set('memory_limit', '2G');

        // 页面模式与命令行模式使用各自的换行符
        if (IS_CLI) {
            $cr = "\n";
        } else {
            $cr = "<br/>";
        }

        echo "[Info] 脚本开始执行{$cr}";

        if (empty($gameId)) {
            echo "[Error] 游戏id不能为空{$cr}";
            exit;
        }

        if (empty($startDate) || !strtotime($startDate)) {
            echo "[Error] 必须输入有效日期{$cr}";
            exit;
        }

        // 判断游戏有效性
        $modRet = $gameMod->queryGameInfoById($gameId, 'game_status');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            echo "[Error] {$modRet['msg']}{$cr}";
            exit;
        }
        if (empty($modRet['data'])) {
            echo "[Error] 无效的游戏id {$gameId}，后台不存在该游戏{$cr}";
            exit;
        }

        // 检查统计表是否已经存在数据，存在的表示数据已经初始化过，不再重复进行
        $modRet = $statMod->queryStatDailyLatestInfo($gameId);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            echo "[Error] {$modRet['msg']}{$cr}";
            exit;
        }
        if (!empty($modRet['data'])) {
            echo "[Warning] {$gameId} 游戏已经对每日简报统计数据做过初始化，不再重复{$cr}";
            exit;
        }

        // 统计开始时间
        $statStartTime = strtotime(date('Y-m-d', strtotime($startDate)));

        // 截止时间统计到昨天为止，不要去统计当天数据
        $statCutoffTime = strtotime('today') - 1;

        // 统计数据
        $lgcRet = $statLgc->statUserDailyByTime($gameId, $statStartTime, $statCutoffTime);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            echo "[Error] {$lgcRet['msg']}{$cr}";
            exit;
        }

        // 记录CacheTime
        $cacheKey = 'DBCACHE_STAT_USER_DAILY_CUTOFFTIME';
        $remark = '数据统计用户每日简报最近统计时间的时间戳';
        $modRet = $cacheMod->exceSetSysCache($gameId, $cacheKey, $statCutoffTime, $remark);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            echo "[Error] {$modRet['msg']}{$cr}";
            exit;
        }

        echo "[Info] 游戏{$gameId} 数据初始化完毕{$cr}";

        echo "[Info] 脚本执行完毕{$cr}";
        exit;
    }

    /**
     * 初始化房间配置的分享内容，仅在发布房间配置防屏蔽分享功能时使用一次，数据修复后删除脚本
     * 临时脚本，所有资源都集中在本方法里面，以后删除的时候一锅端，不用额外去考虑其他耦合代码
     *
     * php index.php Home/Cli/initRoomShareConf
     *
     * @author Carter
     */
    public function initRoomShareConf()
    {
        $mod = new \Think\Model();
        $apiSer = new \Common\Service\ApiService();
        $confSer = new \Common\Service\DbLoadConfigService();

        $resServer = C('RESOURCE_SERVER_IPHOST').':'.C('RESOURCE_SERVER_PORT');
        $fileDir = ROOT_PATH."FileUpload";

        $gameList = $mod->table('sad_game')->where(array('game_status' => 1))->select();
        foreach ($gameList as $v) {
            $gameId = $v['game_id'];
            if (true === $confSer->load($gameId, 'GAME_DICT_DB', 0)) {
                try {
                    $gameMod = new \Home\Model\DsqpDict\DictPlaceGameModel();
                    $placeMod = new \Home\Model\DsqpDict\DictPlaceModel();
                } catch (\Exception $e) {
                    echo "[Error] ".$e->getMessage()."\n";
                    exit;
                }
            } else {
                echo "[Error] svr load failed\n";
                exit;
            }

            $modRet = $placeMod->queryDsqpPlaceListByFirstId($gameId, 'placeID');
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                echo "[Error] queryDsqpPlaceListByFirstId failed {$modRet['msg']}\n";
                exit;
            }
            $placeArr = array_column($modRet['data'], 'placeID');

            $modRet = $gameMod->queryDsqpPlaceGameByPlaceId($placeArr, 'gameId,roomShare');
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                echo "[Error] queryDsqpPlaceGameByPlaceId failed {$modRet['msg']}\n";
                exit;
            }
            $dsqpList = $modRet['data'];

            $attr = array(
                'game_id' => $gameId,
                'share_type' => 1,
            );
            $share = $mod->table('sad_game_share')->where($attr)->find();

            foreach ($dsqpList as $d) {
                $w = array(
                    'game_id' => $gameId,
                    'source' => 5,
                    'play_id' => $d['gameId'],
                );
                $i = $mod->table('sad_game_share')->where($w)->find();
                if ($i) {
                    continue;
                }

                // 资源服上图片要重新复制一张，共用图片的话，删除配置的时候容易起冲突，不好管理
                $savename = uniqid().".".array_pop(explode('.', basename($share['image'])));
                $source = "{$resServer}/{$share['image']}";
                $dest = "{$fileDir}/{$savename}";
                if (!copy($source, $dest)) {
                    echo "[Error] copy {$dest} from {$source} failed\n";
                    exit;
                }

                // 上传缩略图到资源服
                $svrPath = "Admin/GameShare/Image/".$savename;
                $serRet = $apiSer->resourceServerUploadImg($svrPath, $dest);
                if (ERRCODE_SUCCESS !== $serRet['code']) {
                    echo "[Error] resourceServerUploadImg failed {$serRet['msg']}\n";
                    exit;
                }

                $roomShare = json_decode($d['roomShare'], true);
                if (false == $roomShare) {
                    echo "[Error] invalid room share {$d['roomShare']}\n";
                    exit;
                }

                $inserData = array(
                    'game_id' => $gameId,
                    'place_id' => 0,
                    'play_id' => $d['gameId'],
                    'source' => 5,
                    'share_type' => 1,
                    'title' => $roomShare['shareTitle'],
                    'desc' => $roomShare['shareDesc'],
                    'image' => $svrPath,
                    'address' => '',
                    'qrcode_x' => 0,
                    'qrcode_y' => 0,
                );
                $id = $mod->table('sad_game_share')->add($inserData);
                echo "[Info] insert {$id}, play id {$d['gameId']}\n";
            }
        }

        echo "[Info] success\n";
        exit;
    }

    /**
     * 初始化好友/群配置的分享内容，仅在发布好友/群防屏蔽分享功能时使用一次，数据修复后删除脚本
     * 临时脚本，所有资源都集中在本方法里面，以后删除的时候一锅端，不用额外去考虑其他耦合代码
     *
     * php index.php Home/Cli/initFriendShareConf
     *
     * @author Carter
     */
    public function initFriendShareConf()
    {
        $mod = new \Think\Model();
        $apiSer = new \Common\Service\ApiService();
        $confSer = new \Common\Service\DbLoadConfigService();

        $gameList = $mod->table('sad_game')->where(array('game_status' => 1))->select();
        foreach ($gameList as $v) {
            $gameId = $v['game_id'];
            if (true === $confSer->load($gameId, 'GAME_DICT_DB', 0)) {
                try {
                    $placeMod = new \Home\Model\DsqpDict\DictPlaceModel();
                    $configMod = new \Home\Model\DsqpDict\DictPlaceConfigModel();
                } catch (\Exception $e) {
                    echo "[Error] ".$e->getMessage()."\n";
                    exit;
                }
            } else {
                echo "[Error] svr load failed\n";
                exit;
            }
            if (true === $confSer->load($gameId, 'AGENT_ALL_DICT_DB', 0)) {
                try {
                    $shareMod = new \Home\Model\ClubDict\DictShareModel();
                } catch (\Exception $e) {
                    echo "[Error] ".$e->getMessage()."\n";
                    exit;
                }
            } else {
                echo "[Error] svr load failed\n";
                exit;
            }

            $modRet = $placeMod->queryDsqpPlaceListByFirstId($gameId, 'placeID');
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                echo "[Error] queryDsqpPlaceListByFirstId failed {$modRet['msg']}\n";
                exit;
            }
            $placeArr = array_column($modRet['data'], 'placeID');

            // source 6 大厅分享给好友（无奖励）在老分享里面使用的配置：dsqp_dict.dict_place_config.dailyShare
            // source 7 领取钻石-分享给好友在老分享里面使用的配置：dsqp_dict.dict_place_config.inviteShare
            $modRet = $configMod->queryDsqpPlaceConfigByPlaceId($placeArr, 'placeID,dailyShare,inviteShare');
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                echo "[Error] queryDsqpPlaceConfigByPlaceId failed {$modRet['msg']}\n";
                exit;
            }
            $configList = $modRet['data'];
            foreach ($configList as $d) {
                if (!empty($d['dailyShare'])) {
                    $w = array(
                        'game_id' => $gameId,
                        'source' => 6,
                        'place_id' => $d['placeID'],
                    );
                    $i = $mod->table('sad_game_share')->where($w)->find();
                    if ($i) {
                        continue;
                    }
                    $dailyShare = json_decode($d['dailyShare'], true);
                    if (false == $dailyShare) {
                        echo "[Error] invalid daily share {$d['dailyShare']}\n";
                        exit;
                    }
                    $inserData = array(
                        'game_id' => $gameId,
                        'place_id' => $d['placeID'],
                        'play_id' => 0,
                        'source' => 6,
                        'share_type' => 1,
                        'title' => $dailyShare['shareTitle'],
                        'desc' => $dailyShare['shareDesc'],
                        'image' => '',
                        'address' => '',
                        'qrcode_x' => 0,
                        'qrcode_y' => 0,
                    );
                    $id = $mod->table('sad_game_share')->add($inserData);
                    echo "[Info] insert daily share {$id}, place id {$d['placeID']}\n";
                }
                if (!empty($d['inviteShare'])) {
                    $w = array(
                        'game_id' => $gameId,
                        'source' => 7,
                        'place_id' => $d['placeID'],
                    );
                    $i = $mod->table('sad_game_share')->where($w)->find();
                    if ($i) {
                        continue;
                    }
                    $inviteShare = json_decode($d['inviteShare'], true);
                    if (false == $inviteShare) {
                        echo "[Error] invalid invite share {$d['inviteShare']}\n";
                        exit;
                    }
                    $inserData = array(
                        'game_id' => $gameId,
                        'place_id' => $d['placeID'],
                        'play_id' => 0,
                        'source' => 7,
                        'share_type' => 1,
                        'title' => $inviteShare['shareTitle'],
                        'desc' => $inviteShare['shareDesc'],
                        'image' => '',
                        'address' => '',
                        'qrcode_x' => 0,
                        'qrcode_y' => 0,
                    );
                    $id = $mod->table('sad_game_share')->add($inserData);
                    echo "[Info] insert invite share {$id}, place id {$d['placeID']}\n";
                }
            }

            // source 8 俱乐部分享给好友在老分享里面使用的配置：club_dict.dict_share.shareTitle、shareDesc
            $modRet = $shareMod->queryClubShareByGameId($gameId, 'shareTitle,shareDesc');
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                echo "[Error] queryClubShareByGameId failed {$modRet['msg']}\n";
                exit;
            }
            $shareInfo = $modRet['data'];

            $w = array(
                'game_id' => $gameId,
                'source' => 8,
                'place_id' => $gameId,
            );
            $i = $mod->table('sad_game_share')->where($w)->find();
            if ($i) {
                continue;
            }

            $inserData = array(
                'game_id' => $gameId,
                'place_id' => $gameId,
                'play_id' => 0,
                'source' => 8,
                'share_type' => 1,
                'title' => empty($shareInfo['shareTitle']) ? '' : $shareInfo['shareTitle'],
                'desc' => empty($shareInfo['shareDesc']) ? '' : $shareInfo['shareDesc'],
                'image' => '',
                'address' => '',
                'qrcode_x' => 0,
                'qrcode_y' => 0,
            );
            $id = $mod->table('sad_game_share')->add($inserData);
            echo "[Info] insert club share {$id}, game id {$gameId}\n";
        }

        echo "[Info] success\n";
        exit;
    }

    /**
     * 初始化表sad_game_landpage中新增字段place_id的值
     * php index.php Home/Cli/initLandPageConfigData
     * @author daniel
     */
    public function initLandPageConfigData()
    {
        $mod = new \Think\Model();
        $landPageList = $mod->table('sad_game_landpage')->select();
        foreach ($landPageList as $value) {
            if ($value['place_id'] == 0) {
                $where = ['id' => $value['id']];
                $data = ['place_id' => $value['game_id']];
                $mod->table('sad_game_landpage')->where($where)->save($data);
                echo "[Info] update " . $value['title'] . " data \n";
            }
        }
        echo "[Info] success\n";
        exit;
    }
}
