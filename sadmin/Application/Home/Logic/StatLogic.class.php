<?php
namespace Home\Logic;

use Common\Service\DbLoadConfigService;
use Home\Model\Club\ClubModel;
use Home\Model\Club\PromoterModel;
use Home\Model\Club\PromoterDelModel;
use Home\Model\Club\PromoterPayModel;
use Home\Model\Club\PromoterSellModel;
use Home\Model\ClubLog\LogClubModel;
use Home\Model\ClubLog\LogClubRoomcardModel;
use Home\Model\ClubLog\LogFailFormalGamecardModel;
use Home\Model\ClubLog\LogGamecardModel;
use Home\Model\ClubLog\LogPromoterSellModel;
use Home\Model\DsqpDict\DictPlaceModel;
use Home\Model\GameShareLogModel;
use Home\Model\GameDev\UUserExtModel;
use Home\Model\GameDev\UUserInfoModel;
use Home\Model\GameLogDev\UOnlinenumberLogModel;
use Home\Model\GameLogDev\UPropsLogModel;
use Home\Model\GameLogDev\UPyjRecordLogModel;
use Home\Model\GameLogDev\UPyjUserRecordModel;
use Home\Model\GameLogDev\UShareLogModel;
use Home\Model\GameLogDev\UUserInfoLogModel;
use Home\Model\StatClubPromoterModel;
use Home\Model\StatDiamondProduceModel;
use Home\Model\StatGameitemModel;
use Home\Model\StatGameRoomModel;
use Home\Model\StatGameShareModel;
use Home\Model\StatUserDailyModel;
use Home\Model\StatUserDailyRegionModel;
use Home\Model\StatUserDailyUsercacheModel;
use Home\Model\StatUserRankModel;
use Home\Model\SysCacheModel;
use Home\Model\StatOnlineModel;

class StatLogic
{
    public $showKeyList = array(
        "00:00", "00:05", "00:10", "00:15", "00:20", "00:25",
        "00:30", "00:35", "00:40", "00:45", "00:50", "00:55",
        "01:00", "01:05", "01:10", "01:15", "01:20", "01:25",
        "01:30", "01:35", "01:40", "01:45", "01:50", "01:55",
        "02:00", "02:05", "02:10", "02:15", "02:20", "02:25",
        "02:30", "02:35", "02:40", "02:45", "02:50", "02:55",
        "03:00", "03:05", "03:10", "03:15", "03:20", "03:25",
        "03:30", "03:35", "03:40", "03:45", "03:50", "03:55",
        "04:00", "04:05", "04:10", "04:15", "04:20", "04:25",
        "04:30", "04:35", "04:40", "04:45", "04:50", "04:55",
        "05:00", "05:05", "05:10", "05:15", "05:20", "05:25",
        "05:30", "05:35", "05:40", "05:45", "05:50", "05:55",
        "06:00", "06:05", "06:10", "06:15", "06:20", "06:25",
        "06:30", "06:35", "06:40", "06:45", "06:50", "06:55",
        "07:00", "07:05", "07:10", "07:15", "07:20", "07:25",
        "07:30", "07:35", "07:40", "07:45", "07:50", "07:55",
        "08:00", "08:05", "08:10", "08:15", "08:20", "08:25",
        "08:30", "08:35", "08:40", "08:45", "08:50", "08:55",
        "09:00", "09:05", "09:10", "09:15", "09:20", "09:25",
        "09:30", "09:35", "09:40", "09:45", "09:50", "09:55",
        "10:00", "10:05", "10:10", "10:15", "10:20", "10:25",
        "10:30", "10:35", "10:40", "10:45", "10:50", "10:55",
        "11:00", "11:05", "11:10", "11:15", "11:20", "11:25",
        "11:30", "11:35", "11:40", "11:45", "11:50", "11:55",
        "12:00", "12:05", "12:10", "12:15", "12:20", "12:25",
        "12:30", "12:35", "12:40", "12:45", "12:50", "12:55",
        "13:00", "13:05", "13:10", "13:15", "13:20", "13:25",
        "13:30", "13:35", "13:40", "13:45", "13:50", "13:55",
        "14:00", "14:05", "14:10", "14:15", "14:20", "14:25",
        "14:30", "14:35", "14:40", "14:45", "14:50", "14:55",
        "15:00", "15:05", "15:10", "15:15", "15:20", "15:25",
        "15:30", "15:35", "15:40", "15:45", "15:50", "15:55",
        "16:00", "16:05", "16:10", "16:15", "16:20", "16:25",
        "16:30", "16:35", "16:40", "16:45", "16:50", "16:55",
        "17:00", "17:05", "17:10", "17:15", "17:20", "17:25",
        "17:30", "17:35", "17:40", "17:45", "17:50", "17:55",
        "18:00", "18:05", "18:10", "18:15", "18:20", "18:25",
        "18:30", "18:35", "18:40", "18:45", "18:50", "18:55",
        "19:00", "19:05", "19:10", "19:15", "19:20", "19:25",
        "19:30", "19:35", "19:40", "19:45", "19:50", "19:55",
        "20:00", "20:05", "20:10", "20:15", "20:20", "20:25",
        "20:30", "20:35", "20:40", "20:45", "20:50", "20:55",
        "21:00", "21:05", "21:10", "21:15", "21:20", "21:25",
        "21:30", "21:35", "21:40", "21:45", "21:50", "21:55",
        "22:00", "22:05", "22:10", "22:15", "22:20", "22:25",
        "22:30", "22:35", "22:40", "22:45", "22:50", "22:55",
        "23:00", "23:05", "23:10", "23:15", "23:20", "23:25",
        "23:30", "23:35", "23:40", "23:45", "23:50", "23:55",
    );

    /************************************ 市场数据 ************************************/

    /**
     * 统计代理概况
     * @author Carter
     */
    public function statMarketClubPromoter($lastStat, $gameId, $statDate)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $statMod = new StatClubPromoterModel();
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load($gameId, 'CONF_DBTYPE_CLUB', 0)) {
            try {
                $clbMod = new ClubModel();
                $prmMod = new PromoterModel();
                $prmDelMod = new PromoterDelModel();
                $payMod = new PromoterPayModel();
                $sellMod = new PromoterSellModel();
            } catch (\Exception $e) {
                $conMsg = "CONF_DBTYPE_CLUB ".var_export(C('CONF_DBTYPE_CLUB'), true);
                $ret['code'] = ERRCODE_SYSTEM;
                $ret['msg'] = "数据库连接失败，错误信息：".$e->getMessage().", {$conMsg}";
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_SYSTEM;
            $ret['msg'] = "数据库配置加载失败，请确认数据库配置信息";
            return $ret;
        }
        if (true === $confSer->load($gameId, 'CONF_DBTYPE_CLUB_LOG', 0)) {
            try {
                $cLogMod = new LogClubModel();
                $sellLogMod = new LogPromoterSellModel();
            } catch (\Exception $e) {
                $conMsg = "CONF_DBTYPE_CLUB_LOG ".var_export(C('CONF_DBTYPE_CLUB_LOG'), true);
                $ret['code'] = ERRCODE_SYSTEM;
                $ret['msg'] = "数据库连接失败，错误信息：".$e->getMessage().", {$conMsg}";
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_SYSTEM;
            $ret['msg'] = "数据库配置加载失败，请确认数据库配置信息";
            return $ret;
        }
        if (true === $confSer->load($gameId, 'CONF_DBTYPE_GAME_LOG_DEV', 0)) {
            try {
                $roundMod = new UPyjRecordLogModel();
            } catch (\Exception $e) {
                $conMsg = "CONF_DBTYPE_GAME_LOG_DEV ".var_export(C('CONF_DBTYPE_GAME_LOG_DEV'), true);
                $ret['code'] = ERRCODE_SYSTEM;
                $ret['msg'] = "数据库连接失败，错误信息：".$e->getMessage().", {$conMsg}";
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_SYSTEM;
            $ret['msg'] = "数据库配置加载失败，请确认数据库配置信息";
            return $ret;
        }

        /******************** 统计日新增、累计新增 ********************/

        // 通过代理表和已删代理表获取当日新增人数
        $attr = array(
            'gameId' => $gameId,
            'createDate' => $statDate,
        );
        $modRet = $prmMod->queryClubPromoterCountByAttr($attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $promoterCreateCount = $modRet['data'];

        $attr = array(
            'gameId' => $gameId,
            'promoterDate' => $statDate,
        );
        $modRet = $prmDelMod->queryClubPromoterCountByAttr($attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $prmDelCreateCount = $modRet['data'];

        // 统计日新增
        $promoterCount = $promoterCreateCount + $prmDelCreateCount;

        // 累加到新增累计里面
        $promoterAmount = $lastStat['promoter_amount'] + $promoterCount;

        /******************** 统计日转正、累计转正、有效代理累计转正 ********************/

        // 获取转正日志
        $attr = array(
            'gameId' => $gameId,
            'clubType' => $cLogMod::LOG_TYPE_TRANSFER,
            'createDate' => $statDate
        );
        $modRet = $cLogMod->queryClubLogByAttr($attr, 'clubId');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $dayTranCidArr = array_column($modRet['data'], 'clubId');

        // 查看当日转正数组中，当日已删除的代理，这部分代理要过滤掉
        if (empty($dayTranCidArr)) {
            $prmDelCreateCount = 0;
        } else {
            $attr = array(
                'gameId' => $gameId,
                'clubId' => $dayTranCidArr,
                'createDate' => $statDate,
            );
            $modRet = $prmDelMod->queryClubPromoterCountByAttr($attr);
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $ret['code'] = $modRet['code'];
                $ret['msg'] = $modRet['msg'];
                return $ret;
            }
            $prmDelCreateCount = $modRet['data'];
        }
        $transferCount = count($dayTranCidArr) - $prmDelCreateCount;

        // 累计转正数，不包括已删代理
        $attr = array(
            'gameId' => $gameId,
            'clubStatus' => $clbMod::CLUB_STATUS_NORMAL,
            'createDateElt' => $statDate,
        );
        $modRet = $clbMod->queryClubCountByAttr($attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $transferAmount = $modRet['data'];

        $sql = "SELECT COUNT(*) AS count FROM club AS c JOIN promoter AS p ON c.id = p.clubId";
        $sql .= " WHERE c.gameId = {$gameId} AND c.clubStatus = 1 AND p.createDate <= '{$statDate}' AND p.totalPay > 0";
        try {
            $sqlRet = $clbMod->query($sql);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[statMarketClubPromoter] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $effectiveTransfer = $sqlRet[0]['count'];

        /******************** 日活、充值 ********************/

        // 通过战绩表，查询到当日的开房活跃代理
        $modRet = $roundMod->queryUPyjClubRecordByDate($statDate);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $pyjCidArr = array_column($modRet['data'], 'clubId');

        // 开房活跃代理中，通过拿到的clubId后可获取代理id
        $clubPidArr = array();
        if (!empty($pyjCidArr)) {
            $attr = array(
                'clubId' => $pyjCidArr
            );
            $modRet = $prmMod->queryClubromoterListByAttr($attr, 'id');
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $ret['code'] = $modRet['code'];
                $ret['msg'] = $modRet['msg'];
                return $ret;
            }
            $clubPidArr = array_column($modRet['data'], 'id');
        }

        // 亲友圈活跃代理数
        $clubActive = count($clubPidArr);

        // 通过卖钻流水和撤回流水，查询到当日的卖钻活跃代理，这部分只有不在开房活跃中的才算散户代理
        $attr = array(
            'gameId' => $gameId,
            'createDate' => $statDate,
        );
        $modRet = $sellLogMod->queryPromoterSellRevokeLogByAttr($attr, 'promoterSellId');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $revokeIdArr = array_column($modRet['data'], 'promoterSellId');
        $modRet = $sellMod->queryClubPromoterSellAndRevoke($gameId, $statDate, $revokeIdArr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $sellPidArr = $modRet['data'];
        // 去重
        $sellPidArr = array_flip(array_flip($sellPidArr));

        // 取卖钻代理对开房代理的差集，就是散户代理
        $retailPidArr = array_diff($sellPidArr, $clubPidArr);

        // 散户活跃代理数
        $retailActive = count($retailPidArr);

        // 日活跃代理数
        $activeCount = $clubActive + $retailActive;

        // 日活跃代理中，存在充值记录的代理为有效代理
        $dailyActivePidArr = array_merge($clubPidArr, $retailPidArr);
        if (empty($dailyActivePidArr)) {
            $effectiveActive = 0;
        } else {
            $attr = array(
                'promoterId' => $dailyActivePidArr,
                'gameId' => $gameId,
                'totalPayGt' => 0,
            );
            $modRet = $prmMod->queryClubPromoterCountByAttr($attr);
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $ret['code'] = $modRet['code'];
                $ret['msg'] = $modRet['msg'];
                return $ret;
            }
            $effectiveActive = $modRet['data'];
        }

        // 开始计算亲友圈、散户、有效代理和当日充值总额
        $effectiveRecharge = 0;
        $clubRecharge = 0;
        $retailRecharge = 0;

        // 获取所有当日充值流水
        $attr = array(
            'gameId' => $gameId,
            'isSuccess' => 1,
            'createDate' => $statDate,
        );
        $field = 'promoterId,price';
        $modRet = $payMod->queryClubPromoterPayListByAttr($attr, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $payMap = array();
        $payPidArr = array();
        // 将充值日志处理为以代理id为key，充值价格做个累加
        foreach ($modRet['data'] as $v) {
            if (!isset($payMap[$v['promoterId']])) {
                $payMap[$v['promoterId']] = 0;
                $payPidArr[] = $v['promoterId'];
            }

            $price = round($v['price'] * 100);

            $payMap[$v['promoterId']] += $price;

            // 有效代理充值总额
            $effectiveRecharge += $price;
        }
        $rechargeAmount = $effectiveRecharge + $lastStat['recharge_amount'];

        // 亲友圈代理当日充值总额，亲友圈代理充值也同时累加进有效代理充值
        $clubPayArr = array_intersect($payPidArr, $clubPidArr);
        foreach ($clubPayArr as $v) {
            // 亲友圈代理充值
            $clubRecharge += $payMap[$v];
        }

        // 散户代理当日充值总额，散户代理充值也同时累加进有效代理充值
        $retailPayArr = array_intersect($payPidArr, $retailPidArr);
        foreach ($retailPayArr as $v) {
            // 散户代理充值
            $retailRecharge += $payMap[$v];
        }

        // 统计完毕，插入数据
        $statData = array(
            // 累计代理数
            'promoterAmount' => $promoterAmount,
            // 累计转正代理数
            'transferAmount' => $transferAmount,
            // 日活跃代理数
            'activeCount' => $activeCount,
            // 代理充值总额
            'rechargeAmount' => $rechargeAmount,
            // 日新开通代理数
            'promoterCount' => $promoterCount,
            // 日转正代理数
            'transferCount' => $transferCount,
            // 有效代理累计转正数
            'effectiveTransfer' => $effectiveTransfer,
            // 有效代理当日活跃数
            'effectiveActive' => $effectiveActive,
            // 有效代理当日充值总额
            'effectiveRecharge' => $effectiveRecharge,
            // 亲友圈代理当日活跃数
            'clubActive' => $clubActive,
            // 亲友圈代理当日充值总额
            'clubRecharge' => $clubRecharge,
            // 散户代理当日活跃数
            'retailActive' => $retailActive,
            // 散户代理当日充值总额
            'retailRecharge' => $retailRecharge,
        );
        $modRet = $statMod->insertStatClubPromoter($statData, $gameId, $statDate);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $ret['data'] = $modRet['data'];

        return $ret;
    }

    /************************************ 实时数据，在线用户 ************************************/

    /**
     * 处理数据逻辑
     * 处理成前端页面需要的格式
     * @param array $data 初始数据格式
     * @return array 处理结果
     */
    public function formatOnlineData($data,$datatime)
    {
        $result = array();
        $dayArray = array();

        // 原始数据处理成按天，按时的数组
        foreach($data as  $item){
            $dayKey = date( 'Y-m-d',$item['data_time']  );
            $hourKey = date( 'H:i',$item['data_hour']  );

            $temp = &$dayArray[ $dayKey  ] ;
            if( !isset($temp) ){
                $temp  = array();
            }

            $temp[ $hourKey ] =  (int)$item['online_count'] ;
        }

        //按天处理成图表所需的格式
        foreach($datatime as $dayItem ){
            $day = date('Y-m-d',$dayItem);

            if($dayArray[ $day ]){
                $showData =& $dayArray[ $day ] ;
            }else{
                $showData = array();
            }
            //按时间段处理时间，确保一天所有时间段有数据，即便数据库没有统计值，则补充为0
            $itemData = array();
            foreach ($this->showKeyList as $hour ){
                if( empty( $showData[$hour]) ){
                    $itemData[] = 0;
                }else{
                    $itemData[] = $showData[$hour];
                }
            }
            //输出图表所需的格式
            $result[$day] = array(
                'name' => $day,
                'type' => 'line' ,
                'smooth' => true,
                'selected' => false ,
                'data' => $itemData
            );

        }

        unset($data);
        return $result;
    }

    /**
     * 处理新增人数实时数据
     * @author liyao
     */
    public function formatRegisterData($data, $datatime, &$displayTime)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $result = array();
        $dayArray = array();
        $displayTime = array();

        // 显示的横坐标时间
        for ($i = 0, $j = 0; $i < count($this->showKeyList); $i += 6, $j++) {
            $displayTime[] = $this->showKeyList[$i];
        }

        // 原始数据处理成按天，按时的数组
        foreach ($data as $item) {
            $dayKey = date('Y-m-d', $item['data_time']);
            $hourKey = date('H:i', $item['data_hour']);
            $seconds = intval(date("H", $item['data_hour'])) * 3600 + intval(date("i", $item['data_hour'])) * 60;

            if (!isset($dayArray[$dayKey])) {
                $dayArray[$dayKey] = array();
            }

            $idx = intval($seconds / 1800);
            $key = $displayTime[$idx];
            if (!isset($dayArray[$dayKey][$key])) {
                $dayArray[$dayKey][$key] = 0;
            }
            $dayArray[$dayKey][$key] += (int) $item['register_count'];
        }

        //按天处理成图表所需的格式
        foreach ($datatime as $dayItem) {
            $day = date('Y-m-d', $dayItem);

            if ($dayArray[$day]) {
                $showData = $dayArray[$day];
            } else {
                $showData = array();
            }
            //按时间段处理时间，确保一天所有时间段有数据，即便数据库没有统计值，则补充为0
            $itemData = array();
            foreach ($displayTime as $hour) {
                if (empty($showData[$hour])) {
                    $itemData[] = 0;
                } else {
                    $itemData[] = $showData[$hour];
                }
            }
            //输出图表所需的格式
            $result[$day] = array(
                'name' => $day,
                'type' => 'line',
                'smooth' => true,
                'selected' => false,
                'data' => $itemData
            );
        }
        $ret['data'] = $result;

        return $ret;
    }

    /**
     * 在线用户定时任务逻辑
     * @param int $gameId 游戏产品ID
     */
    public function statOneGameOnlineLogic($gameId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $logMsg = "[Info] 统计脚本开始统计：{$gameId}，时间：".date('Y-m-d H:i:s', time() )."\n";

        $cacheModel = new SysCacheModel() ;

        $confSer = new DbLoadConfigService();
        $dbStatus = $confSer->load($gameId , 'CONF_DBTYPE_GAME_LOG_DEV', 0) ;
        if (true === $dbStatus) {
            try {
                $logDev = new UOnlinenumberLogModel();
            } catch (\Exception $e) {
                $ret['code'] = ERRCODE_SYSTEM;
                $ret['msg'] = "数据库连接失败，错误信息：".$e->getMessage();
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_SYSTEM;
            $ret['msg'] = "数据库配置加载失败，请确认数据库配置信息1";
            return $ret;
        }

        // 游戏库
        if (true === $confSer->load($gameId, 'GAME_DEV_DB', 0)) {
            try {
                $userMod = new UUserInfoModel();
            } catch (\Exception $e) {
                $ret['code'] = ERRCODE_SYSTEM;
                $ret['msg'] = "数据库连接失败，错误信息：".$e->getMessage();
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_SYSTEM;
            $ret['msg'] = "数据库配置加载失败，请确认数据库配置信息1";
            return $ret;
        }
        try {
            //计算循环日期
            $countWhere = array();
            //缓存取数据最大ID，计算要索引的时间。

            $modRet = $cacheModel->querySysCacheByKey($gameId, 'sadmin_online_cache');
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $logMsg = "[Warning] 缓存异常：{$gameId}，".$modRet['msg']."\n";
                set_exception(__FILE__, __LINE__, " statOneGameOnlineLogic  [Warning] {$gameId}".$logMsg );
            }
            $sysCache = $modRet['data']['cache_sting'] ;

            if($sysCache){
                $maxId = (int) $sysCache ;
                $countWhere['id'] = array('gt',$maxId); //减少对日志表的索引
            }

            $where['gameId'] = $gameId;
            if($maxId){
                $where['id'] = array('gt',$maxId);
                $firstInfo = $logDev->getOnleDataInfo($where);
            }  else {
                $firstInfo = $logDev->getOnleDataInfo($where);
            }

            $logMsg = "[Info] 查询在线用户表：{$gameId}，".$logDev->getLastSql()."\n";

            //查询在线数据异常.
            if($firstInfo['code'] != ERRCODE_SUCCESS){

                $logMsg = "[Fail] 查询在线用户的数据异常：{$gameId}，".$firstInfo['msg']."\n";
                set_exception(__FILE__, __LINE__, " _statOneGameOnlinData  [Warning] {$gameId}".$logMsg );

                $ret['code'] = ERRCODE_DB_SELECT_ERR ;
                $ret['msg'] = $logMsg ;
                return $ret ;
            }

            //处理开始时间
            $times = $firstInfo['data']['createTime'] ? strtotime($firstInfo['data']['createTime']) : (time() - 300) ;
            $startTimeUnixtime = $times - $times % 300 ; //整数时间

            $i = 0;
            do {
                $endTimeUnixtime = $startTimeUnixtime + 300;

                $logMsg = "[Info] 查询{$gameId}在线用户表时间区间开始时间：".date('Y-m-d H:i:s' , $startTimeUnixtime)."\n";

                $logMsg = "[Info] 查询{$gameId}在线用户表时间区间结束时间：".date('Y-m-d H:i:s' , $endTimeUnixtime)."\n";

                $countWhere['gameId'] = $gameId;
                $countWhere['createTime'] = array(
                    array('egt' , date('Y-m-d H:i:s' , $startTimeUnixtime) ) ,
                    array('lt' , date('Y-m-d H:i:s' , $endTimeUnixtime) )
                );

                $agentDiamond = $logDev->getGameOnlineMaxNumber($countWhere);

                $logMsg = "[Info] 查询在线用户表：{$gameId}，".$logDev->getLastSql()."\n";

                //查询数据判断
                if($agentDiamond['code'] != ERRCODE_SUCCESS){

                    $logMsg = "[Fail] 查询在线用户的数据失败：{$gameId}，".$agentDiamond['msg'] ."\n";
                    set_exception(__FILE__, __LINE__, " _statOneGameOnlinData  [Warning] {$gameId}".$logMsg );

                    $ret['code'] = ERRCODE_DB_SELECT_ERR ;
                    $ret['msg'] = $logMsg ;
                    return $ret ;

                }

                // 查询注册人数
                $where = array();
                $where['type'] = 4;
                $where['createTime'][] = array('egt' , date('Y-m-d H:i:s' , $startTimeUnixtime) );
                $where['createTime'][] = array('lt' , date('Y-m-d H:i:s' , $endTimeUnixtime) );
                $modRet = $userMod->queryUserCountByWhere($where);
                if($modRet['code'] != ERRCODE_SUCCESS){
                    return $modRet;
                }
                $register_count = intval($modRet["data"]);

                $dataTime = strtotime( date('Y-m-d',$startTimeUnixtime ) ) ;
                $dataHour = strtotime( date('Y-m-d H:i',$startTimeUnixtime ).":00" ) ;
                $data = array(
                    'game_id' => $gameId,
                    'online_count' => (int) $agentDiamond['data']['onlineNumber'],
                    'register_count' => $register_count,
                    'data_hour' => $dataHour ,
                    'data_time' => $dataTime ,
                );

                $statModel = new StatOnlineModel();
                $thisData = $statModel->getDateTimeIsExtsts($gameId ,$dataHour,$dataTime );
                if($thisData){
                    //更新
                    $dataId = $thisData['id'] ;
                    $data['update_time'] = time();
                    $result = $statModel->updateStatData( $dataId , $data);

                }else{
                    //新增
                    $data['create_time'] = time();
                    $result = $statModel->addEmptyData( $data);
                    $dataId = $result ;
                }

                if($agentDiamond['data']['id']){ //有数据的时候进行cache缓存
                    $setSysCache = $cacheModel->exceSetSysCache($gameId, 'sadmin_online_cache', $agentDiamond['data']['id'], '');
                }
                //缓存数据逻辑
                if( $setSysCache['code'] != ERRCODE_SUCCESS ){
                    $logMsg = "[Fail] 缓存异常：{$gameId}，".$setSysCache['msg']."\n";
                    set_exception(__FILE__, __LINE__, " statOneGameOnlineLogic  [Warning] {$gameId}".$logMsg .$statModel->getLastSql() );

                    $ret['code'] = ERRCODE_DB_UPDATE_ERR ;
                    $ret['msg'] = $logMsg ;
                    return $ret;
                }
                $logMsg = "[Info] {$gameId}单次统计完成，更新统计数据SQL：".$statModel->getLastSql()."\n";

                $i++;
                $startTimeUnixtime += 300 ;
            } while ($startTimeUnixtime < time() && !empty($agentDiamond));
        } catch (\Exception $e) {
            $logMsg = "[Fail] 日志库查询失败，{$gameId} 错误信息：".$e->getMessage()."\n";
            set_exception(__FILE__, __LINE__, " statOneGameOnlineLogic  [Warning] {$gameId}".$logMsg  );

            $ret['code'] = ERRCODE_SYSTEM ;
            $ret['msg'] = $logMsg ;
            return $ret;
        }
    }

    /************************************ 用户数据，每日简报 ************************************/

    /**
     * 根据开始结束时间统计指定游戏的每日简报数据
     * @author Carter
     */
    public function statUserDailyByTime($gameId, $statSTime, $statETime)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $log = new \Think\Log();
        $dailyMod = new StatUserDailyModel();
        $regionMod = new StatUserDailyRegionModel();
        $userCacheMod = new StatUserDailyUsercacheModel();
        $confSer = new DbLoadConfigService();
        // 游戏库
        if (true === $confSer->load($gameId, 'GAME_DEV_DB', 0)) {
            try {
                $userMod = new UUserInfoModel();
                $extMod = new UUserExtModel();
            } catch (\Exception $e) {
                $ret['code'] = ERRCODE_SYSTEM;
                $ret['msg'] = "数据库连接失败，错误信息：".$e->getMessage();
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_SYSTEM;
            $ret['msg'] = "数据库配置加载失败，请确认数据库配置信息1";
            return $ret;
        }
        // 游戏日志库
        if (true === $confSer->load($gameId, 'CONF_DBTYPE_GAME_LOG_DEV', 0)) {
            try {
                $uLogMod = new UUserInfoLogModel();
                $propMod = new UPropsLogModel();
                $recordMod = new UPyjRecordLogModel();
            } catch (\Exception $e) {
                $ret['code'] = ERRCODE_SYSTEM;
                $ret['msg'] = "数据库连接失败，错误信息：".$e->getMessage();
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_SYSTEM;
            $ret['msg'] = "数据库配置加载失败，请确认数据库配置信息";
            return $ret;
        }
        // 俱乐部日志库
        if (true === $confSer->load($gameId, 'CONF_DBTYPE_CLUB_LOG', 0)) {
            try {
                $cLogMod = new LogClubRoomcardModel() ;
            } catch (\Exception $e) {
                $ret['code'] = ERRCODE_SYSTEM;
                $ret['msg'] = "数据库连接失败，错误信息：".$e->getMessage();
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_SYSTEM;
            $ret['msg'] = "数据库配置加载失败，请确认数据库配置信息";
            return $ret;
        }

        // 获取指定时间段内已统计数据，数据入库时需要
        $attr = array(
            'gameId' => $gameId,
            'sDataTime' => strtotime(date("Y-m-d", $statSTime)),
            'eDataTime' => $statETime,
        );
        $field = 'id,data_time,add_user,login_user,active_user,consume_prop';
        $modRet = $dailyMod->queryStatDailyListByAttr($attr, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }

        // 每日简报数据，以统计日期为key
        $statDailyData = array();

        // 通过主表id，可查出地区表已统计数据，用于后续数据入库对比用
        $statIdArr = array();

        foreach ($modRet['data']['list'] as $v) {
            $statDailyData[$v['data_time']] = $v;
            $statIdArr[] = $v['id'];
        }

        // 获取相应的地区数据，数据入库时需要
        $attr = array('parentId' => $statIdArr);
        $field = 'id,parent_id,place_id,data_type,data_val';
        $modRet = $regionMod->queryStatDailyRegionByAttr($attr, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }

        // 地区数据
        $statRegionData = array();
        foreach ($modRet['data'] as $v) {
            $statRegionData[$v['parent_id']][$v['place_id']][$v['data_type']] = array(
                'id' => $v['id'],
                'dataVal' => $v['data_val'],
            );
        }

        // 若统计区间跨越多天，划分成按天统计
        $curDate = strtotime(date('Y-m-d', $statSTime));
        $headTime = $statSTime;
        $tailTime = min($statETime, strtotime('+1 day', $curDate)) - 1;
        while ($headTime < $statETime) {
            // 做一次时间段校验，开始结束时间必须是同一天内
            if (date('Y-m-d', $headTime) != date('Y-m-d', $tailTime)) {
                $errMsg = "game {$gameId}, head time ".date('Y-m-d H:i:s', $headTime);
                $errMsg .= ", tail time ".date('Y-m-d H:i:s', $tailTime)." sync failed.";
                $errMsg .= " cur date ".date('Y-m-d H:i:s', $curDate);
                $errMsg .= ", start time ".date('Y-m-d H:i:s', $statSTime);
                $errMsg .= ", end time ".date('Y-m-d H:i:s', $statETime);
                set_exception(__FILE__, __LINE__, "[statUserDailyByTime] {$errMsg}");
            }

            /******************** 新增注册 ********************/

            // 获取注册用户数，按天累加
            $attr = array(
                'type' => 4, // 1：普通玩家（已经不用）；4：朋友局游戏玩家
                'startCreateTime' => date('Y-m-d H:i:s', $headTime),
                'endCreateTime' => date('Y-m-d H:i:s', $tailTime),
            );
            $modRet = $userMod->queryDevUserListByAttr($attr, 'userId');
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $ret['code'] = $modRet['code'];
                $ret['msg'] = $modRet['msg'];
                return $ret;
            }

            // 新增注册数
            $addUserCount = 0;

            // 新增注册的uid，用来统计地区新增注册数据
            $addUidArr = array();

            foreach ($modRet['data'] as $v) {
                $addUserCount++;
                $addUidArr[] = $v['userId'];
            }

            /******************** 登录人数 ********************/

            // 获取登录用户数，先获取当天已经统计过的玩家
            $attr = array(
                'statTime' => $curDate,
            );
            $modRet = $userCacheMod->queryStatDailyUserCacheByAttr($gameId, $attr, 'uid');
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $ret['code'] = $modRet['code'];
                $ret['msg'] = $modRet['msg'];
                return $ret;
            }

            // 缓存表中当天已经登录过的用户
            $cacheUser = array_column($modRet['data'], 'uid');

            // 获取统计时间段的登录用户数
            $attr = array(
                'sLoginTime' => date('Y-m-d H:i:s', $headTime),
                'eLoginTime' => date('Y-m-d H:i:s', $tailTime),
            );
            $modRet = $uLogMod->queryDevLogUserInfoList($attr, 'userId');
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $ret['code'] = $modRet['code'];
                $ret['msg'] = $modRet['msg'];
                return $ret;
            }

            // 同一个人多次登录只加一
            $loginUser = array();
            foreach ($modRet['data'] as $v) {
                $loginUser[$v['userId']] = 1;
            }
            $loginUser = array_keys($loginUser);

            // 对时间段内缓存表中已登录用户数组与当天已登录用户数组做一次差集，集合就是新增的登录用户
            $loginUserArr = array_diff($loginUser, $cacheUser);

            // 将增加的登录人数用户插入到缓存表
            if (!empty($loginUserArr)) {
                $modRet = $userCacheMod->insertStatUserDailyUserCache($gameId, $curDate, $loginUserArr);
                if (ERRCODE_SUCCESS !== $modRet['code']) {
                    $ret['code'] = $modRet['code'];
                    $ret['msg'] = $modRet['msg'];
                    return $ret;
                }
            }

            // 非正式环境，记录详情
            if ('production' != APP_STATUS) {
                $log->write("game {$gameId}, starttime ".date('Y-m-d H:i:s', $headTime).", endtime ".date('Y-m-d H:i:s', $tailTime));
                $log->write("insert user cache: ".implode(", ", $loginUserArr));
            }

            /******************** 活跃人数 ********************/

            // 获取当天缓存表中，已登录但未参与牌局的用户（已登录的用户必定已经进入了缓存表）
            $attr = array(
                'statTime' => $curDate,
                'gameFlag' => 0,
            );
            $modRet = $userCacheMod->queryStatDailyUserCacheByAttr($gameId, $attr, 'id,uid');
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $ret['code'] = $modRet['code'];
                $ret['msg'] = $modRet['msg'];
                return $ret;
            }
            $cacheSilentUser = array_combine(
                array_column($modRet['data'], 'uid'),
                array_column($modRet['data'], 'id')
            );

            // 获取指定时间段内，所有成功开局的牌局记录
            $field = "userId1,userId2,userId3,userId4";
            $modRet = $recordMod->queryUPyjOpeningRecordLogByTime($headTime, $tailTime, $field);
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $ret['code'] = $modRet['code'];
                $ret['msg'] = $modRet['msg'];
                return $ret;
            }
            // 整理出所有参与了牌局的玩家列表
            $gameUser = array();
            foreach ($modRet['data'] as $v) {
                $gameUser[$v['userId1']] = 1;
                $gameUser[$v['userId2']] = 1;
                $gameUser[$v['userId3']] = 1;
                $gameUser[$v['userId4']] = 1;
            }
            // uid为0的表示牌局未满4人，过滤掉
            unset($gameUser[0]);

            // 将缓存中已登录未活跃用户数据与战绩表所有参与牌局用户数据做一次交集，得到的就是新增的活跃用户
            $activeUserMap = array_intersect_key($cacheSilentUser, $gameUser);

            // 将新增活跃用户的缓存表参与字段更新为已活跃
            if (!empty($activeUserMap)) {
                $modRet = $userCacheMod->updateStatDailyCacheGameFlag($activeUserMap);
                if (ERRCODE_SUCCESS !== $modRet['code']) {
                    $ret['code'] = $modRet['code'];
                    $ret['msg'] = $modRet['msg'];
                    return $ret;
                }
            }

            // 新增的活跃人数
            $activeUserArr = array_keys($activeUserMap);

            // 非正式环境，记录详情
            if ('production' != APP_STATUS) {
                $log->write("update active cache: ".implode(", ", $activeUserArr));
            }

            /******************** 消耗钻石 ********************/

            // 获取消耗钻石，游戏开房及俱乐部开房
            $diamondCount = 0;

            // 游戏开房的钻石消耗
            $attr = array(
                'way' => 100,
                'sCreateTime' => date('Y-m-d H:i:s', $headTime),
                'eCreateTime' => date('Y-m-d H:i:s', $tailTime),
                'propsId' => 10008,
            );
            $modRet = $propMod->queryDevLogPropListByAttr($attr, 'propsNum');
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $ret['code'] = $modRet['code'];
                $ret['msg'] = $modRet['msg'];
                return $ret;
            }
            foreach ($modRet['data'] as $v) {
                $diamondCount += $v['propsNum'];
            }

            // 俱乐部的钻石消耗
            $attr = array(
                'gameId' => $gameId,
                'sCreateTime' => date('Y-m-d H:i:s', $headTime),
                'eCreateTime' => date('Y-m-d H:i:s', $tailTime),
            );
            $modRet = $cLogMod->queryClugLogRoomcardByAttr($attr, 'cardConsume');
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $ret['code'] = $modRet['code'];
                $ret['msg'] = $modRet['msg'];
                return $ret;
            }
            foreach ($modRet['data'] as $v) {
                $diamondCount += $v['cardConsume'];
            }

            /******************** 主统计表数据入库 ********************/

            // 主表数据入库，已经统计过的作数据更新
            if (isset($statDailyData[$curDate])) {
                $statId = $statDailyData[$curDate]['id'];

                $updateAttr = array(
                    'addUser' => $addUserCount + $statDailyData[$curDate]['add_user'],
                    'loginUser' => count($loginUserArr) + $statDailyData[$curDate]['login_user'],
                    'activeUser' => count($activeUserArr) + $statDailyData[$curDate]['active_user'],
                    'consumeProp' => $diamondCount + $statDailyData[$curDate]['consume_prop']
                );
                $modRet = $dailyMod->updateStatUserDaily($statId, $updateAttr);
                if (ERRCODE_SUCCESS !== $modRet['code']) {
                    $ret['code'] = $modRet['code'];
                    $ret['msg'] = $modRet['msg'];
                    return $ret;
                }
            }
            // 未统计过的日期作数据插入
            else {
                $insertAttr = array(
                    'addUser' => $addUserCount,
                    'loginUser' => count($loginUserArr),
                    'activeUser' => count($activeUserArr),
                    'consumeProp' => $diamondCount,
                );
                $modRet = $dailyMod->insertStatUserDaily($gameId, $curDate, $insertAttr);
                if (ERRCODE_SUCCESS !== $modRet['code']) {
                    $ret['code'] = $modRet['code'];
                    $ret['msg'] = $modRet['msg'];
                    return $ret;
                }

                $statId = $modRet['data'];
            }

            // 非正式环境，记录详情
            if ('production' != APP_STATUS) {
                $log->write("add user: ".implode(", ", $addUidArr));
                $log->write("login user: ".implode(", ", $loginUserArr));
                $log->write("active user: ".implode(", ", $activeUserArr));
            }

            /******************** 地区表数据入库 ********************/

            $regionData = array(
                // 新增注册
                array('uid' => $addUidArr, 'type' => $regionMod::DATA_TYPE_REGISTER),
                // 登录人数
                array('uid' => $loginUserArr, 'type' => $regionMod::DATA_TYPE_LOGIN),
                // 活跃人数
                array('uid' => $activeUserArr, 'type' => $regionMod::DATA_TYPE_ACTIVE),
            );
            foreach ($regionData as $v) {
                if (!empty($v['uid'])) {
                    // 查出每个用户的关联地区
                    $extAttr = array(
                        'userId' => $v['uid'],
                    );
                    $modRet = $extMod->queryDevUserExtListByAttr($extAttr, 'preferredCity');
                    if (ERRCODE_SUCCESS !== $modRet['code']) {
                        $ret['code'] = $modRet['code'];
                        $ret['msg'] = $modRet['msg'];
                        return $ret;
                    }
                    $region = array();
                    foreach ($modRet['data'] as $x) {
                        if (!isset($region[$x['preferredCity']])) {
                            $region[$x['preferredCity']] = 0;
                        }
                        $region[$x['preferredCity']]++;
                    }

                    // 非正式环境，记录详情
                    if ('production' != APP_STATUS) {
                        $log->write("type {$v['type']}, region ".var_export($region, true));
                    }

                    // 开始入库
                    foreach ($region as $placeId => $count) {
                        // 已经统计过的作数据更新
                        if (isset($statRegionData[$statId][$placeId][$v['type']])) {
                            $updateVal = $statRegionData[$statId][$placeId][$v['type']]['dataVal'] + $count;
                            $modRet = $regionMod->updateStatUserDailyRegion($statRegionData[$statId][$placeId][$v['type']]['id'], $updateVal);
                            if (ERRCODE_SUCCESS !== $modRet['code']) {
                                $ret['code'] = $modRet['code'];
                                $ret['msg'] = $modRet['msg'];
                                return $ret;
                            }
                        }
                        // 未统计过的日期作数据插入
                        else {
                            $regionInsertAttr = array(
                                'parentId' => $statId,
                                'placeId' => $placeId,
                                'dataType' => $v['type'],
                                'dataVal' => $count,
                            );
                            $modRet = $regionMod->insertStatUserDailyRegion($regionInsertAttr);
                            if (ERRCODE_SUCCESS !== $modRet['code']) {
                                $ret['code'] = $modRet['code'];
                                $ret['msg'] = $modRet['msg'];
                                return $ret;
                            }
                        }
                    }
                }
            }

            // 记录本次数据
            $ret['data'][] = array(
                'curDate' => $curDate,
                'headTime' => $headTime,
                'tailTime' => $tailTime,
            );
            $curDate = $headTime = $tailTime + 1;
            $tailTime = min($statETime, strtotime('+1 day', $curDate)) - 1;
        };

        return $ret;
    }

    /**
     * 获取指定每日简报数据的地区饼图
     * @author Carter
     */
    public function getStatUserDailyRegionPie($parentId, $dataType)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $statMod = new StatUserDailyModel();
        $regionMod = new StatUserDailyRegionModel();
        $confSer = new DbLoadConfigService();
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

        // 获取主表基本信息
        $modRet = $statMod->queryStatDailyListById($parentId, 'data_time');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $statInfo = $modRet['data'];

        // 获取源数据
        $attr = array(
            'parentId' => $parentId,
            'dataType' => $dataType,
        );
        $field = 'place_id,data_val';
        $modRet = $regionMod->queryStatDailyRegionByAttr($attr, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        if (empty($modRet['data'])) {
            $ret['code'] = ERRCODE_DB_DATA_EMPTY;
            $ret['msg'] = '该记录未统计地区数据';
            return $ret;
        }

        // 数据值累计
        $valCount = 0;
        // 地区数组
        $placeIdArr = array();
        // 数据字典
        $dataMap = array();
        foreach ($modRet['data'] as $v) {
            $valCount += $v['data_val'];
            $placeIdArr[] = $v['place_id'];
            $dataMap[$v['place_id']] = $v['data_val'];
        }

        // 通过地区数组，可以获取到地区字典
        $modRet = $placeMod->queryDsqpPlaceByPlaceId($placeIdArr, 'placeID,placeName');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $placeMap = array_combine(
            array_column($modRet['data'], 'placeID'),
            array_column($modRet['data'], 'placeName')
        );
        // 可能存在注册后未选地区的情况
        $placeMap[0] = '地区未选';

        // 饼图数据
        $regionData = array(
            'legendData' => array(),
            'seriesData' => array(),
        );
        foreach ($dataMap as $placeId => $val) {
            if (!isset($placeMap[$placeId])) {
                set_exception(__FILE__, __LINE__, "[getStatUserDailyRegionPie] stat id {$parentId}, type {$dataType}, place {$placeId}");
            }

            // 百分比
            $precent = round($val / $valCount * 100, 2);
            // 地区信息，包括名称和百分比
            $name = "{$placeMap[$placeId]} {$precent}%";

            // 整合数据
            $regionData['legendData'][] = $name;
            $regionData['seriesData'][] = array('name' => $name, 'value' => $val);
        }

        $typeMap = $regionMod->dataTypeMap;

        $ret['data']['title'] = date('Y-m-d', $statInfo['data_time'])." {$typeMap[$dataType]}";
        $ret['data']['region'] = $regionData;

        return $ret;
    }

    /************************************ 用户数据，用户排行 ************************************/

    /**
     * 对指定游戏的指定日期进行用户排行统计
     * @author Carter
     */
    public function statUserRankByGameAndTime($gameId, $statTime)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        // 统计排行数，目前要求统计前 200 名
        $rankCount = 200;

        $startTime = date('Y-m-d 00:00:00', $statTime);
        $endTime = date('Y-m-d 23:59:59', $statTime);

        $statMod = new StatUserRankModel();
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load($gameId, 'GAME_DEV_DB', 0)) {
            try {
                $uInfoMod = new UUserInfoModel();
            } catch (\Exception $e) {
                $ret['code'] = ERRCODE_SYSTEM;
                $ret['msg'] = "数据库连接失败，错误信息：".$e->getMessage();
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_SYSTEM;
            $ret['msg'] = "数据库配置加载失败，请确认数据库配置信息";
            return $ret;
        }
        if (true === $confSer->load($gameId, 'CONF_DBTYPE_GAME_LOG_DEV', 0)) {
            try {
                $propMod = new UPropsLogModel();
                $recordMod = new UPyjRecordLogModel();
            } catch (\Exception $e) {
                $ret['code'] = ERRCODE_SYSTEM;
                $ret['msg'] = "数据库连接失败，错误信息：".$e->getMessage();
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_SYSTEM;
            $ret['msg'] = "数据库配置加载失败，请确认数据库配置信息";
            return $ret;
        }

        // 钻石消耗 u_props_log WHERE way = 100 AND propsid = 10008
        $propSort = array();
        $attr = array(
            'way' => $propMod::WAY_ROOM,
            'sCreateTime' => $startTime,
            'eCreateTime' => $endTime,
            'propsId' => $propMod::PROP_DIAMOND,
        );
        $field = 'userId,propsNum';
        $modRet = $propMod->queryDevLogPropListByAttr($attr, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        foreach ($modRet['data'] as $v) {
            if (!isset($propSort[$v['userId']])) {
                $propSort[$v['userId']] = array('uid' => $v['userId'], 'num' => 0);
            }
            $propSort[$v['userId']]['num'] += $v['propsNum'];
        }
        usort($propSort, function($a, $b) {
            if ($a['num'] == $b['num']) {
                return 0;
            }
            return ($a['num'] > $b['num']) ? -1 : 1;
        });
        $propSort = array_slice($propSort, 0, $rankCount);

        // 大赢家
        $winSort = array();

        // 参与牌局
        $gameSort = array();

        // 参与4人局
        $game4Sort = array();

        // 获取牌局日志用以统计牌局排行，只统计成功开局的，每次仅取 1w 条，防止内存耗尽
        $field = 'winnerId,playerCount,winnerNum,userId1,userCashDiff1,userId2,userCashDiff2,userId3,userCashDiff3,userId4,userCashDiff4';
        $page = 1;
        $limit = 10000;
        do {
            $modRet = $recordMod->queryUPyjOpeningRecordLogByBlock($startTime, $endTime, $field, $limit, $page);
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $ret['code'] = $modRet['code'];
                $ret['msg'] = $modRet['msg'];
                return $ret;
            }
            $list = $modRet['data'];

            foreach ($list as $v) {
                // 玩家映射
                $playerMap = array();
                if (!empty($v['userId1'])) {
                    $playerMap[$v['userId1']] = $v['userCashDiff1'];
                }
                if (!empty($v['userId2'])) {
                    $playerMap[$v['userId2']] = $v['userCashDiff2'];
                }
                if (!empty($v['userId3'])) {
                    $playerMap[$v['userId3']] = $v['userCashDiff3'];
                }
                if (!empty($v['userId4'])) {
                    $playerMap[$v['userId4']] = $v['userCashDiff4'];
                }

                // 记录大赢家
                if ($v['winnerNum'] < $v['playerCount']) {
                    // 记录 winnerId
                    if (!isset($winSort[$v['winnerId']])) {
                        $winSort[$v['winnerId']] = array('uid' => $v['winnerId'], 'count' => 0);
                    }
                    $winSort[$v['winnerId']]['count']++;

                    // 大赢家有多个，那么要去对比分数来确定每一位大赢家
                    if ($v['winnerNum'] > 1) {
                        foreach ($playerMap as $u => $score) {
                            // 字段 winnerId 这位大赢家已经记录了，所以不用重复记录
                            if ($u != $v['winnerId'] && $score == $playerMap[$v['winnerId']]) {
                                if (!isset($winSort[$u])) {
                                    $winSort[$u] = array('uid' => $u, 'count' => 0);
                                }
                                $winSort[$u]['count']++;
                            }
                        }
                    }
                }

                // 参与牌局
                foreach ($playerMap as $u => $score) {
                    if (!isset($gameSort[$u])) {
                        $gameSort[$u] = array('uid' => $u, 'count' => 0);
                    }
                    $gameSort[$u]['count']++;
                }

                // 参与4人局
                if ($v['playerCount'] == 4) {
                    foreach ($playerMap as $u => $score) {
                        if (!isset($game4Sort[$u])) {
                            $game4Sort[$u] = array('uid' => $u, 'count' => 0);
                        }
                        $game4Sort[$u]['count']++;
                    }
                }
            }

            $page++;
        } while(!empty($list));

        // 对大赢家排序
        usort($winSort, function($a, $b) {
            if ($a['count'] == $b['count']) {
                return 0;
            }
            return ($a['count'] > $b['count']) ? -1 : 1;
        });
        $winSort = array_slice($winSort, 0, $rankCount);

        // 对参与牌局排序
        usort($gameSort, function($a, $b) {
            if ($a['count'] == $b['count']) {
                return 0;
            }
            return ($a['count'] > $b['count']) ? -1 : 1;
        });
        $gameSort = array_slice($gameSort, 0, $rankCount);

        // 对参与4人局排序
        usort($game4Sort, function($a, $b) {
            if ($a['count'] == $b['count']) {
                return 0;
            }
            return ($a['count'] > $b['count']) ? -1 : 1;
        });
        $game4Sort = array_slice($game4Sort, 0, $rankCount);

        // 将所有uid记录到一个数组，再统一去取用户名
        $userArr = array();
        foreach ($propSort as $v) {
            $userArr[] = $v['uid'];
        }
        foreach ($winSort as $v) {
            $userArr[] = $v['uid'];
        }
        foreach ($gameSort as $v) {
            $userArr[] = $v['uid'];
        }
        foreach ($game4Sort as $v) {
            $userArr[] = $v['uid'];
        }
        // 去重
        $userArr = array_flip(array_flip($userArr));

        // 获取所有用户名，获得 uid 与 用户名 map
        $userMap = array();
        if (!empty($userArr)) {
            $modRet = $uInfoMod->queryDevUserListByAttr(['userId' => $userArr], 'userId,nickName');
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $ret['code'] = $modRet['code'];
                $ret['msg'] = $modRet['msg'];
                return $ret;
            }
            $userMap = array_combine(array_column($modRet['data'], 'userId'), array_column($modRet['data'], 'nickName'));
        }

        // 插入统计表
        $itemDatas = array();
        for ($i = 0; $i < $rankCount; $i++) {
            $itemDatas[] = array(
                'prop_user_id' => isset($propSort[$i]['uid']) ? $propSort[$i]['uid'] : 0,
                'prop_user_name' => isset($userMap[$propSort[$i]['uid']]) ? $userMap[$propSort[$i]['uid']] : '',
                'prop_nums' => isset($propSort[$i]['num']) ? $propSort[$i]['num'] : 0,
                'win_user_id' => isset($winSort[$i]['uid']) ? $winSort[$i]['uid'] : 0,
                'win_user_name' => isset($userMap[$winSort[$i]['uid']]) ? $userMap[$winSort[$i]['uid']] : '',
                'win_nums' => isset($winSort[$i]['count']) ? $winSort[$i]['count'] : 0,
                'record_user_id' => isset($gameSort[$i]['uid']) ? $gameSort[$i]['uid'] : 0,
                'record_user_name' => isset($userMap[$gameSort[$i]['uid']]) ? $userMap[$gameSort[$i]['uid']] : '',
                'record_nums' => isset($gameSort[$i]['count']) ? $gameSort[$i]['count'] : 0,
                'record4_user_id' => isset($game4Sort[$i]['uid']) ? $game4Sort[$i]['uid'] : 0,
                'record4_user_name' => isset($userMap[$game4Sort[$i]['uid']]) ? $userMap[$game4Sort[$i]['uid']] : '',
                'record4_nums' => isset($game4Sort[$i]['count']) ? $game4Sort[$i]['count'] : 0,
            );
        }
        $modRet = $statMod->insertStatUserRankByData($gameId, $statTime, $itemDatas);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $ret['data'] = $modRet['data'];

        return $ret;
    }

    /************************************ 用户数据，分享统计 ************************************/

    /**
     * 对指定游戏的指定日期进行游戏分享数据统计
     * @author Carter
     */
    public function statGameShareData($gameId, $statTime)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $statMod = new StatGameShareModel();
        $admLogMod = new GameShareLogModel();
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load($gameId, 'CONF_DBTYPE_GAME_LOG_DEV', 0)) {
            try {
                $gmeLogMod = new UShareLogModel();
            } catch (\Exception $e) {
                $ret['code'] = ERRCODE_SYSTEM;
                $ret['msg'] = "数据库连接失败，错误信息：".$e->getMessage();
                $ret['msg'] .= ", CONF_DBTYPE_GAME_LOG_DEV ".var_export(C('CONF_DBTYPE_GAME_LOG_DEV'), true);
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_SYSTEM;
            $ret['msg'] = "数据库配置加载失败，请确认数据库配置信息";
            return $ret;
        }

        /*
         * # 图标点击次数与人数
         * # `game_log_dev`.`u_share_log` WHERE `way` = x
         * # `game_log_dev`.`u_share_log` WHERE `way` = x GROUP BY `userId`
         * # 11 点击"钻石"图标
         * # 12 点击分享领钻"分享朋友圈"按钮
         * # 13 点击大厅"分享"按钮
         * # 14 点击大厅分享"好友/群"按钮
         * # 15 点击大厅分享"朋友圈"按钮
         * # 16 点击"领取钻石"按钮
         * # 17 点击大厅领钻"好友/群"按钮
         * # 18 点击大厅领钻"朋友圈"按钮
         * # 19 点击"活动"按钮
         * # 20 点击活动"好友/群"按钮
         * # 21 点击亲友圈"分享好友/群"按钮
         * # 22 点击亲友圈"分享朋友圈"按钮
         * # 23 点击亲友圈"分享二维码"按钮
         * # 24 创建房间"邀请微信好友"按钮
         *
         * # 成功分享的次数与人数
         * # `sadmin_rls`.`sad_stat_game_share` WHERE `game_id` = xxx AND `source` = xxx
         * # `sadmin_rls`.`sad_stat_game_share` WHERE `game_id` = xxx AND `source` = xxx GROUP BY `user_id`
         * # 2 大厅钻石
         * # 6 大厅分享好友群
         * # 1 大厅分享朋友圈
         * # 7 大厅领取钻石好友群
         * # 3 大厅领取钻石朋友圈
         * # - 活动好友群
         * # 8 亲友圈好友群
         * # 4 亲友圈朋友圈
         * # 9 亲友圈二维码
         * # 5 创建房间邀请
         */
        $awardIconCount = 0; // 分享领钻图标次数
        $awardIconNum = array(); // 分享领钻图标人数
        $awardBtnCount = 0; // 分享领钻按钮次数
        $awardBtnNum = array(); // 分享领钻按钮人数
        $awardSuccCount = 0; // 分享领钻成功次数
        $awardSuccNum = array(); // 分享领钻成功人数
        $shareIconCount = 0; // 固定分享图标次数
        $shareIconNum = array(); // 固定分享图标人数
        $shareFriendCount = 0; // 固定分享好友群次数
        $shareFriendNum = array(); // 固定分享好友群人数
        $shareSocialCount = 0; // 固定分享朋友圈次数
        $shareSocialNum = array(); // 固定分享朋友圈人数
        $shareFsuccCount = 0; // 固定分享好友群成功次数
        $shareFsuccNum = array(); // 固定分享好友群成功人数
        $shareSsuccCount = 0; // 固定分享朋友圈成功次数
        $shareSsuccNum = array(); // 固定分享朋友圈成功人数
        $diamondIconCount = 0; // 领取钻石图标次数
        $diamondIconNum = array(); // 领取钻石图标人数
        $diamondFriendCount = 0; // 领取钻石好友群次数
        $diamondFriendNum = array(); // 领取钻石好友群人数
        $diamondSocialCount = 0; // 领取钻石朋友圈次数
        $diamondSocialNum = array(); // 领取钻石朋友圈人数
        $diamondFsuccCount = 0; // 领取钻石好友群成功次数
        $diamondFsuccNum = array(); // 领取钻石好友群成功人数
        $diamondSsuccCount = 0; // 领取钻石朋友圈成功次数
        $diamondSsuccNum = array(); // 领取钻石朋友圈成功人数
        $activityIconCount = 0; // 活动图标次数
        $activityIconNum = array(); // 活动图标人数
        $activityBtnCount = 0; // 活动按钮次数
        $activityBtnNum = array(); // 活动按钮人数
        $activitySuccCount = 0; // 活动成功次数
        $activitySuccNum = array(); // 活动成功人数
        $clubFriendCount = 0; // 亲友圈好友群次数
        $clubFriendNum = array(); // 亲友圈好友群人数
        $clubSocialCount = 0; // 亲友圈朋友圈次数
        $clubSocialNum = array(); // 亲友圈朋友圈人数
        $clubQrcodeCount = 0; // 亲友圈二维码次数
        $clubQrcodeNum = array(); // 亲友圈二维码人数
        $clubFsuccCount = 0; // 亲友圈好友群成功次数
        $clubFsuccNum = array(); // 亲友圈好友群成功人数
        $clubSsuccCount = 0; // 亲友圈朋友圈成功次数
        $clubSsuccNum = array(); // 亲友圈朋友圈成功人数
        $clubQsuccCount = 0; // 亲友圈二维码成功次数
        $clubQsuccNum = array(); // 亲友圈二维码成功人数
        $roomBtnCount = 0; // 房间邀请按钮次数
        $roomBtnNum = array(); // 房间邀请按钮人数
        $roomSuccCount = 0; // 房间邀请成功次数
        $roomSuccNum = array(); // 房间邀请成功人数

        // 获取游戏分享日志
        $attr = array(
            'startTime' => date("Y-m-d 00:00:00", $statTime),
            'endTime' => date("Y-m-d 23:59:59", $statTime),
        );
        $modRet = $gmeLogMod->queryUShareAllLogByAttr($attr, 'userId,way');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        foreach ($modRet['data'] as $v) {
            switch ($v['way']) {
                // 11 点击"钻石"图标 - $awardIconCount, $awardIconNum
                case '11':
                    $awardIconCount++;
                    $awardIconNum[] = $v['userId'];
                    break;
                // 12 点击分享领钻"分享朋友圈"按钮 - $awardBtnCount, $awardBtnNum
                case '12':
                    $awardBtnCount++;
                    $awardBtnNum[] = $v['userId'];
                    break;
                // 13 点击大厅"分享"按钮 - $shareIconCount, $shareIconNum
                case '13':
                    $shareIconCount++;
                    $shareIconNum[] = $v['userId'];
                    break;
                // 14 点击大厅分享"好友/群"按钮 - $shareFriendCount, $shareFriendNum
                case '14':
                    $shareFriendCount++;
                    $shareFriendNum[] = $v['userId'];
                    break;
                // 15 点击大厅分享"朋友圈"按钮 - $shareSocialCount, $shareSocialNum
                case '15':
                    $shareSocialCount++;
                    $shareSocialNum[] = $v['userId'];
                    break;
                // 16 点击"领取钻石"按钮 - $diamondIconCount, $diamondIconNum
                case '16':
                    $diamondIconCount++;
                    $diamondIconNum[] = $v['userId'];
                    break;
                // 17 点击大厅领钻"好友/群"按钮 - $diamondFriendCount, $diamondFriendNum
                case '17':
                    $diamondFriendCount++;
                    $diamondFriendNum[] = $v['userId'];
                    break;
                // 18 点击大厅领钻"朋友圈"按钮 - $diamondSocialCount, $diamondSocialNum
                case '18':
                    $diamondSocialCount++;
                    $diamondSocialNum[] = $v['userId'];
                    break;
                // 19 点击"活动"按钮 - $activityIconCount, $activityIconNum
                case '19':
                    $activityIconCount++;
                    $activityIconNum[] = $v['userId'];
                    break;
                // 20 点击活动"好友/群"按钮 - $activityBtnCount, $activityBtnNum
                case '20':
                    $activityBtnCount++;
                    $activityBtnNum[] = $v['userId'];
                    break;
                // 21 点击亲友圈"分享好友/群"按钮 - $clubFriendCount, $clubFriendNum
                case '21':
                    $clubFriendCount++;
                    $clubFriendNum[] = $v['userId'];
                    break;
                // 22 点击亲友圈"分享朋友圈"按钮 - $clubSocialCount, $clubSocialNum
                case '22':
                    $clubSocialCount++;
                    $clubSocialNum[] = $v['userId'];
                    break;
                // 23 点击亲友圈"分享二维码"按钮 - $clubQrcodeCount, $clubQrcodeNum
                case '23':
                    $clubQrcodeCount++;
                    $clubQrcodeNum[] = $v['userId'];
                    break;
                // 24 创建房间"邀请微信好友"按钮 - $roomBtnCount, $roomBtnNum
                case '24':
                    $roomBtnCount++;
                    $roomBtnNum[] = $v['userId'];
                    break;
            }
        }

        // 人数进行去重后统计
        $awardIconNum = count(array_flip($awardIconNum));
        $awardBtnNum = count(array_flip($awardBtnNum));
        $shareIconNum = count(array_flip($shareIconNum));
        $shareFriendNum = count(array_flip($shareFriendNum));
        $shareSocialNum = count(array_flip($shareSocialNum));
        $diamondIconNum = count(array_flip($diamondIconNum));
        $diamondFriendNum = count(array_flip($diamondFriendNum));
        $diamondSocialNum = count(array_flip($diamondSocialNum));
        $activityIconNum = count(array_flip($activityIconNum));
        $activityBtnNum = count(array_flip($activityBtnNum));
        $clubFriendNum = count(array_flip($clubFriendNum));
        $clubSocialNum = count(array_flip($clubSocialNum));
        $clubQrcodeNum = count(array_flip($clubQrcodeNum));
        $roomBtnNum = count(array_flip($roomBtnNum));

        // 获取后台分享日志
        $attr = array(
            'gameId' => $gameId,
            'createDate' => date("Y-m-d", $statTime),
        );
        $modRet = $admLogMod->queryGameShareLogByAttr($attr, 'source,user_id');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        foreach ($modRet['data'] as $v) {
            switch ($v['source']) {
                // 1 大厅分享朋友圈 - $shareSsuccCount, $shareSsuccNum
                case '1':
                    $shareSsuccCount++;
                    $shareSsuccNum[] = $v['user_id'];
                    break;
                // 2 大厅钻石 - $awardSuccCount, $awardSuccNum
                case '2':
                    $awardSuccCount++;
                    $awardSuccNum[] = $v['user_id'];
                    break;
                // 3 大厅领取钻石朋友圈 - $diamondSsuccCount, $diamondSsuccNum
                case '3':
                    $diamondSsuccCount++;
                    $diamondSsuccNum[] = $v['user_id'];
                    break;
                // 4 亲友圈朋友圈 - $clubSsuccCount, $clubSsuccNum
                case '4':
                    $clubSsuccCount++;
                    $clubSsuccNum[] = $v['user_id'];
                    break;
                // 5 创建房间邀请 - $roomSuccCount, $roomSuccNum
                case '5':
                    $roomSuccCount++;
                    $roomSuccNum[] = $v['user_id'];
                    break;
                // 6 大厅分享好友群 - $shareFsuccCount, $shareFsuccNum
                case '6':
                    $shareFsuccCount++;
                    $shareFsuccNum[] = $v['user_id'];
                    break;
                // 7 大厅领取钻石好友群 - $diamondFsuccCount, $diamondFsuccNum
                case '7':
                    $diamondFsuccCount++;
                    $diamondFsuccNum[] = $v['user_id'];
                    break;
                // 8 亲友圈好友群 - $clubFsuccCount, $clubFsuccNum
                case '8':
                    $clubFsuccCount++;
                    $clubFsuccNum[] = $v['user_id'];
                    break;
                // 9 亲友圈二维码 - $clubQsuccCount, $clubQsuccNum
                case '9':
                    $clubQsuccCount++;
                    $clubQsuccNum[] = $v['user_id'];
                    break;
            }
        }

        // 人数进行去重后统计
        $shareSsuccNum = count(array_flip($shareSsuccNum));
        $awardSuccNum = count(array_flip($awardSuccNum));
        $diamondSsuccNum = count(array_flip($diamondSsuccNum));
        $clubSsuccNum = count(array_flip($clubSsuccNum));
        $roomSuccNum = count(array_flip($roomSuccNum));
        $shareFsuccNum = count(array_flip($shareFsuccNum));
        $diamondFsuccNum = count(array_flip($diamondFsuccNum));
        $clubFsuccNum = count(array_flip($clubFsuccNum));
        $clubQsuccNum = count(array_flip($clubQsuccNum));

        // 插入统计数据
        $statData = array(
            'awardIconCount' => $awardIconCount,
            'awardIconNum' => $awardIconNum,
            'awardBtnCount' => $awardBtnCount,
            'awardBtnNum' => $awardBtnNum,
            'awardSuccCount' => $awardSuccCount,
            'awardSuccNum' => $awardSuccNum,
            'shareIconCount' => $shareIconCount,
            'shareIconNum' => $shareIconNum,
            'shareFriendCount' => $shareFriendCount,
            'shareFriendNum' => $shareFriendNum,
            'shareSocialCount' => $shareSocialCount,
            'shareSocialNum' => $shareSocialNum,
            'shareFsuccCount' => $shareFsuccCount,
            'shareFsuccNum' => $shareFsuccNum,
            'shareSsuccCount' => $shareSsuccCount,
            'shareSsuccNum' => $shareSsuccNum,
            'diamondIconCount' => $diamondIconCount,
            'diamondIconNum' => $diamondIconNum,
            'diamondFriendCount' => $diamondFriendCount,
            'diamondFriendNum' => $diamondFriendNum,
            'diamondSocialCount' => $diamondSocialCount,
            'diamondSocialNum' => $diamondSocialNum,
            'diamondFsuccCount' => $diamondFsuccCount,
            'diamondFsuccNum' => $diamondFsuccNum,
            'diamondSsuccCount' => $diamondSsuccCount,
            'diamondSsuccNum' => $diamondSsuccNum,
            'activityIconCount' => $activityIconCount,
            'activityIconNum' => $activityIconNum,
            'activityBtnCount' => $activityBtnCount,
            'activityBtnNum' => $activityBtnNum,
            'activitySuccCount' => $activitySuccCount,
            'activitySuccNum' => $activitySuccNum,
            'clubFriendCount' => $clubFriendCount,
            'clubFriendNum' => $clubFriendNum,
            'clubSocialCount' => $clubSocialCount,
            'clubSocialNum' => $clubSocialNum,
            'clubQrcodeCount' => $clubQrcodeCount,
            'clubQrcodeNum' => $clubQrcodeNum,
            'clubFsuccCount' => $clubFsuccCount,
            'clubFsuccNum' => $clubFsuccNum,
            'clubSsuccCount' => $clubSsuccCount,
            'clubSsuccNum' => $clubSsuccNum,
            'clubQsuccCount' => $clubQsuccCount,
            'clubQsuccNum' => $clubQsuccNum,
            'roomBtnCount' => $roomBtnCount,
            'roomBtnNum' => $roomBtnNum,
            'roomSuccCount' => $roomSuccCount,
            'roomSuccNum' => $roomSuccNum,
        );
        $modRet = $statMod->insertGameShareStatByDate($gameId, $statTime, $statData);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $ret['data']['id'] = $modRet['data'];

        return $ret;
    }

    /************************************ 游戏数据，对局统计 ************************************/

    /**
     * 游戏对局统计主逻辑
     * @author Carter
     */
    public function statGameRoundByDate($gameId, $statTime)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $statMod = new StatGameitemModel();
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load($gameId, 'CONF_DBTYPE_GAME_LOG_DEV', 0)) {
            try {
                $roundMod = new UPyjRecordLogModel();
                $userMod = new UPyjUserRecordModel();
            } catch (\Exception $e) {
                $conMsg = "CONF_DBTYPE_GAME_LOG_DEV ".var_export(C('CONF_DBTYPE_GAME_LOG_DEV'), true);
                $ret['code'] = ERRCODE_SYSTEM;
                $ret['msg'] = "数据库连接失败，错误信息：".$e->getMessage().", {$conMsg}";
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_SYSTEM;
            $ret['msg'] = "数据库配置加载失败，请确认数据库配置信息";
            return $ret;
        }

        // 开局次数：创建了房间并开始行牌（包括第一局未打完没有钻石消耗的情况）
        // game_region_log_dev.u_pyj_record_log 指定日期内的总记录数
        $createCount = 0;

        // 成功开局次数：至少完成了一局的房间（一定存在钻石消耗）
        // game_region_log_dev.u_pyj_record_log 指定日期内 roundCount 大于 0 的记录
        $createAccessCount = 0;

        // 平均每场时长：当日所有成功开局的平均对局时间（不统计没有结算的）
        // game_region_log_dev.u_pyj_record_log 的 gameStopTime - gameStartTime 除以总场数
        $totalAverageTime = 0;
        // 累计所有场次的耗时总长，秒
        $totalTime = 0;

        // 平均每局时长：当日所有用户成功开局的每一局开始到每一局结算的平均时间
        // game_region_log_dev.u_pyj_user_record 每一局的 gameStopTime - gameStartTime 除以总局数
        $itemAverageTime = 0;
        // 每一局的耗时总长，秒
        $totalRoundTime = 0;
        // 有效统计的局数
        $roundCount = 0;

        // 大赢家平均胜分：当日每一场中的大赢家平均得分（不统计没有结算的）
        // game_region_log_dev.u_pyj_record_log roundCount > 0 的最高分，按 winnerNum 是多少就有多少个大赢家算
        $winAverageIntegral = 0;
        // 大赢家累计总分
        $winIntegralAmount = 0;
        // 大赢家人次
        $winIntegralCount = 0;

        // 每局平均胜分：当日每一局中的赢家平均得分
        // game_region_log_dev.u_pyj_user_record 每一局的赢家得分的平均分
        $averageIntegral = 0;
        // 每局赢家累计总分
        $roundIntegralAmount = 0;
        // 每局赢家人次
        $roundIntegralCount = 0;

        // 获取场流水记录
        $field = "roundCount,gameStartTime,gameStopTime,winnerNum,";
        $field .= "userCashDiff1,userCashDiff2,userCashDiff3,userCashDiff4";
        $modRet = $roundMod->queryUPyjRecordLogListByDate($statTime, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        if (empty($modRet['data'])) {
            // 线上偶发统计不到任何牌局数据，埋个点持续跟踪下
            $errMsg = "{$gameId} get record empty, sql ".$roundMod->getLastSql();
            set_exception(__FILE__, __LINE__, "[statGameRoundByDate] {$errMsg}");
        }
        foreach ($modRet['data'] as $v) {
            // 每条记录就记一次开局次数
            $createCount++;

            // roundCount > 0 的才算成功开局次数，且进行大赢家统计
            if ($v['roundCount'] > 0) {
                // 成功开局次数
                $createAccessCount++;

                // 本场耗时
                $totalTime += strtotime($v['gameStopTime']) - strtotime($v['gameStartTime']);

                // 通过大赢家uid确认本场大赢家得分
                $win = max(array(
                    $v['userCashDiff1'],
                    $v['userCashDiff2'],
                    $v['userCashDiff3'],
                    $v['userCashDiff4'],
                ));

                // 有可能流局，流局认为没有大赢家
                if ($win > 0) {
                    // 累计大赢家的时候，以winnerNum作为赢家人次
                    $winIntegralAmount += $win * $v['winnerNum'];
                    $winIntegralCount += $v['winnerNum'];
                }
            }
        }

        // 计算每场平均时长，不统计没有结算的
        $totalAverageTime = round($totalTime / $createAccessCount);

        // 计算大赢家平均胜分
        $winAverageIntegral = round($winIntegralAmount * 1000 / $winIntegralCount);

        // 分段获取，一次性获取可能数据量太大造成内存溢出
        $offset = 0;
        // 每次获取长度
        $limit = 10000;
        while (true) {
            $modRet = $userMod->queryUPyjUserRecordGroupByTime($statTime, $offset, $limit);
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $ret['code'] = $modRet['code'];
                $ret['msg'] = $modRet['msg'];
                return $ret;
            }
            if (empty($modRet['data'])) {
                break;
            }
            foreach ($modRet['data'] as $v) {
                // 统计每局耗时
                $totalRoundTime += strtotime($v['date']) - strtotime($v['gameStartTime']);
                $roundCount++;

                // 计算本局赢家，可能存在多个并列赢家
                $scoreArr = array();
                if ($v['userinfo1'] != 'null') {
                    $info = json_decode($v['userinfo1'], true);
                    $scoreArr[] = $info['cashDiff'];
                }
                if ($v['userinfo2'] != 'null') {
                    $info = json_decode($v['userinfo2'], true);
                    $scoreArr[] = $info['cashDiff'];
                }
                if ($v['userinfo3'] != 'null') {
                    $info = json_decode($v['userinfo3'], true);
                    $scoreArr[] = $info['cashDiff'];
                }
                if ($v['userinfo4'] != 'null') {
                    $info = json_decode($v['userinfo4'], true);
                    $scoreArr[] = $info['cashDiff'];
                }

                $winNum = 0;
                $winScore = 0;
                foreach ($scoreArr as $score) {
                    if ($score > $winScore) {
                        $winNum = 1;
                        $winScore = $score;
                    } else if ($score == $winScore) {
                        $winNum++;
                    }
                }

                // 赢分为0表示流局，流局不在平均分统计范围
                if ($winScore > 0) {
                    $roundIntegralAmount += $winScore;
                    $roundIntegralCount += $winNum;
                }
            }

            $offset += $limit;
        }

        // 计算每局平均时长
        $itemAverageTime = round($totalRoundTime / $roundCount);

        // 计算平均胜分
        $averageIntegral = round($roundIntegralAmount * 1000 / $roundIntegralCount);

        $statData = array(
            // 开局次数
            'createCount' => $createCount,
            // 成功开局次数
            'createAccessCount' => $createAccessCount,
            // 平均每场时长
            'totalAverageTime' => $totalAverageTime,
            // 平均每局时长
            'itemAverageTime' => $itemAverageTime,
            // 大赢家平均胜分
            'winAverageIntegral' => $winAverageIntegral,
            // 每局平均胜分
            'averageIntegral' => $averageIntegral,
        );

        // 统计完毕，数据入库。新数据插入，已存在则更新
        $modRet = $statMod->insertStatGameItem($gameId, $statTime, $statData);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $ret['data']['id'] = $modRet['data']['id'];

        return $ret;
    }

    /************************************ 游戏数据，玩法统计 ************************************/

    /**
     * 游戏玩法统计主逻辑
     * @author Carter
     */
    public function statGameRoomByDate($gameId, $statTime)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $statMod = new StatGameRoomModel();
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load($gameId, 'CONF_DBTYPE_GAME_LOG_DEV', 0)) {
            try {
                $roundMod = new UPyjRecordLogModel();
            } catch (\Exception $e) {
                $ret['code'] = ERRCODE_SYSTEM;
                $ret['msg'] = "数据库连接失败，错误信息：".$e->getMessage();
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_SYSTEM;
            $ret['msg'] = "数据库配置加载失败，请确认数据库配置信息1";
            return $ret;
        }

        /*
         * # 开局次数：创建了房间并开始行牌（包括第一局未打完没有钻石消耗的情况）
         * 普通房：u_pyj_record_log clubId = 0
         * 俱乐部：u_pyj_record_log clubId > 0
         *
         * # 成功开局次数：至少完成了一局的房间（一定存在钻石消耗）
         * 普通房：u_pyj_record_log clubId = 0 AND roundCount > 0
         * 俱乐部：u_pyj_record_log clubId > 0 AND roundCount > 0
         *
         * # 子玩法人数：对应玩法下，成功开局房间内的玩家累计人数
         * u_pyj_record_log roundCount > 0，指定的gameId，userId1,userId2,userId3,userId4大于0的人数
         *
         * # 子玩法次数：对应玩法下，成功开局次数
         * u_pyj_record_log roundCount > 0，指定的gameId开局次数
         *
         * # 4人局次数：当日所有4人房的成功开局次数
         * userId1,userId2,userId3,userId4大于0的人数为4的局数
         *
         * # 3人局次数：当日所有3人房的成功开局次数
         * userId1,userId2,userId3,userId4大于0的人数为3的局数
         *
         * # 2人局次数：当日所有2人房的成功开局次数
         * userId1,userId2,userId3,userId4大于0的人数为2的局数
         */

        $statData = array(
            // 所有玩法的总计
            0 => array(
                // 普通房间
                $statMod::ROOM_TYPE_GAME => array(
                    // 开局次数
                    'createCount' => 0,
                    // 成功开局次数
                    'createAccessCount' => 0,
                    // 2人房成功开局次数
                    'twoCount' => 0,
                    // 3人房成功开局次数
                    'threeCount' => 0,
                    // 4人房成功开局次数
                    'fourCount' => 0,
                ),
                // 俱乐部房间
                $statMod::ROOM_TYPE_CLUB => array(
                    // 开局次数
                    'createCount' => 0,
                    // 成功开局次数
                    'createAccessCount' => 0,
                    // 2人房成功开局次数
                    'twoCount' => 0,
                    // 3人房成功开局次数
                    'threeCount' => 0,
                    // 4人房成功开局次数
                    'fourCount' => 0,
                ),
            ),
        );

        // 获取场流水记录
        $field = "gameId,clubId,roundCount,userId1,userId2,userId3,userId4";
        $modRet = $roundMod->queryUPyjRecordLogListByDate($statTime, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        if (empty($modRet['data'])) {
            $ret['code'] = ERRCODE_DATA_ERR;
            $ret['msg'] = '查询不到任何数据，游戏牌局日志已被清除或无牌局日志，不能进行统计';
            return $ret;
        }
        foreach ($modRet['data'] as $v) {
            // 按玩法统计，如果某玩法一局都没打，那么这个玩法的那一天的数据也不会统计
            if (!isset($statData[$v['gameId']])) {
                $statData[$v['gameId']] = array(
                    $statMod::ROOM_TYPE_GAME => array(
                        'createCount' => 0,
                        'createAccessCount' => 0,
                        'twoCount' => 0,
                        'threeCount' => 0,
                        'fourCount' => 0,
                    ),
                    $statMod::ROOM_TYPE_CLUB => array(
                        'createCount' => 0,
                        'createAccessCount' => 0,
                        'twoCount' => 0,
                        'threeCount' => 0,
                        'fourCount' => 0,
                    ),
                );
            }

            // 房间类型
            if ($v['clubId'] == 0) {
                // 普通房间
                $roomType = $statMod::ROOM_TYPE_GAME;
            } else {
                // 俱乐部房间
                $roomType = $statMod::ROOM_TYPE_CLUB;
            }

            // 开局次数
            $statData[0][$roomType]['createCount']++;
            $statData[$v['gameId']][$roomType]['createCount']++;

            // 成功开局
            if ($v['roundCount'] > 0) {
                // 成功开局次数
                $statData[0][$roomType]['createAccessCount']++;
                $statData[$v['gameId']][$roomType]['createAccessCount']++;

                // 人数
                $playCount = 0;
                if ($v['userId1'] > 0) {
                    $playCount++;
                }
                if ($v['userId2'] > 0) {
                    $playCount++;
                }
                if ($v['userId3'] > 0) {
                    $playCount++;
                }
                if ($v['userId4'] > 0) {
                    $playCount++;
                }

                // 根据实际参与人数统计房间人数
                if (2 == $playCount) {
                    $statData[0][$roomType]['twoCount']++;
                    $statData[$v['gameId']][$roomType]['twoCount']++;
                } else if (3 == $playCount) {
                    $statData[0][$roomType]['threeCount']++;
                    $statData[$v['gameId']][$roomType]['threeCount']++;
                } else if (4 == $playCount) {
                    $statData[0][$roomType]['fourCount']++;
                    $statData[$v['gameId']][$roomType]['fourCount']++;
                } else {
                    set_exception(__FILE__, __LINE__, "[statGameRoomByDate] err data:".var_export($v, true));
                }
            }
        }

        // 获取该游戏统计日期的统计数据，用来判断要统计的数据是否已存在，已存在的只更新不插入
        $attr = array(
            'game_id' => $gameId,
            'data_time' => $statTime,
        );
        $field = 'id,game_item_id,room_type';
        $modRet = $statMod->queryStatGameRoomAllList($attr, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $roomList = array();
        foreach ($modRet['data'] as $v) {
            $roomList[$v['game_item_id']][$v['room_type']] = $v['id'];
        }

        // 遍历统计数据，入库操作
        foreach ($statData as $itemId => $data) {
            foreach ($data as $roomType => $v) {
                // 存在，更新
                if (isset($roomList[$itemId][$roomType])) {
                    $updateId = $roomList[$itemId][$roomType];
                    $modRet = $statMod->updateStatGameRoom($updateId, $v);
                    if (ERRCODE_SUCCESS !== $modRet['code']) {
                        $ret['code'] = $modRet['code'];
                        $ret['msg'] = $modRet['msg'];
                        return $ret;
                    }
                    $ret['data']['updateId'][] = $updateId;
                }
                // 不存在，插入
                else {
                    $v['gameId'] = $gameId;
                    $v['gameItemId'] = $itemId;
                    $v['roomType'] = $roomType;
                    $v['dataTime'] = $statTime;
                    $modRet = $statMod->insertStatGameRoom($v);
                    if (ERRCODE_SUCCESS !== $modRet['code']) {
                        $ret['code'] = $modRet['code'];
                        $ret['msg'] = $modRet['msg'];
                        return $ret;
                    }
                    $ret['data']['insertId'][] = $modRet['data']['id'];
                }
            }
        }

        return $ret;
    }

    /************************************ 钻石产出统计 ************************************/

    /**
     * 钻石产出获取指定游戏已统计数据中，最后一天的统计时间
     * @author Carter
     */
    public function getDiamondProduceLastStatTime($gameId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $statMod = new StatDiamondProduceModel();
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load($gameId, 'CONF_DBTYPE_CLUB_LOG', 0)) {
            try {
                $cardMod = new LogGamecardModel();
                $formalMod = new LogFailFormalGamecardModel();
            } catch (\Exception $e) {
                $ret['code'] = ERRCODE_SYSTEM;
                $ret['msg'] = "数据库连接失败，错误信息：".$e->getMessage();
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_SYSTEM;
            $ret['msg'] = "数据库配置加载失败，请确认数据库配置信息1";
            return $ret;
        }
        if (true === $confSer->load($gameId, 'CONF_DBTYPE_GAME_LOG_DEV', 0)) {
            try {
                $propMod = new UPropsLogModel();
            } catch (\Exception $e) {
                $ret['code'] = ERRCODE_SYSTEM;
                $ret['msg'] = "数据库连接失败，错误信息：".$e->getMessage();
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_SYSTEM;
            $ret['msg'] = "数据库配置加载失败，请确认数据库配置信息";
            return $ret;
        }

        $modRet = $statMod->queryStatDiamondProduceLastTime($gameId);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        if (empty($modRet['data'])) {
            $lastStatTime = 0;
        } else {
            $lastStatTime = $modRet['data']['stat_time'];
        }

        // 如果最后统计时间为0，表示该游戏从未进行过统计，那从所有俱乐部及后端表中取得最早存在数据的日期，从那一天开始统计
        if (0 == $lastStatTime) {
            // 俱乐部钻石流水最早一条记录的时间
            $modRet = $cardMod->queryFirstClubGamecardLog($gameId);
            if (ERRCODE_DB_DATA_EMPTY === $modRet['code']) {
                $modRet['data']['createDate'] = date('Y-m-d');
            } else if (ERRCODE_SUCCESS !== $modRet['code']) {
                $ret['code'] = $modRet['code'];
                $ret['msg'] = $modRet['msg'];
                return $ret;
            }
            $firstClubTime = strtotime($modRet['data']['createDate']);

            // 俱乐部邮件送钻最早一条记录时间
            $modRet = $formalMod->queryFirstClubFormalGamecardLog($gameId);
            if (ERRCODE_DB_DATA_EMPTY === $modRet['code']) {
                $modRet['data']['createDate'] = date('Y-m-d');
            } else if (ERRCODE_SUCCESS !== $modRet['code']) {
                $ret['code'] = $modRet['code'];
                $ret['msg'] = $modRet['msg'];
                return $ret;
            }
            $firstFormalTime = strtotime($modRet['data']['createDate']);

            // 游戏资产日志最早一条记录时间
            $modRet = $propMod->queryFirstGamePropsLog();
            if (ERRCODE_DB_DATA_EMPTY === $modRet['code']) {
                $modRet['data']['createTime'] = date('Y-m-d');
            } else if (ERRCODE_SUCCESS !== $modRet['code']) {
                $ret['code'] = $modRet['code'];
                $ret['msg'] = $modRet['msg'];
                return $ret;
            }
            $firstPropsTime = strtotime($modRet['data']['createTime']);

            // 取最早时间，然后将最早时间的前一天作为上一次统计日期，那么接下来的统计就是从这最早时间开始统计
            $minTime = min($firstClubTime, $firstFormalTime, $firstPropsTime);
            $lastStatTime = strtotime(date('Y-m-d', $minTime)) - 86400;
        }

        $ret['data'] = $lastStatTime;

        return $ret;
    }

    /**
     * 对指定游戏的指定日期进行钻石产出统计
     * @author Carter
     */
    public function statDiamondProduceByGameAndTime($gameId, $statTime)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $statDate = date('Y-m-d', $statTime);

        $statMod = new StatDiamondProduceModel();
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load($gameId, 'CONF_DBTYPE_CLUB_LOG', 0)) {
            try {
                $cardMod = new LogGamecardModel();
                $formalMod = new LogFailFormalGamecardModel();
            } catch (\Exception $e) {
                $ret['code'] = ERRCODE_SYSTEM;
                $ret['msg'] = "数据库连接失败，错误信息：".$e->getMessage();
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_SYSTEM;
            $ret['msg'] = "数据库配置加载失败，请确认数据库配置信息1";
            return $ret;
        }
        if (true === $confSer->load($gameId, 'CONF_DBTYPE_GAME_LOG_DEV', 0)) {
            try {
                $propMod = new UPropsLogModel();
            } catch (\Exception $e) {
                $ret['code'] = ERRCODE_SYSTEM;
                $ret['msg'] = "数据库连接失败，错误信息：".$e->getMessage();
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_SYSTEM;
            $ret['msg'] = "数据库配置加载失败，请确认数据库配置信息";
            return $ret;
        }

        /*
         * # 赠送代理商钻石：通过俱乐部操作中的赠送钻石，赠送给代理商的钻石数量。
         * # club_log.log_gamecard, source=3 系统赠送
         *
         * # 赠送专属钻石：通过俱乐部操作中的赠送专属钻石，赠送给代理商的专属钻石数量。
         * # club_log.log_gamecard, source=7 俱乐部专属钻石，只统计changeNum为正的流水
         *
         * # 代理商返钻：下级代理商购钻后，上级代理获得的返钻数量。
         * # club_log.log_gamecard, source=6 代理商返钻
         *
         * # 代理商购买：代理商通过代理商后台购买的钻石数量。
         * # club_log.log_gamecard, source=2 代理商购买
         *
         * # 游戏内购：玩家通过游戏内商城购买的钻石数量。
         * # select * from huain_log_dev.u_props_log where way=0 and propsid=10008
         *
         * # 每日分享：玩家通过游戏内的每日分享得到的钻石数量。
         * # select * from huain_log_dev.u_props_log where way=12 and propsid=10008
         *
         * # 邀请好友：玩家通过游戏内的邀请好友功能获得的钻石数量。
         * # select * from huain_log_dev.u_props_log where way IN(21,32) and propsid=10008
         *
         * # 赠送玩家钻石：通过后台以邮件形式发送给玩家的钻石数量。
         * # club_log.log_fail_formal_gamecard 全部记录 + game_log_dev.u_props_log where way = 17 and propsid = 10008
         */
        $statData = array(
            'giftAgent' => 0,      // 赠送代理商钻石
            'giftExclusive' => 0,  // 赠送专属钻石
            'superiorAward' => 0,  // 代理商返钻
            'agentPurchase' => 0,  // 代理商购买
            'mallPurchase' => 0,   // 游戏内购
            'shareAward' => 0,     // 每日分享
            'inviteAward' => 0,    // 邀请好友
            'adminDeliver' => 0,   // 赠送玩家钻石
        );

        // 获取俱乐部钻石产出记录
        $modRet = $cardMod->queryClubGamecardProduceLogList($gameId, $statDate);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        foreach ($modRet['data'] as $v) {
            switch ($v['source']) {
                // 2 代理商购买 - agentPurchase
                case '2':
                    if ($v['changeNum'] < 0) {
                        $errMsg = "club_log.log_gamecard negative changeNum {$v['changeNum']}, id {$v['id']}";
                        set_exception(__FILE__, __LINE__, "[statDiamondProduceByGameAndTime] {$errMsg}");
                        continue;
                    }
                    $statData['agentPurchase'] += $v['changeNum'];
                    break;
                // 3 系统赠送 - giftAgent
                case '3':
                    if ($v['changeNum'] < 0) {
                        $errMsg = "club_log.log_gamecard negative changeNum {$v['changeNum']}, id {$v['id']}";
                        set_exception(__FILE__, __LINE__, "[statDiamondProduceByGameAndTime] {$errMsg}");
                        continue;
                    }
                    $statData['giftAgent'] += $v['changeNum'];
                    break;
                // 6 代理商返钻 - superiorAward
                case '6':
                    if ($v['changeNum'] < 0) {
                        $errMsg = "club_log.log_gamecard negative changeNum {$v['changeNum']}, id {$v['id']}";
                        set_exception(__FILE__, __LINE__, "[statDiamondProduceByGameAndTime] {$errMsg}");
                        continue;
                    }
                    $statData['superiorAward'] += $v['changeNum'];
                    break;
                // 7 俱乐部专属钻石 - giftExclusive
                case '7':
                    if ($v['changeNum'] > 0) {
                        $statData['giftExclusive'] += $v['changeNum'];
                    }
                    break;
            }
        }

        // 获取俱乐部后台邮件送钻记录
        $modRet = $formalMod->queryClubFailFormalGamecardLogList($gameId, $statDate);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        foreach ($modRet['data'] as $v) {
            $statData['adminDeliver'] += $v['gameCard'];
        }

        // 获取游戏资产变化记录
        $modRet = $propMod->queryGamePropsDiamondProduceList($gameId, $statTime);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        foreach ($modRet['data'] as $v) {
            if ($v['propsNum'] < 0) {
                $errMsg = "game_log_dev.u_props_log negative propsNum {$v['propsNum']}, id {$v['id']}";
                set_exception(__FILE__, __LINE__, "[statDiamondProduceByGameAndTime] {$errMsg}");
                continue;
            }
            switch ($v['way']) {
                // 0 游戏内购 - mallPurchase
                case '0':
                    $statData['mallPurchase'] += $v['propsNum'];
                    break;
                // 12 每日分享 - shareAward
                case '12':
                    $statData['shareAward'] += $v['propsNum'];
                    break;
                // 17 邮件送钻 - adminDeliver
                case '17':
                    $statData['adminDeliver'] += $v['propsNum'];
                    break;
                // 21,32 邀请好友 - inviteAward
                case '21':
                case '32':
                    $statData['inviteAward'] += $v['propsNum'];
                    break;
            }
        }

        // 检查指定游戏的指定日期是否已统计，未统计则插入，已统计则更新
        $attr = array(
            'game_id' => $gameId,
            'stat_time' => $statTime,
        );
        $modRet = $statMod->queryStatDiamondProduceAllList($attr, 'id');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        if (empty($modRet['data'])) {
            // 插入统计数据
            $modRet = $statMod->insertDiamondProduceStatByDate($gameId, $statTime, $statData);
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $ret['code'] = $modRet['code'];
                $ret['msg'] = $modRet['msg'];
                return $ret;
            }
            $ret['data'] = $modRet['data'];
        } else {
            // 更新统计数据
            $tbId = $modRet['data'][0]['id'];
            $modRet = $statMod->updateDiamondProduceStatById($tbId, $statData);
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $ret['code'] = $modRet['code'];
                $ret['msg'] = $modRet['msg'];
                return $ret;
            }
            $ret['data'] = $tbId;
        }

        return $ret;
    }

    /************************************ 钻石消耗统计 ************************************/

    /**
     * 获得单个游戏的钻石消耗记录
     * 处理成前端页面需要的格式
     * @param array $param 筛选条件
     * @param int $limit 每页显示的数据
     * @return array 处理结果
     */
    public function getOneGameDiamondsConsumeStatList($param,$limit = 20)
    {
        $model = D('StatGameitemConsume') ;

        // 设置分页参数名称
        $page = intval( $_GET['p'] ) > 0 ? intval( $_GET['p'] ) : 1 ;
        $where = $param ;

        //列表的分页信息
        $count      = $model->getGameStatDiamondConsumeCount($where);// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,$limit);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        $data       = array(
            'pages' => $show
        );

        //获得查询的日期列表
        $result= $model->getGameStatDiamondConsumeDataList($where['game_id'] , $param ,$limit);

        //表格内容,按天处理数据
        $data['listData'] = &$result;

        return $data ;
    }

    /**
     * 钻石消耗定时任务逻辑
     * @param int $gameId 游戏产品ID
     * @param int $startTime 开始时间的时间戳
     * @param int $endTime 定时任务的结束时间
     */
    public function statGameDiamondLogic($gameId,$startTimeUnixtime,$endTimeUnixtime)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $statModel =  D('StatGameitemConsume'); //统计表model

        try {
            $stat = array() ; //当天统计数据
            //开始时间
            $startTime  = date('Y-m-d H:i:s',$startTimeUnixtime);
            //处理结束时间
            $endTime  = date('Y-m-d H:i:s', $endTimeUnixtime );
            //===================== 新数据处理逻辑 start =========================
            //查询当天的数据存不存在，不存在则先写入数据。
            $nowData = $statModel->getDateTimeIsExtsts($gameId , $startTimeUnixtime );
            //第一次统计时写入基础空数据；
            if(empty($nowData)){
                $baseData = array(
                    'game_id' => $gameId,
                    'data_time'=> $startTimeUnixtime,
                    'create_time' => time()
                );

                $nowData['id'] = $statModel->addEmptyData($baseData);
            }
            //===================== 新数据处理逻辑 end =========================

            //===================== 连接日志库 start =========================
            $confSer = new DbLoadConfigService(); //连接游戏日志库
            if (true === $confSer->load($gameId, 'CONF_DBTYPE_GAME_LOG_DEV', 0)) {
                try {
                    $uPropsMod = new UPropsLogModel();
                    $uPyjRecordLogMod = new UPyjRecordLogModel();
                } catch (\Exception $e) {
                    $ret['code'] = ERRCODE_SYSTEM;
                    $ret['msg'] = ' statGameDiamondLogic - '.$gameId."数据库连接失败，错误信息：".$e->getMessage();
                    set_exception(__FILE__, __LINE__, " statGameDiamondLogic {$gameId} 数据库连接失败，错误信息：".$e->getMessage() );
                    return $ret;
                }
            } else {
                $ret['code'] = ERRCODE_SYSTEM;
                $ret['msg'] = ' statGameDiamondLogic - '.$gameId.'数据库配置加载失败，请确认数据库配置信息';
                set_exception(__FILE__, __LINE__, " statGameDiamondLogic {$gameId} 数据库配置加载失败，请确认数据库配置信息,".$confSer );
                return $ret;
            }
            //===================== 新数据处理逻辑 end =========================

            //===================== 房卡/钻石消耗统计 start =========================
            //计算循环日期
            $where = array();
            $where['createTime'] = array(
                'between' , array( $startTime , $endTime )
            );
            $where['way'] = 100 ;//钻石消耗类型

            $diamondCount = 0 ;
            $stat['diamond_count'] = & $diamondCount;
            $stat['four_diamond'] = 0 ;
            $stat['three_diamond']= 0 ;
            $stat['two_diamond']  = 0;

            $i = 0;
            $pageLimitNum = 1000 ;
            do{
                $num = 0 ;
                $limitStart = $i * $pageLimitNum ;
                //===================== 房卡/钻石消耗日志表处理逻辑 start =========================
                //查询房卡扣费列表。
                $field = ' id,roomId,way,propsNum,propsId,propsBefore,propsAfter,createTime ' ;
                $logDataRet = $uPropsMod->queryUPropsLogsListByWhere($where,$limitStart , $pageLimitNum,$field);
                if($logDataRet['code'] !== ERRCODE_SUCCESS){
                    return $logDataRet ;
                }
                $logData = $logDataRet['data'];

                //房间类型人数判断
                $roomIds = array_column($logData, 'roomId'); //获取房间ID
                if(empty($roomIds ) ){
                    continue;
                }
                //===================== 房卡/钻石消耗日志表处理逻辑 end =========================

                //===================== 开房日志表查询，识别房间类型 start =========================
                $logRoomUserField = 'roomId,ownerId,gameNum,userId1,userId2,userId3,userId4';
                $logRoomUserWhere = array(
                    'roomId' => $roomIds
                );
                $roomDataRet = $uPyjRecordLogMod->queryUPyjRecordListByAttr($logRoomUserWhere, $logRoomUserField);
                if($roomDataRet['code'] !== ERRCODE_SUCCESS){
                    set_exception(__FILE__, __LINE__, " statGameDiamondLogic {$gameId}-查询开房信息日志数据库异常：".$roomDataRet['msg'] );
                    return $roomDataRet ;
                }

                $roomData = $roomDataRet['data'];
                $roomCount = array();
                //识别房间的 人数 类型
                foreach ($roomData as $room){

                    if( (int) $room['userId1'] > 0 ){
                        $roomCount[ $room['roomId'] ] = 1 ;
                    }
                    if( (int) $room['userId2'] > 0 ){
                        $roomCount[ $room['roomId'] ] = 2 ;
                    }
                    if( (int) $room['userId3'] > 0 ){
                        $roomCount[ $room['roomId'] ] = 3 ;
                    }
                    if( (int) $room['userId4'] > 0 ){
                        $roomCount[ $room['roomId'] ] = 4 ;
                    }

                }
                //===================== 开房日志表查询，识别房间类型 end =========================

                foreach($logData as $log){

                    if($roomCount[ $log['roomId'] ] == 4 ){
                        $stat['four_diamond'] += (int) $log['propsNum'];
                    }elseif ( $roomCount[ $log['roomId'] ] == 3  ) {
                        $stat['three_diamond'] += (int)$log['propsNum'];
                    }else{
                        $stat['two_diamond'] += (int)$log['propsNum'];
                    }

                    $diamondCount += (int)$log['propsNum'];
                }

                $num = count($logData);

                $i ++ ;
            }while ($num >= $pageLimitNum );

            //===================== 管理员扣除玩家钻石（后台接口扣除）统计 start =========================
            $where['way'] = 98 ;//管理员扣用户钻石
            $where['propsId'] = 10008 ; // 10008 房卡，10009元宝
            $where['propsNum'] = array('lt' , 0 );
            $manageMinus = (int) $uPropsMod->where($where)->sum('propsNum');
            $stat['manage_minus'] = abs($manageMinus);
            $diamondCount +=  (int)$stat['manage_minus'] ; //钻石消耗总数计算进行累加；

            //===================== 管理员扣除玩家钻石（后台接口扣除）统计 end =========================

            //===================== 管理员扣除代理商钻石（俱乐部扣除）统计 start =========================
            //扣除代理商钻石
            $dbStatus = $confSer->load($gameId, 'CONF_DBTYPE_CLUB_LOG', 0);
            if (true === $dbStatus ) {
                try {
                    $clubLog = new LogGamecardModel();
                    $clubRoomCard = new  LogClubRoomcardModel() ;
                } catch (\Exception $e) {
                    $ret['code'] = ERRCODE_SYSTEM;
                    $ret['msg'] = ' statGameDiamondLogic - '.$gameId."代理商日志库查询失败，错误信息 ：".$e->getMessage();
                    set_exception(__FILE__, __LINE__, " statGameDiamondLogic {$gameId} - 代理商日志库查询失败，错误信息 ".$e->getMessage() );
                    return $ret ;
                }
            } else {
                $ret['code'] = ERRCODE_SYSTEM;
                $ret['msg'] = ' statGameDiamondLogic - '.$gameId."代理商日志库连接失败，错误信息 ：".$dbStatus;
                set_exception(__FILE__, __LINE__, " statGameDiamondLogic {$gameId} - 代理商日志库连接失败，错误信息 ：".$dbStatus );
            }

            // 删除通用钻石
            $countWhere = array(
                'gameId' => $gameId,
                'source' => 4,
                'createTime' => array(
                    array('egt',$startTime),
                    array('elt',$endTime)
                )
            );
            $modRet = $clubLog->getClubLogCountNumberByGameId($countWhere);
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $ret['code'] = $modRet['code'];
                $ret['msg'] = $modRet['msg'];
                return $ret;
            }
            $agentDiamondNormal = abs((int)$modRet['data']);

            // 删除代理数专属钻石
            $countWhere = array(
                'gameId' => $gameId,
                'source' => 7,
                'changeNum' => array('lt', 0),
                'createTime' => array(
                    array('egt',$startTime),
                    array('elt',$endTime)
                )
            );
            $modRet = $clubLog->getClubLogCountNumberByGameId($countWhere);
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $ret['code'] = $modRet['code'];
                $ret['msg'] = $modRet['msg'];
                return $ret;
            }
            $agentDiamondSpecial = abs((int)$modRet['data']);

            $stat['agent_diamond'] = $agentDiamondNormal + $agentDiamondSpecial;
            $diamondCount += (int)$stat['agent_diamond'] ; //钻石消耗总数计算进行累加；
            //===================== 管理员扣除代理商钻石（俱乐部扣除）统计 end =========================

            //===================== 俱乐部开房房卡消耗统计 start =========================
            //统计俱乐部开房的钻石消耗统计
            $clubCardWhere = array(
                'gameId' => $gameId ,
                'gameUserNum' => 4 ,
                'createTime' => array(
                    'between' , array( $startTime , $endTime )
                )
            );

            $clubRoom4 =  $clubRoomCard ->queryClubRoomCardNumberByWhere($clubCardWhere); //查询4消耗人钻石
            $stat['club_four_count'] = (int)$clubRoom4['data'] ;
            $stat['club_diamond_count'] += $stat['club_four_count'] ;
            $diamondCount += $stat['club_four_count'] ; //总数累加

            $clubCardWhere['gameUserNum'] = 3 ;
            $clubRoom3 = $clubRoomCard ->queryClubRoomCardNumberByWhere($clubCardWhere);//查询3人消耗钻石
            $stat['club_three_count'] = (int) $clubRoom3['data'] ;
            $stat['club_diamond_count'] += $stat['club_three_count'] ;
            $diamondCount += $stat['club_three_count'] ; //总数累加

            $clubCardWhere['gameUserNum'] = 2 ;
            $clubRoom2 = $clubRoomCard ->queryClubRoomCardNumberByWhere($clubCardWhere); //查询2消耗人钻石
            $stat['club_two_count'] = (int) $clubRoom2['data'];
            $stat['club_diamond_count'] += $stat['club_two_count'] ;
            $diamondCount += $stat['club_two_count'] ; //总数累加
            //===================== 俱乐部开房房卡消耗统计 end =========================

            //更新当天的数据。
            $result = $statModel -> updateStatData( $nowData['id'] ,$stat);


            $ret['msg'] = ' statGameDiamondLogic - '.$gameId." Success 定时任务统计完成 ：".$statModel->getLastSql();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[Fail]  statGameDiamondLogic - {$gameId} 钻石消耗统计失败： ".$e->getMessage());
            $ret['code'] = ERRCODE_SYSTEM;
            $ret['msg'] = ' statGameDiamondLogic - '.$gameId."钻石消耗统计失败：". __FILE__."<br />".__LINE__."<br />".$e->getMessage();
            return $ret;
        };

        return $ret;
    }
}
