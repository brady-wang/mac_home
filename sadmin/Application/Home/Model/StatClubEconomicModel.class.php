<?php
namespace Home\Model;

use Think\Model;

class StatClubEconomicModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据参数获取经济分析列表
     * @author Carter
     */
    public function queryClubEconomicListForDown($gameId, $attr, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $where = array('game_id' => $gameId);
        // 查看近14天
        if (1 == $attr['query_type']) {
            // 获取最新一条记录的统计时间
            try {
                $info = $this->field('stat_time')->where(array('game_id' => $gameId))->order('stat_time DESC')->find();
            } catch(\Exception $e) {
                set_exception(__FILE__, __LINE__, "[queryClubEconomicListForDown] select failed: ".$e->getMessage());
                $ret['code'] = ERRCODE_DB_SELECT_ERR;
                $ret['msg'] = $e->getMessage();
                return $ret;
            }
            $dateTime = $info['stat_time'];

            $where['stat_time'] = array('between', array(strtotime('-13 day', $dateTime), $dateTime));
        }
        // 按时间区间查
        else if (2 == $attr['query_type']) {
            $where['stat_time'] = array('between', array(strtotime($attr['start_date']), strtotime($attr['end_date'])));
        }

        try {
            $list = $this->field($field)->where($where)->order('stat_time DESC')->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryClubEconomicListForDown] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $list;

        return $ret;
    }

    /**
     * 获取经济分析页面数据
     * @author Carter
     */
    public function queryClubEconomicDetail($gameId, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        // 获取最新一条记录的统计时间
        try {
            $field = 'stat_time,stat_date';
            $where = array(
                'game_id' => $gameId,
            );
            $info = $this->field($field)->where($where)->order('stat_time DESC')->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryClubEconomicDetail] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $dateTime = $info['stat_time'];
        $ret['data']['statDate'] = $info['stat_date'];

        /******************** 总览数据 ********************/

        // 计算总览累计钻石产出、钻石总结余、活跃代理结余、玩家结余及其环比数据
        $dCutTime = strtotime('-1 day', $dateTime);
        $dEndTime = strtotime('-2 day', $dateTime);
        $wCutTime = strtotime('-7 day', $dateTime);
        $wEndTime = strtotime('-14 day', $dateTime);
        $mCutTime = strtotime('-30 day', $dateTime);
        $mEndTime = strtotime('-60 day', $dateTime);
        $timeArr = array(
            $dateTime,
            $dCutTime,
            $dEndTime,
            $wCutTime,
            $wEndTime,
            $mCutTime,
            $mEndTime,
        );
        try {
            $field = 'diamond_produce_amount,diamond_remain_amount,diamond_remain_active_agent,diamond_remain_game,stat_time';
            $where = array(
                'game_id' => $gameId,
                'stat_time' => array('in', $timeArr),
            );
            $list = $this->field($field)->where($where)->order('stat_time DESC')->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryClubEconomicDetail] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ringMap = array_combine(array_column($list, 'stat_time'), $list);

        if (empty($ringMap[$dateTime])) {
            $ret['code'] = ERRCODE_DB_DATA_EMPTY;
            $ret['msg'] = "当日数据不存在，请联系管理员检查统计脚本";
            return $ret;
        }

        // 累计钻石产出
        $ret['data']['gnlProduce'] = $ringMap[$dateTime]['diamond_produce_amount'];
        // 钻石总结余
        $ret['data']['gnlRemain'] = $ringMap[$dateTime]['diamond_remain_amount'];
        // 活跃代理结余
        $ret['data']['gnlAgRemain'] = $ringMap[$dateTime]['diamond_remain_active_agent'];
        // 玩家结余
        $ret['data']['gnlGmRemain'] = $ringMap[$dateTime]['diamond_remain_game'];

        // 累计钻石产出环比数据，日
        $prdcDElm = $ringMap[$dateTime]['diamond_produce_amount'] - $ringMap[$dCutTime]['diamond_produce_amount'];
        $prdcDBase = $ringMap[$dCutTime]['diamond_produce_amount'] - $ringMap[$dEndTime]['diamond_produce_amount'];
        if (0 == $prdcDBase) {
            $ret['data']['ringDProduce'] = '-';
        } else {
            $ret['data']['ringDProduce'] = round(($prdcDElm - $prdcDBase) / $prdcDBase * 100, 1)."%";
        }
        // 周
        $prdcWElm = $ringMap[$dateTime]['diamond_produce_amount'] - $ringMap[$wCutTime]['diamond_produce_amount'];
        $prdcWBase = $ringMap[$wCutTime]['diamond_produce_amount'] - $ringMap[$wEndTime]['diamond_produce_amount'];
        if (0 == $prdcWBase) {
            $ret['data']['ringWProduce'] = '-';
        } else {
            $ret['data']['ringWProduce'] = round(($prdcWElm - $prdcWBase) / $prdcWBase * 100, 1)."%";
        }
        // 月
        $prdcMElm = $ringMap[$dateTime]['diamond_produce_amount'] - $ringMap[$mCutTime]['diamond_produce_amount'];
        $prdcMBase = $ringMap[$mCutTime]['diamond_produce_amount'] - $ringMap[$mEndTime]['diamond_produce_amount'];
        if (0 == $prdcMBase) {
            $ret['data']['ringMProduce'] = '-';
        } else {
            $ret['data']['ringMProduce'] = round(($prdcMElm - $prdcMBase) / $prdcMBase * 100, 1)."%";
        }

        // 钻石总结余环比数据，日
        $remnDElm = $ringMap[$dateTime]['diamond_remain_amount'] - $ringMap[$dCutTime]['diamond_remain_amount'];
        $remnDBase = $ringMap[$dCutTime]['diamond_remain_amount'] - $ringMap[$dEndTime]['diamond_remain_amount'];
        if (0 == $remnDBase) {
            $ret['data']['ringDRemain'] = '-';
        } else {
            $ret['data']['ringDRemain'] = round(($remnDElm - $remnDBase) / $remnDBase * 100, 1)."%";
        }
        // 周
        $remnWElm = $ringMap[$dateTime]['diamond_remain_amount'] - $ringMap[$wCutTime]['diamond_remain_amount'];
        $remnWBase = $ringMap[$wCutTime]['diamond_remain_amount'] - $ringMap[$wEndTime]['diamond_remain_amount'];
        if (0 == $remnWBase) {
            $ret['data']['ringWRemain'] = '-';
        } else {
            $ret['data']['ringWRemain'] = round(($remnWElm - $remnWBase) / $remnWBase * 100, 1)."%";
        }
        // 月
        $remnMElm = $ringMap[$dateTime]['diamond_remain_amount'] - $ringMap[$mCutTime]['diamond_remain_amount'];
        $remnMBase = $ringMap[$mCutTime]['diamond_remain_amount'] - $ringMap[$mEndTime]['diamond_remain_amount'];
        if (0 == $remnMBase) {
            $ret['data']['ringMRemain'] = '-';
        } else {
            $ret['data']['ringMRemain'] = round(($remnMElm - $remnMBase) / $remnMBase * 100, 1)."%";
        }

        // 活跃代理结余环比数据，日
        $agacDElm = $ringMap[$dateTime]['diamond_remain_active_agent'] - $ringMap[$dCutTime]['diamond_remain_active_agent'];
        $agacDBase = $ringMap[$dCutTime]['diamond_remain_active_agent'] - $ringMap[$dEndTime]['diamond_remain_active_agent'];
        if (0 == $agacDBase) {
            $ret['data']['ringDAgRemain'] = '-';
        } else {
            $ret['data']['ringDAgRemain'] = round(($agacDElm - $agacDBase) / $agacDBase * 100, 1)."%";
        }
        // 周
        $agacWElm = $ringMap[$dateTime]['diamond_remain_active_agent'] - $ringMap[$wCutTime]['diamond_remain_active_agent'];
        $agacWBase = $ringMap[$wCutTime]['diamond_remain_active_agent'] - $ringMap[$wEndTime]['diamond_remain_active_agent'];
        if (0 == $agacWBase) {
            $ret['data']['ringWAgRemain'] = '-';
        } else {
            $ret['data']['ringWAgRemain'] = round(($agacWElm - $agacWBase) / $agacWBase * 100, 1)."%";
        }
        // 月
        $agacMElm = $ringMap[$dateTime]['diamond_remain_active_agent'] - $ringMap[$mCutTime]['diamond_remain_active_agent'];
        $agacMBase = $ringMap[$mCutTime]['diamond_remain_active_agent'] - $ringMap[$mEndTime]['diamond_remain_active_agent'];
        if (0 == $agacMBase) {
            $ret['data']['ringMAgRemain'] = '-';
        } else {
            $ret['data']['ringMAgRemain'] = round(($agacMElm - $agacMBase) / $agacMBase * 100, 1)."%";
        }

        // 用户结余环比数据，日
        $gmacDElm = $ringMap[$dateTime]['diamond_remain_game'] - $ringMap[$dCutTime]['diamond_remain_game'];
        $gmacDBase = $ringMap[$dCutTime]['diamond_remain_game'] - $ringMap[$dEndTime]['diamond_remain_game'];
        if (0 == $gmacDBase) {
            $ret['data']['ringDGmRemain'] = '-';
        } else {
            $ret['data']['ringDGmRemain'] = round(($gmacDElm - $gmacDBase) / $gmacDBase * 100, 1)."%";
        }
        // 周
        $gmacWElm = $ringMap[$dateTime]['diamond_remain_game'] - $ringMap[$wCutTime]['diamond_remain_game'];
        $gmacWBase = $ringMap[$wCutTime]['diamond_remain_game'] - $ringMap[$wEndTime]['diamond_remain_game'];
        if (0 == $gmacWBase) {
            $ret['data']['ringWGmRemain'] = '-';
        } else {
            $ret['data']['ringWGmRemain'] = round(($gmacWElm - $gmacWBase) / $gmacWBase * 100, 1)."%";
        }
        // 月
        $gmacMElm = $ringMap[$dateTime]['diamond_remain_game'] - $ringMap[$mCutTime]['diamond_remain_game'];
        $gmacMBase = $ringMap[$mCutTime]['diamond_remain_game'] - $ringMap[$mEndTime]['diamond_remain_game'];
        if (0 == $gmacMBase) {
            $ret['data']['ringMGmRemain'] = '-';
        } else {
            $ret['data']['ringMGmRemain'] = round(($gmacMElm - $gmacMBase) / $gmacMBase * 100, 1)."%";
        }

        /******************** 复合数据 ********************/

        $where = array(
            'game_id' => $gameId,
        );
        // 查看近14天
        if (1 == $attr['query_type']) {
            $where['stat_time'] = array('between', array(strtotime('-13 day', $dateTime), $dateTime));
        }
        // 按时间区间查
        else if (2 == $attr['query_type']) {
            $where['stat_time'] = array('between', array(strtotime($attr['start_date']), strtotime($attr['end_date'])));
        }

        try {
            $field = 'diamond_remain_active_agent,diamond_produce,diamond_consume,diamond_remain_game,';
            $field .= 'diamond_produce_active_agent,diamond_produce_active_game,abnormal_get,abnormal_transfer,stat_date';
            $list = $this->field($field)->where($where)->order('stat_time ASC')->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryClubEconomicDetail] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $compData = array(
            // 图表数据
            'chart' => array(
                // 日期
                'category' => array(),
                // 当日钻石产出
                'diamondProduce' => array(),
                // 当日钻石消耗
                'diamondConsume' => array(),
                // 活跃代理钻石结余
                'agentRemain' => array(),
                // 活跃代理发放
                'agentProduce' => array(),
                // 活跃用户发放
                'gameProduce' => array(),
                // 用户结余
                'gameRemain' => array(),
                // 一次性发放超过300
                'abnormalGet' => array(),
                // 亲友圈转移钻石代理数
                'abnormalTransfer' => array(),
            ),
            // 表格数据
            'table' => array(),
        );
        foreach ($list as $v) {
            // 图表数据
            $compData['chart']['category'][] = $v['stat_date'];
            // 当日钻石产出
            $compData['chart']['diamondProduce'][] = $v['diamond_produce'];
            // 当日钻石消耗
            $compData['chart']['diamondConsume'][] = $v['diamond_consume'];
            // 活跃代理钻石结余
            $compData['chart']['agentRemain'][] = $v['diamond_remain_active_agent'];
            // 活跃代理发放
            $compData['chart']['agentProduce'][] = $v['diamond_produce_active_agent'];
            // 活跃用户发放
            $compData['chart']['gameProduce'][] = $v['diamond_produce_active_game'];
            // 用户结余
            $compData['chart']['gameRemain'][] = $v['diamond_remain_game'];
            // 一次性发放超过300
            $compData['chart']['abnormalGet'][] = $v['abnormal_get'];
            // 亲友圈转移钻石代理数
            $compData['chart']['abnormalTransfer'][] = $v['abnormal_transfer'];
            // 表格数据，新增代理
            $compData['table'][] = $v;
        }
        $ret['data']['compData'] = $compData;

        return $ret;
    }
}
