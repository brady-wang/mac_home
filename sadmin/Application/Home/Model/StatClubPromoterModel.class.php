<?php
namespace Home\Model;

use Think\Model;

class StatClubPromoterModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据参数获取代理概况列表
     * @author Carter
     */
    public function queryClubPromoterListForDown($gameId, $attr, $field)
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
                set_exception(__FILE__, __LINE__, "[queryClubPromoterListForDown] select failed: ".$e->getMessage());
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
            set_exception(__FILE__, __LINE__, "[queryClubPromoterListForDown] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $list;

        return $ret;
    }

    /**
     * 获取代理概况页面数据
     * @author Carter
     */
    public function queryClubPromoterDetail($gameId, $attr)
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
            set_exception(__FILE__, __LINE__, "[queryClubPromoterDetail] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $dateTime = $info['stat_time'];
        $ret['data']['statDate'] = $info['stat_date'];

        /******************** 总览数据 ********************/

        // 计算总览代理、转正、充值及其环比数据
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
            $field = 'promoter_amount,transfer_amount,active_count,recharge_amount,transfer_count,effective_recharge,stat_time';
            $where = array(
                'game_id' => $gameId,
                'stat_time' => array('in', $timeArr),
            );
            $list = $this->field($field)->where($where)->order('stat_time DESC')->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryClubPromoterDetail] select failed: ".$e->getMessage());
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

        // 累计代理数
        $ret['data']['gnlPromoter'] = $ringMap[$dateTime]['promoter_amount'];
        // 累计转正代理数
        $ret['data']['gnlTransfer'] = $ringMap[$dateTime]['transfer_amount'];
        // 日活跃代理数
        $ret['data']['gnlActive'] = $ringMap[$dateTime]['active_count'];
        // 累计代理充值总额
        $ret['data']['gnlRecharge'] = round($ringMap[$dateTime]['recharge_amount'] / 100);

        // 累计代理数环比数据，日
        $prmtDElm = $ringMap[$dateTime]['promoter_amount'] - $ringMap[$dCutTime]['promoter_amount'];
        $prmtDBase = $ringMap[$dCutTime]['promoter_amount'] - $ringMap[$dEndTime]['promoter_amount'];
        if (0 == $prmtDBase) {
            $ret['data']['ringDPromoter'] = '-';
        } else {
            $ret['data']['ringDPromoter'] = round(($prmtDElm - $prmtDBase) / $prmtDBase * 100, 1)."%";
        }
        // 周
        $prmtWElm = $ringMap[$dateTime]['promoter_amount'] - $ringMap[$wCutTime]['promoter_amount'];
        $prmtWBase = $ringMap[$wCutTime]['promoter_amount'] - $ringMap[$wEndTime]['promoter_amount'];
        if (0 == $prmtWBase) {
            $ret['data']['ringWPromoter'] = '-';
        } else {
            $ret['data']['ringWPromoter'] = round(($prmtWElm - $prmtWBase) / $prmtWBase * 100, 1)."%";
        }
        // 月
        $prmtMElm = $ringMap[$dateTime]['promoter_amount'] - $ringMap[$mCutTime]['promoter_amount'];
        $prmtMBase = $ringMap[$mCutTime]['promoter_amount'] - $ringMap[$mEndTime]['promoter_amount'];
        if (0 == $prmtMBase) {
            $ret['data']['ringMPromoter'] = '-';
        } else {
            $ret['data']['ringMPromoter'] = round(($prmtMElm - $prmtMBase) / $prmtMBase * 100, 1)."%";
        }

        // 累计转正代理数环比数据，日
        $trnfDElm = $ringMap[$dateTime]['transfer_count'] - $ringMap[$dCutTime]['transfer_count'];
        $trnfDBase = $ringMap[$dCutTime]['transfer_count'] - $ringMap[$dEndTime]['transfer_count'];
        if (0 == $trnfDBase) {
            $ret['data']['ringDTransfer'] = '-';
        } else {
            $ret['data']['ringDTransfer'] = round(($trnfDElm - $trnfDBase) / $trnfDBase * 100, 1)."%";
        }
        // 周
        $trnfWElm = $ringMap[$dateTime]['transfer_count'] - $ringMap[$wCutTime]['transfer_count'];
        $trnfWBase = $ringMap[$wCutTime]['transfer_count'] - $ringMap[$wEndTime]['transfer_count'];
        if (0 == $trnfWBase) {
            $ret['data']['ringWTransfer'] = '-';
        } else {
            $ret['data']['ringWTransfer'] = round(($trnfWElm - $trnfWBase) / $trnfWBase * 100, 1)."%";
        }
        // 月
        $trnfMElm = $ringMap[$dateTime]['transfer_count'] - $ringMap[$mCutTime]['transfer_count'];
        $trnfMBase = $ringMap[$mCutTime]['transfer_count'] - $ringMap[$mEndTime]['transfer_count'];
        if (0 == $trnfMBase) {
            $ret['data']['ringMTransfer'] = '-';
        } else {
            $ret['data']['ringMTransfer'] = round(($trnfMElm - $trnfMBase) / $trnfMBase * 100, 1)."%";
        }

        // 累计代理充值总额环比数据，日
        $rchrDElm = $ringMap[$dateTime]['effective_recharge'] - $ringMap[$dCutTime]['effective_recharge'];
        $rchrDBase = $ringMap[$dCutTime]['effective_recharge'] - $ringMap[$dEndTime]['effective_recharge'];
        if (0 == $rchrDBase) {
            $ret['data']['ringDRecharge'] = '-';
        } else {
            $ret['data']['ringDRecharge'] = round(($rchrDElm - $rchrDBase) / $rchrDBase * 100, 1)."%";
        }
        // 周
        $rchrWElm = $ringMap[$dateTime]['effective_recharge'] - $ringMap[$wCutTime]['effective_recharge'];
        $rchrWBase = $ringMap[$wCutTime]['effective_recharge'] - $ringMap[$wEndTime]['effective_recharge'];
        if (0 == $rchrWBase) {
            $ret['data']['ringWRecharge'] = '-';
        } else {
            $ret['data']['ringWRecharge'] = round(($rchrWElm - $rchrWBase) / $rchrWBase * 100, 1)."%";
        }
        // 月
        $rchrMElm = $ringMap[$dateTime]['effective_recharge'] - $ringMap[$mCutTime]['effective_recharge'];
        $rchrMBase = $ringMap[$mCutTime]['effective_recharge'] - $ringMap[$mEndTime]['effective_recharge'];
        if (0 == $rchrMBase) {
            $ret['data']['ringMRecharge'] = '-';
        } else {
            $ret['data']['ringMRecharge'] = round(($rchrMElm - $rchrMBase) / $rchrMBase * 100, 1)."%";
        }

        // 活跃代理数的环比数据
        $cutTime = strtotime('-60 day', $dateTime);
        try {
            $field = 'active_count,stat_time';
            $where = array(
                'game_id' => $gameId,
                'stat_time' => array(
                    array('egt', $cutTime),
                    array('elt', $dateTime),
                ),
            );
            $list = $this->field($field)->where($where)->order('stat_time DESC')->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryClubPromoterDetail] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        // 日环比
        $ringDCutFlag = strtotime('-1 day', $dateTime);
        $ringDEndFlag = strtotime('-2 day', $dateTime);
        $efftDElm = 0;
        $efftDBase = 0;
        // 周环比
        $ringWCutFlag = strtotime('-7 day', $dateTime);
        $ringWEndFlag = strtotime('-14 day', $dateTime);
        $efftWElm = 0;
        $efftWBase = 0;
        // 月环比
        $ringMCutFlag = strtotime('-30 day', $dateTime);
        $ringMEndFlag = strtotime('-60 day', $dateTime);
        $efftMElm = 0;
        $efftMBase = 0;
        foreach ($list as $v) {
            // 日
            if ($v['stat_time'] <= $dateTime && $v['stat_time'] > $ringDCutFlag) {
                $efftDElm += $v['active_count'];
            } else if ($v['stat_time'] <= $ringDCutFlag && $v['stat_time'] > $ringDEndFlag) {
                $efftDBase += $v['active_count'];
            }
            // 周
            if ($v['stat_time'] <= $dateTime && $v['stat_time'] > $ringWCutFlag) {
                $efftWElm += $v['active_count'];
            } else if ($v['stat_time'] <= $ringWCutFlag && $v['stat_time'] > $ringWEndFlag) {
                $efftWBase += $v['active_count'];
            }
            // 月
            if ($v['stat_time'] <= $dateTime && $v['stat_time'] > $ringMCutFlag) {
                $efftMElm += $v['active_count'];
            } else if ($v['stat_time'] <= $ringMCutFlag && $v['stat_time'] > $ringMEndFlag) {
                $efftMBase += $v['active_count'];
            }
        }
        // 日
        if (0 == $efftDBase) {
            $ret['data']['ringDEffective'] = '-';
        } else {
            $ret['data']['ringDEffective'] = round(($efftDElm - $efftDBase) / $efftDBase * 100, 1)."%";
        }
        // 周
        if (0 == $efftWBase) {
            $ret['data']['ringWEffective'] = '-';
        } else {
            $ret['data']['ringWEffective'] = round(($efftWElm - $efftWBase) / $efftWBase * 100, 1)."%";
        }
        // 月
        if (0 == $efftMBase) {
            $ret['data']['ringMEffective'] = '-';
        } else {
            $ret['data']['ringMEffective'] = round(($efftMElm - $efftMBase) / $efftMBase * 100, 1)."%";
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
            $field = 'promoter_count,transfer_count,effective_transfer,effective_active,effective_recharge,';
            $field .= 'club_active,club_recharge,retail_active,retail_recharge,stat_date';
            $list = $this->field($field)->where($where)->order('stat_time ASC')->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryClubPromoterDetail] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $compData = array(
            // 图表数据
            'chart' => array(
                // 日期
                'category' => array(),
                // 新开通代理
                'promoterCount' => array(),
                // 新转正代理
                'transferCount' => array(),
                // 有效代理 - 累计转正
                'effectiveTransfer' => array(),
                // 有效代理 - 当日活跃
                'effectiveActive' => array(),
                // 有效代理 - 当日充值总额
                'effectiveRecharge' => array(),
                // 亲友圈代理 - 累计转正
                'clubTransfer' => array(),
                // 亲友圈代理 - 当日活跃
                'clubActive' => array(),
                // 亲友圈代理 - 当日充值总额
                'clubRecharge' => array(),
                // 散户代理 - 累计转正
                'retailTransfer' => array(),
                // 散户代理 - 当日活跃
                'retailActive' => array(),
                // 散户代理 - 当日充值总额
                'retailRecharge' => array(),
            ),
            // 表格数据
            'table' => array(),
        );
        foreach ($list as $v) {
            // 图表数据
            $compData['chart']['category'][] = $v['stat_date'];
            // 新开通代理
            $compData['chart']['promoterCount'][] = $v['promoter_count'];
            // 新转正代理
            $compData['chart']['transferCount'][] = $v['transfer_count'];
            // 有效代理 - 累计转正
            $compData['chart']['effectiveTransfer'][] = $v['effective_transfer'];
            // 有效代理 - 当日活跃
            $compData['chart']['effectiveActive'][] = $v['effective_active'];
            // 有效代理 - 当日充值总额
            $compData['chart']['effectiveRecharge'][] = round($v['effective_recharge'] / 100);
            // 亲友圈代理 - 累计转正
            $compData['chart']['clubTransfer'][] = $v['effective_transfer'];
            // 亲友圈代理 - 当日活跃
            $compData['chart']['clubActive'][] = $v['club_active'];
            // 亲友圈代理 - 当日充值总额
            $compData['chart']['clubRecharge'][] = round($v['club_recharge'] / 100);
            // 散户圈代理 - 累计转正
            $compData['chart']['retailTransfer'][] = $v['effective_transfer'];
            // 散户圈代理 - 当日活跃
            $compData['chart']['retailActive'][] = $v['retail_active'];
            // 散户代理 - 当日充值总额
            $compData['chart']['retailRecharge'][] = round($v['retail_recharge'] / 100);
            // 表格数据，新增代理
            $compData['table'][] = $v;
        }
        $ret['data']['compData'] = $compData;

        return $ret;
    }

    /**
     * 获取指定游戏代理概况统计表最后一条记录
     * @author Carter
     */
    public function queryStatPromoterLastRow($gameId, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $where = array('game_id' => $gameId);

        try {
            $row = $this->field($field)->where($where)->order('stat_time DESC')->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryStatPromoterLastRow] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $row;

        return $ret;
    }

    /**
     * 插入游戏代理概况统计数据
     * @author Carter
     */
    public function insertStatClubPromoter($attr, $gameId, $statDate)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $insertData = array(
            'game_id' => $gameId,
            'promoter_amount' => $attr['promoterAmount'],
            'transfer_amount' => $attr['transferAmount'],
            'active_count' => $attr['activeCount'],
            'recharge_amount' => $attr['rechargeAmount'],
            'promoter_count' => $attr['promoterCount'],
            'transfer_count' => $attr['transferCount'],
            'effective_transfer' => $attr['effectiveTransfer'],
            'effective_active' => $attr['effectiveActive'],
            'effective_recharge' => $attr['effectiveRecharge'],
            'club_active' => $attr['clubActive'],
            'club_recharge' => $attr['clubRecharge'],
            'retail_active' => $attr['retailActive'],
            'retail_recharge' => $attr['retailRecharge'],
            'stat_time' => strtotime($statDate),
            'stat_date' => $statDate,
        );
        try {
            $id = $this->add($insertData);
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertStatClubPromoter] update failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = array_merge(array('id' => $id), $insertData);

        return $ret;
    }

    /**
     * 删除指定id的游戏代理概况统计数据
     * @author Carter
     */
    public function deleteStatClubPromoterById($id)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        try {
            $this->where(array('id' => $id))->delete();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[deleteStatClubPromoterById] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_DELETE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        return $ret;
    }
}
