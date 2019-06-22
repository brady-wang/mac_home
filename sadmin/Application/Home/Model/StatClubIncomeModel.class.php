<?php
namespace Home\Model;

use Think\Model;

class StatClubIncomeModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据参数获取收入趋势列表
     * @author Carter
     */
    public function queryClubIncomeListForDown($gameId, $attr, $field)
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
                set_exception(__FILE__, __LINE__, "[queryClubIncomeListForDown] select failed: ".$e->getMessage());
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
            set_exception(__FILE__, __LINE__, "[queryClubIncomeListForDown] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $list;

        return $ret;
    }

    /**
     * 获取收入趋势页面数据
     * @author Carter
     */
    public function queryClubIncomeDetail($gameId, $attr)
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
            set_exception(__FILE__, __LINE__, "[queryClubIncomeDetail] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $dateTime = $info['stat_time'];
        $ret['data']['statDate'] = $info['stat_date'];

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
            $field = 'income_amount,pay_num,pay_type_one_num,pay_type_one_amount,pay_type_two_num,pay_type_two_amount,';
            $field .= 'pay_type_three_num,pay_type_three_amount,pay_type_four_num,pay_type_four_amount,stat_time,stat_date';
            $list = $this->field($field)->where($where)->order('stat_time ASC')->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryClubIncomeDetail] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $compData = array(
            // 图表数据
            'chart' => array(
                // 日期
                'category' => array(),
                // 付费金额
                'incomeAmount' => array(),
                // 付费人数
                'payNum' => array(),
                // 新增付费额度 - 当日付费总额
                'payTypeOneAmount' => array(),
                // 新增付费额度 - 1-3日付费总额
                'payTypeTwoAmount' => array(),
                // 新增付费额度 - 4-7日付费总额
                'payTypeThreeAmount' => array(),
                // 新增付费额度 - 7日以上付费总额
                'payTypeFourAmount' => array(),
                // 新增付费人数 - 当日付费人数
                'payTypeOneNum' => array(),
                // 新增付费人数 - 1-3日付费人数
                'payTypeTwoNum' => array(),
                // 新增付费人数 - 4-7日付费人数
                'payTypeThreeNum' => array(),
                // 新增付费人数 - 7日以上付费人数
                'payTypeFourNum' => array(),
            ),
            // 表格数据
            'table' => array(),
        );
        foreach ($list as $v) {
            // 图表数据
            $compData['chart']['category'][] = $v['stat_date'];
            // 付费金额
            $compData['chart']['incomeAmount'][] = round($v['income_amount'] / 100);
            // 付费人数
            $compData['chart']['payNum'][] = $v['pay_num'];
            // 新增付费额度 - 当日付费总额
            $compData['chart']['payTypeOneAmount'][] = round($v['pay_type_one_amount'] / 100);
            // 新增付费额度 - 1-3日付费总额
            $compData['chart']['payTypeTwoAmount'][] = round($v['pay_type_two_amount'] / 100);
            // 新增付费额度 - 4-7日付费总额
            $compData['chart']['payTypeThreeAmount'][] = round($v['pay_type_three_amount'] / 100);
            // 新增付费额度 - 7日以上付费总额
            $compData['chart']['payTypeFourAmount'][] = round($v['pay_type_four_amount'] / 100);
            // 新增付费人数 - 当日付费人数
            $compData['chart']['payTypeOneNum'][] = $v['pay_type_one_num'];
            // 新增付费人数 - 1-3日付费人数
            $compData['chart']['payTypeTwoNum'][] = $v['pay_type_two_num'];
            // 新增付费人数 - 4-7日付费人数
            $compData['chart']['payTypeThreeNum'][] = $v['pay_type_three_num'];
            // 新增付费人数 - 7日以上付费人数
            $compData['chart']['payTypeFourNum'][] = $v['pay_type_four_num'];
            // 表格数据
            $compData['table'][] = $v;
        }

        // 对表格数据进行日期倒序
        usort($compData['table'], function($a, $b) {
            return $a['stat_time'] > $b['stat_time'] ? -1 : 1;
        });

        $ret['data']['compData'] = $compData;

        return $ret;
    }
}
