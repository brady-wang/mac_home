<?php
namespace Home\Model;

use Think\Model;

class SysCronlogModel extends Model
{
    // 定时器类型
    const CRON_TYPE_STAT_CLUB_PROMOTER       = 9000;  // 数据统计：亲友圈概况
    const CRON_TYPE_STAT_GAME_ROUND          = 10000; // 数据统计：对局统计
    const CRON_TYPE_STAT_GAME_ROOM           = 10010; // 数据统计：玩法统计
    const CRON_TYPE_SYNC_GAMEDIAMOND         = 10020; // 数据统计：钻石消耗
    const CRON_TYPE_SYNC_GAMEMAKEDIAMOND     = 10030; // 数据统计：钻石产出
    const CRON_TYPE_TOOL_CLEAR_SYS_REDUNANCE = 20001; // 清除系统冗余数据
    const CRON_TYPE_STAT_USER_GAME_DAILY     = 20000; // 数据统计：每日简报
    const CRON_TYPE_STAT_USER_GAME_TOTAL     = 20100; // 数据统计：用户累计
    const CRON_TYPE_STAT_USER_GAME_REMAIN    = 20200; // 数据统计：用户留存
    const CRON_TYPE_STAT_USER_GAME_BEHAVE    = 20300; // 数据统计：用户行为
    const CRON_TYPE_STAT_USER_GAME_SHARE     = 20305; // 数据统计：用户分享
    const CRON_TYPE_STAT_USER_GAME_REGEISTER = 20400; // 数据统计：注册来源
    const CRON_TYPE_STAT_USER_GAME_RANK      = 20500; // 数据统计：用户排行
    const CRON_TYPE_SYNC_GAMEONLINE          = 20600; // 数据统计：实时在线人数
    const CRON_TYPE_SYNC_GAME_MAIL_TIMER     = 20700; // 定时邮件
    const CRON_TYPE_SYNC_CRON_TIME           = 20800; // 新年拉新
    const CRON_TYPE_SYNC_USER_GAME_CHANNEL   = 20900; // 数据统计：渠道统计
    public $cronTypeMap = array(
        self::CRON_TYPE_TOOL_CLEAR_SYS_REDUNANCE => '清除系统冗余数据',
        self::CRON_TYPE_SYNC_GAME_MAIL_TIMER => '定时邮件',
        self::CRON_TYPE_SYNC_CRON_TIME => '新年拉新',
        self::CRON_TYPE_STAT_CLUB_PROMOTER => '数据统计：亲友圈概况',
        self::CRON_TYPE_SYNC_GAMEONLINE => '数据统计：实时在线人数',
        self::CRON_TYPE_STAT_USER_GAME_DAILY => '数据统计：每日简报',
        self::CRON_TYPE_STAT_USER_GAME_TOTAL => '数据统计：用户累计',
        self::CRON_TYPE_STAT_USER_GAME_REMAIN => '数据统计：用户留存',
        self::CRON_TYPE_STAT_USER_GAME_BEHAVE => '数据统计：用户行为',
        self::CRON_TYPE_STAT_USER_GAME_SHARE => '数据统计：用户分享',
        self::CRON_TYPE_STAT_USER_GAME_REGEISTER => '数据统计：注册来源',
        self::CRON_TYPE_STAT_USER_GAME_RANK => '数据统计：用户排行',
        self::CRON_TYPE_SYNC_USER_GAME_CHANNEL => '数据统计：渠道统计',
        self::CRON_TYPE_STAT_GAME_ROUND => '数据统计：对局统计',
        self::CRON_TYPE_STAT_GAME_ROOM => '数据统计：玩法统计',
        self::CRON_TYPE_SYNC_GAMEDIAMOND => '数据统计：钻石消耗',
        self::CRON_TYPE_SYNC_GAMEMAKEDIAMOND => '数据统计：钻石产出',
    );

    // 执行结果
    const RET_CODE_SUCCESS = 10; // 成功
    const RET_CODE_WARNING = 90; // 异常
    const RET_CODE_FAIL = 99; // 失败
    public $retCodeMap = array(
        self::RET_CODE_SUCCESS => array('name' => '成功', 'label' => 'label-success'),
        self::RET_CODE_WARNING => array('name' => '异常', 'label' => 'label-warning'),
        self::RET_CODE_FAIL => array('name' => '失败', 'label' => 'label-danger'),
    );

    /**
     * 获取定时器流水列表
     * @author Carter
     */
    public function querySysCronlogList($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array();
        if ($attr['handle_type']) {
            $where['cron_type'] = $attr['handle_type'];
        }
        if ($attr['handle_status']) {
            $where['ret_code'] = $attr['handle_status'];
        }
        if ($attr['start_date']) {
            // 确保时间是从指定日期的0点0分0秒开始
            $startStamp = strtotime(date('Y-m-d', strtotime($attr['start_date'])));
            $where['start_time'][] = array('egt', $startStamp);
        }
        if ($attr['end_date']) {
            // 确保时间是从指定日期的23点59分59秒结束
            $endStamp = strtotime(date('Y-m-d 23:59:59', strtotime($attr['end_date'])));
            $where['start_time'][] = array('elt', $endStamp);
        }

        try {
            // 分页获取
            $pageSize = C('PAGE_SIZE');
            $count = $this->where($where)->count();
            $paginate = new \Think\Page($count, $pageSize);
            $pagination = $paginate->show();
            $page = $paginate->getCurPage();

            $field = 'id,cron_type,start_time,end_time,ret_code,ret_data';
            $list = $this->field($field)->where($where)->order('id DESC')->page("{$page},{$pageSize}")->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[querySysCronlogList] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        foreach ($list as $k => $v) {
            // 定时器执行时长
            $dur = $v['end_time'] - $v['start_time'];
            // 数据错误
            if ($dur < 0) {
                $list[$k]['duration'] = 'error data';
            }
            $list[$k]['duration'] = format_second_time($dur);
        }

        $ret['data']['list'] = $list;
        $ret['data']['pagination'] = $pagination;
        return $ret;
    }

    /**
     * 插入定时器流水
     * @author Carter
     */
    public function insertSysCronlog($cronType, $startTime, $endTime, $retCode, $retData)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $insertData = array(
            'cron_type' => $cronType,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'ret_code' => $retCode,
            'ret_data' => $retData,
        );

        try {
            $this->add($insertData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertSysCronlog] ".$e->getMessage());
            $this->ret['code'] = ERRCODE_DB_ADD_ERR;
            $this->ret['msg'] = $e->getMessage();
            return $ret;
        }

        return $ret;
    }
}
