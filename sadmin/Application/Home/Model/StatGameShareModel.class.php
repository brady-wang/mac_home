<?php
namespace Home\Model;

use Think\Model;

class StatGameShareModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据条件获取分享统计数据列表，分页
     * @author Carter
     */
    public function queryStatGameShareListByAttr($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array();
        if ($attr['game_id']) {
            $where['game_id'] = $attr['game_id'];
        }
        if ($attr['start_date']) {
            $where['stat_time'][] = array('egt', strtotime($attr['start_date']));
        }
        if ($attr['end_date']) {
            $where['stat_time'][] = array('elt', strtotime($attr['end_date']));
        }

        try {
            // 分页获取
            $pageSize = C('PAGE_SIZE');
            $count = $this->where($where)->count();
            $paginate = new \Think\Page($count, $pageSize);
            $pagination = $paginate->show();
            $page = $paginate->getCurPage();

            $list = $this->field('*')->where($where)->order('stat_time DESC')->page("{$page},{$pageSize}")->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryStatGameShareListByAttr] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data']['list'] = $list;
        $ret['data']['pagination'] = $pagination;
        return $ret;
    }

    /**
     * 得到分享统计的图形数据
     * @author Carter
     */
    public function queryGameShareChartData($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        // 默认查询最近七天数据
        if (empty($attr['start_date']) && empty($attr['end_date'])) {
            $eTime = strtotime('midnight yesterday');
            $sTime = strtotime('-6 days', $eTime);
        }
        // 同时选择了开始与结束日期
        else if (!empty($attr['start_date']) && !empty($attr['end_date'])) {
            $sTime = strtotime($attr['start_date']);
            $eTime = strtotime($attr['end_date']);
        }
        // 只选了开始日期，那就查询从开始日期往后推七天
        else if (!empty($attr['start_date'])) {
            $sTime = strtotime($attr['start_date']);
            $eTime = strtotime('+6 days', $sTime);
        }
        // 只选了结束日期，那就查询从结束日期往前推七天
        else if (!empty($attr['end_date'])) {
            $eTime = strtotime($attr['end_date']);
            $sTime = strtotime('-6 days', $eTime);
        }

        $where = array(
            'game_id' => $attr['game_id'],
            'stat_time' => array(
                array('egt', $sTime),
                array('elt', $eTime),
            ),
        );

        try {
            $list = $this->field('*')->where($where)->order('stat_time ASC')->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGameShareChartData] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $xAxis = array_column($list, 'stat_time');
        array_walk($xAxis, function(&$item) {
            $item = date('Y-m-d', $item);
        });
        $ret['data']['xAxis'] = $xAxis;

        $chartMap = array(
            'award' => array(
                'name' => '分享领钻',
                'legend' => array('图标次数', '图标人数', '按钮次数', '按钮人数', '成功次数', '成功人数'),
                'series' => array(
                    'award_icon_count' => array('name' => '图标次数', 'stack' => 'icon', 'data' => array()),
                    'award_icon_num' => array('name' => '图标人数', 'stack' => 'icon', 'data' => array()),
                    'award_btn_count' => array('name' => '按钮次数', 'stack' => 'btn', 'data' => array()),
                    'award_btn_num' => array('name' => '按钮人数', 'stack' => 'btn', 'data' => array()),
                    'award_succ_count' => array('name' => '成功次数', 'stack' => 'succ', 'data' => array()),
                    'award_succ_num' => array('name' => '成功人数', 'stack' => 'succ', 'data' => array()),
                ),
            ),
            'share' => array(
                'name' => '固定分享',
                'legend' => array('图标次数', '图标人数', '好友/群按钮次数', '好友/群按钮人数', '好友/群成功次数', '好友/群成功人数', '朋友圈按钮次数', '朋友圈按钮人数', '朋友圈成功次数', '朋友圈成功人数'),
                'series' => array(
                    'share_icon_count' => array('name' => '图标次数', 'stack' => 'icon', 'data' => array()),
                    'share_icon_num' => array('name' => '图标人数', 'stack' => 'icon', 'data' => array()),
                    'share_friend_count' => array('name' => '好友/群按钮次数', 'stack' => 'fbtn', 'data' => array()),
                    'share_friend_num' => array('name' => '好友/群按钮人数', 'stack' => 'fbtn', 'data' => array()),
                    'share_social_count' => array('name' => '朋友圈成功次数', 'stack' => 'sbtn', 'data' => array()),
                    'share_social_num' => array('name' => '朋友圈成功人数', 'stack' => 'sbtn', 'data' => array()),
                    'share_fsucc_count' => array('name' => '好友/群成功次数', 'stack' => 'fsucc', 'data' => array()),
                    'share_fsucc_num' => array('name' => '好友/群成功人数', 'stack' => 'fsucc', 'data' => array()),
                    'share_ssucc_count' => array('name' => '朋友圈成功次数', 'stack' => 'ssucc', 'data' => array()),
                    'share_ssucc_num' => array('name' => '朋友圈成功人数', 'stack' => 'ssucc', 'data' => array()),
                ),
            ),
            'diamond' => array(
                'name' => '领取钻石',
                'legend' => array('图标次数', '图标人数', '好友/群按钮次数', '好友/群按钮人数', '好友/群成功次数', '好友/群成功人数', '朋友圈按钮次数', '朋友圈按钮人数', '朋友圈成功次数', '朋友圈成功人数'),
                'series' => array(
                    'diamond_icon_count' => array('name' => '图标次数', 'stack' => 'icon', 'data' => array()),
                    'diamond_icon_num' => array('name' => '图标人数', 'stack' => 'icon', 'data' => array()),
                    'diamond_friend_count' => array('name' => '好友/群按钮次数', 'stack' => 'fbtn', 'data' => array()),
                    'diamond_friend_num' => array('name' => '好友/群按钮人数', 'stack' => 'fbtn', 'data' => array()),
                    'diamond_social_count' => array('name' => '朋友圈成功次数', 'stack' => 'sbtn', 'data' => array()),
                    'diamond_social_num' => array('name' => '朋友圈成功人数', 'stack' => 'sbtn', 'data' => array()),
                    'diamond_fsucc_count' => array('name' => '好友/群成功次数', 'stack' => 'fsucc', 'data' => array()),
                    'diamond_fsucc_num' => array('name' => '好友/群成功人数', 'stack' => 'fsucc', 'data' => array()),
                    'diamond_ssucc_count' => array('name' => '朋友圈成功次数', 'stack' => 'ssucc', 'data' => array()),
                    'diamond_ssucc_num' => array('name' => '朋友圈成功人数', 'stack' => 'ssucc', 'data' => array()),
                ),
            ),
            'activity' => array(
                'name' => '活动',
                'legend' => array('图标次数', '图标人数', '按钮次数', '按钮人数', '成功次数', '成功人数'),
                'series' => array(
                    'activity_icon_count' => array('name' => '图标次数', 'stack' => 'icon', 'data' => array()),
                    'activity_icon_num' => array('name' => '图标人数', 'stack' => 'icon', 'data' => array()),
                    'activity_btn_count' => array('name' => '按钮次数', 'stack' => 'btn', 'data' => array()),
                    'activity_btn_num' => array('name' => '按钮人数', 'stack' => 'btn', 'data' => array()),
                    'activity_succ_count' => array('name' => '成功次数', 'stack' => 'succ', 'data' => array()),
                    'activity_succ_num' => array('name' => '成功人数', 'stack' => 'succ', 'data' => array()),
                ),
            ),
            'club' => array(
                'name' => '亲友圈',
                'legend' => array(
                    '好友/群按钮次数',
                    '好友/群按钮人数',
                    '朋友圈按钮次数',
                    '朋友圈按钮人数',
                    '二维码按钮次数',
                    '二维码按钮人数',
                    '好友/群成功次数',
                    '好友/群成功人数',
                    '朋友圈成功次数',
                    '朋友圈成功人数',
                    '二维码成功次数',
                    '二维码成功人数',
                ),
                'series' => array(
                    'club_friend_count' => array('name' => '好友/群按钮次数', 'stack' => 'icon', 'data' => array()),
                    'club_friend_num' => array('name' => '好友/群按钮人数', 'stack' => 'icon', 'data' => array()),
                    'club_social_count' => array('name' => '朋友圈按钮次数', 'stack' => 'fbtn', 'data' => array()),
                    'club_social_num' => array('name' => '朋友圈按钮人数', 'stack' => 'fbtn', 'data' => array()),
                    'club_qrcode_count' => array('name' => '二维码按钮次数', 'stack' => 'sbtn', 'data' => array()),
                    'club_qrcode_num' => array('name' => '二维码按钮人数', 'stack' => 'sbtn', 'data' => array()),
                    'club_fsucc_count' => array('name' => '好友/群成功次数', 'stack' => 'fsucc', 'data' => array()),
                    'club_fsucc_num' => array('name' => '好友/群成功人数', 'stack' => 'fsucc', 'data' => array()),
                    'club_ssucc_count' => array('name' => '朋友圈成功次数', 'stack' => 'ssucc', 'data' => array()),
                    'club_ssucc_num' => array('name' => '朋友圈成功人数', 'stack' => 'ssucc', 'data' => array()),
                    'club_qsucc_count' => array('name' => '二维码成功次数', 'stack' => 'ssucc', 'data' => array()),
                    'club_qsucc_num' => array('name' => '二维码成功人数', 'stack' => 'ssucc', 'data' => array()),
                ),
            ),
            'room' => array(
                'name' => '房间邀请',
                'legend' => array('按钮次数', '按钮人数', '成功次数', '成功人数'),
                'series' => array(
                    'room_btn_count' => array('name' => '按钮次数', 'stack' => 'btn', 'data' => array()),
                    'room_btn_num' => array('name' => '按钮人数', 'stack' => 'btn', 'data' => array()),
                    'room_succ_count' => array('name' => '成功次数', 'stack' => 'succ', 'data' => array()),
                    'room_succ_num' => array('name' => '成功人数', 'stack' => 'succ', 'data' => array()),
                ),
            ),
        );

        foreach ($chartMap as $k => $v) {
            foreach ($v['series'] as $i => $j) {
                $chartMap[$k]['series'][$i]['data'] = array_column($list, $i);
            }
        }

        $ret['data']['chartMap'] = $chartMap;
        return $ret;
    }

    /**
     * 根据条件获取统计数据列表，不分页
     * @author Carter
     */
    public function queryStatGameShareAllList($attr, $field = '*')
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array();
        if ($attr['game_id']) {
            $where['game_id'] = $attr['game_id'];
        }
        if ($attr['start_time']) {
            $where['stat_time'][] = array('egt', $attr['start_time']);
        }
        if ($attr['end_time']) {
            $where['stat_time'][] = array('elt', $attr['end_time']);
        }
        if ($attr['stat_time']) {
            $where['stat_time'][] = array('eq', $attr['stat_time']);
        }

        try {
            $list = $this->field($field)->where($where)->order("stat_time DESC")->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryStatGameShareAllList] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $list;
        return $ret;
    }

    /**
     * 获取分享统计表指定游戏最后统计时间
     * @author Carter
     */
    public function queryStatGameShareLastTime($gameId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try {
            $info = $this->field('id,stat_time')->where(array('game_id' => $gameId))->order('stat_time DESC')->find();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryStatGameShareLastTime] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $info;

        return $ret;
    }

    /**
     * 按日期插入指定游戏的分享统计记录
     * @author Carter
     */
    public function insertGameShareStatByDate($gameId, $statTime, $statData)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $insertData = array(
            'game_id' => $gameId,
            'award_icon_count' => $statData['awardIconCount'],
            'award_icon_num' => $statData['awardIconNum'],
            'award_btn_count' => $statData['awardBtnCount'],
            'award_btn_num' => $statData['awardBtnNum'],
            'award_succ_count' => $statData['awardSuccCount'],
            'award_succ_num' => $statData['awardSuccNum'],
            'share_icon_count' => $statData['shareIconCount'],
            'share_icon_num' => $statData['shareIconNum'],
            'share_friend_count' => $statData['shareFriendCount'],
            'share_friend_num' => $statData['shareFriendNum'],
            'share_social_count' => $statData['shareSocialCount'],
            'share_social_num' => $statData['shareSocialNum'],
            'share_fsucc_count' => $statData['shareFsuccCount'],
            'share_fsucc_num' => $statData['shareFsuccNum'],
            'share_ssucc_count' => $statData['shareSsuccCount'],
            'share_ssucc_num' => $statData['shareSsuccNum'],
            'diamond_icon_count' => $statData['diamondIconCount'],
            'diamond_icon_num' => $statData['diamondIconNum'],
            'diamond_friend_count' => $statData['diamondFriendCount'],
            'diamond_friend_num' => $statData['diamondFriendNum'],
            'diamond_social_count' => $statData['diamondSocialCount'],
            'diamond_social_num' => $statData['diamondSocialNum'],
            'diamond_fsucc_count' => $statData['diamondFsuccCount'],
            'diamond_fsucc_num' => $statData['diamondFsuccNum'],
            'diamond_ssucc_count' => $statData['diamondSsuccCount'],
            'diamond_ssucc_num' => $statData['diamondSsuccNum'],
            'activity_icon_count' => $statData['activityIconCount'],
            'activity_icon_num' => $statData['activityIconNum'],
            'activity_btn_count' => $statData['activityBtnCount'],
            'activity_btn_num' => $statData['activityBtnNum'],
            'activity_succ_count' => $statData['activitySuccCount'],
            'activity_succ_num' => $statData['activitySuccNum'],
            'club_friend_count' => $statData['clubFriendCount'],
            'club_friend_num' => $statData['clubFriendNum'],
            'club_social_count' => $statData['clubSocialCount'],
            'club_social_num' => $statData['clubSocialNum'],
            'club_qrcode_count' => $statData['clubQrcodeCount'],
            'club_qrcode_num' => $statData['clubQrcodeNum'],
            'club_fsucc_count' => $statData['clubFsuccCount'],
            'club_fsucc_num' => $statData['clubFsuccNum'],
            'club_ssucc_count' => $statData['clubSsuccCount'],
            'club_ssucc_num' => $statData['clubSsuccNum'],
            'club_qsucc_count' => $statData['clubQsuccCount'],
            'club_qsucc_num' => $statData['clubQsuccNum'],
            'room_btn_count' => $statData['roomBtnCount'],
            'room_btn_num' => $statData['roomBtnNum'],
            'room_succ_count' => $statData['roomSuccCount'],
            'room_succ_num' => $statData['roomSuccNum'],
            'stat_time' => $statTime,
        );

        try {
            $id = $this->add($insertData);
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertGameShareStatByDate] insert failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $id;

        return $ret;
    }

    /**
     * 根据id删除分享统计表记录
     * @author Carter
     */
    public function deleteGameShareStatById($id)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        try {
            $this->where(array('id' => $id))->delete();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[deleteGameShareStatById] delete failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        return $ret;
    }
}
