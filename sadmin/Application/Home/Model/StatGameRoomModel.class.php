<?php
namespace Home\Model;

use Think\Model;

class StatGameRoomModel extends Model
{
    // 子玩法数据类型
    const DATA_TYPE_COUNT = 1; // 成功开局次数
    const DATA_TYPE_NUM = 2; // 参与人数
    public $dataTypeMap = array(
        self::DATA_TYPE_COUNT => '成功开局次数',
        self::DATA_TYPE_NUM => '参与人数',
    );

    // 房间类型
    const ROOM_TYPE_GAME = 1; // 普通房间
    const ROOM_TYPE_CLUB = 2; // 俱乐部房间
    public $roomTypeMap = array(
        self::ROOM_TYPE_GAME => '普通房间',
        self::ROOM_TYPE_CLUB => '俱乐部房间',
    );

    /**
     * 根据条件获取玩法统计数据列表，不分页
     * @author Carter
     */
    public function queryStatGameRoomAllList($attr, $field)
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
        if ($attr['data_time']) {
            $where['data_time'] = $attr['data_time'];
        }

        try {
            $list = $this->field($field)->where($where)->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryStatGameRoomAllList] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $list;
        return $ret;
    }

    /**
     * 获取玩法统计（按玩法）页面数据
     * @author Carter
     */
    public function queryGameRoomListForPlaytype($gameId, $showPlay, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        // 根据时间条件，获取统计的时间段
        $firstWhere = array(
            'game_id' => $gameId,
        );
        if ($attr['start_date']) {
            $firstWhere['data_time'][] = array('egt', strtotime($attr['start_date']));
        }
        if ($attr['end_date']) {
            $firstWhere['data_time'][] = array('elt', strtotime($attr['end_date']));
        }

        try {
            // 分页获取
            $pageSize = C('PAGE_SIZE');
            $row = $this->field('count(DISTINCT `data_time`) as c')->where($firstWhere)->find();
            $count = $row['c'];
            $paginate = new \Think\Page($count, $pageSize);
            $pagination = $paginate->show();
            $page = $paginate->getCurPage();

            $list = $this->field('data_time')->where($firstWhere)->group('data_time')->order('data_time DESC')->page("{$page},{$pageSize}")->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGameRoomListForPlaytype] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data']['pagination'] = $pagination;

        $timeArr = array_column($list, 'data_time');
        if (empty($timeArr)) {
            $ret['data']['list'] = array();
            return $ret;
        }

        $secondWhere = array(
            'game_id' => $gameId,
            // 拿到时间段区间后，获取该区间内的玩法统计数
            'data_time' => array('in', $timeArr),
            // 不要漏掉总览行
            'game_item_id' => array('in', array_merge(array(0), $showPlay)),
        );
        // 房间类型
        if ($attr['room_type']) {
            $secondWhere['room_type'] = $attr['room_type'];
        }

        $field = 'game_item_id,create_count,create_access_count,two_count,three_count,four_count,data_time';
        try {
            $dataList = $this->field($field)->where($secondWhere)->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGameRoomListForPlaytype] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $showList = array_fill_keys($timeArr, array(
            // 开局次数
            'createCount' => 0,
            // 成功开局次数
            'createAccessCount' => 0,
            // 各子玩法信息
            'play' => array_fill_keys($showPlay, 0),
        ));
        foreach ($dataList as $v) {
            if (0 == $v['game_item_id']) {
                // 开局次数
                $showList[$v['data_time']]['createCount'] += $v['create_count'];
                // 成功开局次数
                $showList[$v['data_time']]['createAccessCount'] += $v['create_access_count'];
            } else {
                // 展示成功开局次数
                if (self::DATA_TYPE_COUNT == $attr['data_type']) {
                    // 子玩法成功开局次数
                    $showList[$v['data_time']]['play'][$v['game_item_id']] += $v['create_access_count'];
                }
                // 展示参与人数
                else if (self::DATA_TYPE_NUM == $attr['data_type']) {
                    // 子玩法人数
                    $showList[$v['data_time']]['play'][$v['game_item_id']] += $v['two_count'] * 2;
                    $showList[$v['data_time']]['play'][$v['game_item_id']] += $v['three_count'] * 3;
                    $showList[$v['data_time']]['play'][$v['game_item_id']] += $v['four_count'] * 4;
                }
            }
        }

        $ret['data']['list'] = $showList;
        return $ret;
    }

    /**
     * 获取玩法统计（按人数）页面数据
     * @author Carter
     */
    public function queryGameRoomListForNumber($gameId, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        // 根据时间条件，获取统计的时间段
        $firstWhere = array(
            'game_id' => $gameId,
            'game_item_id' => 0,
        );
        if ($attr['start_date']) {
            $firstWhere['data_time'][] = array('egt', strtotime($attr['start_date']));
        }
        if ($attr['end_date']) {
            $firstWhere['data_time'][] = array('elt', strtotime($attr['end_date']));
        }

        try {
            // 分页获取
            $pageSize = C('PAGE_SIZE');
            $row = $this->field('count(DISTINCT `data_time`) as c')->where($firstWhere)->find();
            $count = $row['c'];
            $paginate = new \Think\Page($count, $pageSize);
            $pagination = $paginate->show();
            $page = $paginate->getCurPage();

            $list = $this->field('data_time')->where($firstWhere)->group('data_time')->order('data_time DESC')->page("{$page},{$pageSize}")->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGameRoomListForNumber] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data']['pagination'] = $pagination;

        $timeArr = array_column($list, 'data_time');
        if (empty($timeArr)) {
            $ret['data']['list'] = array();
            return $ret;
        }

        $secondWhere = array(
            'game_id' => $gameId,
            'game_item_id' => 0,
            // 拿到时间段区间后，获取该区间内的玩法统计数
            'data_time' => array('in', $timeArr),
        );
        // 房间类型
        if ($attr['room_type']) {
            $secondWhere['room_type'] = $attr['room_type'];
        }

        $field = 'create_count,create_access_count,two_count,three_count,four_count,data_time';
        try {
            $dataList = $this->field($field)->where($secondWhere)->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGameRoomListForNumber] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $showList = array_fill_keys($timeArr, array(
            // 开局次数
            'createCount' => 0,
            // 成功开局次数
            'createAccessCount' => 0,
            // 4人局次数
            'fourCount' => 0,
            // 3人局次数
            'threeCount' => 0,
            // 2人局次数
            'twoCount' => 0,
        ));
        foreach ($dataList as $v) {
            // 开局次数
            $showList[$v['data_time']]['createCount'] += $v['create_count'];
            // 成功开局次数
            $showList[$v['data_time']]['createAccessCount'] += $v['create_access_count'];
            // 4人局次数
            $showList[$v['data_time']]['fourCount'] += $v['four_count'];
            // 3人局次数
            $showList[$v['data_time']]['threeCount'] += $v['three_count'];
            // 2人局次数
            $showList[$v['data_time']]['twoCount'] += $v['two_count'];
        }

        $ret['data']['list'] = $showList;
        return $ret;
    }

    /**
     * 获取玩法统计（按玩法）的图形数据
     * @author Carter
     */
    public function queryGameRoomPlayChartData($gameId, $attr, $showPlay, $chartMap)
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
            'game_id' => $gameId,
            'game_item_id' => array('in', array_merge(array(0), $showPlay)),
            'data_time' => array(
                array('egt', $sTime),
                array('elt', $eTime),
            ),
        );
        if ($attr['room_type']) {
            $where['room_type'] = $attr['room_type'];
        }

        try {
            $field = 'game_item_id,create_count,create_access_count,two_count,three_count,four_count,data_time';
            $list = $this->field($field)->where($where)->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGameRoomPlayChartData] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $statData = array_fill_keys(array_keys($chartMap), array());

        foreach ($list as $v) {
            $date = date('Y-m-d', $v['data_time']);
            if (0 == $v['game_item_id']) {
                // 开局次数
                if (!isset($statData['create_count'][$date])) {
                    $statData['create_count'][$date] = 0;
                }
                $statData['create_count'][$date] += $v['create_count'];
                // 成功开局次数
                if (!isset($statData['create_access_count'][$date])) {
                    $statData['create_access_count'][$date] = 0;
                }
                $statData['create_access_count'][$date] += $v['create_access_count'];
            } else {
                if (self::DATA_TYPE_COUNT == $attr['data_type']) {
                    // 子玩法成功开局次数
                    if (!isset($statData["play_{$v['game_item_id']}"][$date])) {
                        $statData["play_{$v['game_item_id']}"][$date] = 0;
                    }
                    $statData["play_{$v['game_item_id']}"][$date] += $v['create_access_count'];
                } else if (self::DATA_TYPE_NUM == $attr['data_type']) {
                    // 子玩法人数
                    if (!isset($statData["play_{$v['game_item_id']}"][$date])) {
                        $statData["play_{$v['game_item_id']}"][$date] = 0;
                    }
                    $statData["play_{$v['game_item_id']}"][$date] += $v['two_count'] * 2;
                    $statData["play_{$v['game_item_id']}"][$date] += $v['three_count'] * 3;
                    $statData["play_{$v['game_item_id']}"][$date] += $v['four_count'] * 4;
                }
            }
        }

        $chartData = array();
        foreach ($statData as $key => $v) {
            // 按时间排序
            uksort($v, function($a, $b) {
                if ($a == $b) {
                    return 0;
                }
                return ($a < $b) ? -1 :1;
            });
            $chartData[] = array(
                'name' => $chartMap[$key],
                'key' => $key,
                'xAxis' => array_keys($v),
                'data' => array_values($v),
            );
        }

        $ret['data'] = $chartData;
        return $ret;
    }

    /**
     * 获取玩法统计（按人数）的图形数据
     * @author Carter
     */
    public function queryGameRoomNumChartData($gameId, $attr, $chartMap)
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
            'game_id' => $gameId,
            'game_item_id' => 0,
            'data_time' => array(
                array('egt', $sTime),
                array('elt', $eTime),
            ),
        );
        if ($attr['room_type']) {
            $where['room_type'] = $attr['room_type'];
        }

        try {
            $field = 'create_count,create_access_count,two_count,three_count,four_count,data_time';
            $list = $this->field($field)->where($where)->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGameRoomNumChartData] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $statData = array_fill_keys(array_keys($chartMap), array());

        foreach ($list as $v) {
            $date = date('Y-m-d', $v['data_time']);
            // 开局次数
            if (!isset($statData['create_count'][$date])) {
                $statData['create_count'][$date] = 0;
            }
            $statData['create_count'][$date] += $v['create_count'];
            // 成功开局次数
            if (!isset($statData['create_access_count'][$date])) {
                $statData['create_access_count'][$date] = 0;
            }
            $statData['create_access_count'][$date] += $v['create_access_count'];
            // 4人局次数
            if (!isset($statData['four_count'][$date])) {
                $statData['four_count'][$date] = 0;
            }
            $statData['four_count'][$date] += $v['four_count'];
            // 3人局次数
            if (!isset($statData['three_count'][$date])) {
                $statData['three_count'][$date] = 0;
            }
            $statData['three_count'][$date] += $v['three_count'];
            // 2人局次数
            if (!isset($statData['two_count'][$date])) {
                $statData['two_count'][$date] = 0;
            }
            $statData['two_count'][$date] += $v['two_count'];
        }

        $chartData = array();
        foreach ($statData as $key => $v) {
            // 按时间排序
            uksort($v, function($a, $b) {
                if ($a == $b) {
                    return 0;
                }
                return ($a < $b) ? -1 :1;
            });
            $chartData[] = array(
                'name' => $chartMap[$key],
                'key' => $key,
                'xAxis' => array_keys($v),
                'data' => array_values($v),
            );
        }

        $ret['data'] = $chartData;
        return $ret;
    }

    /**
     * 根据条件获取玩法统计（按玩法）的导出数据
     * @author Carter
     */
    public function queryGameRoomPlayExportData($gameId, $playMap, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $where = array(
            'game_id' => $gameId,
            'data_time' => array(
                array('egt', strtotime($attr['start_date'])),
                array('elt', strtotime($attr['end_date'])),
            ),
        );
        if ($attr['room_type']) {
            $where['room_type'] = $attr['room_type'];
        }

        try {
            $field = 'game_item_id,create_count,create_access_count,two_count,three_count,four_count,data_time';
            $list = $this->field($field)->where($where)->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGameRoomPlayExportData] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $exportData = array();
        foreach ($list as $v) {
            if (!isset($exportData[$v['data_time']])) {
                $exportData[$v['data_time']] = array(
                    'data_time' => date('Y-m-d', $v['data_time']),
                    'create_count' => 0,
                    'create_access_count' => 0,
                );
                foreach ($playMap as $playId => $playName) {
                    $exportData[$v['data_time']]["play_{$playId}"] = 0;
                }
            }
            if (0 == $v['game_item_id']) {
                // 开局次数
                $exportData[$v['data_time']]['create_count'] += $v['create_count'];
                // 成功开局次数
                $exportData[$v['data_time']]['create_access_count'] += $v['create_access_count'];
            } else {
                if (self::DATA_TYPE_COUNT == $attr['data_type']) {
                    // 子玩法成功开局次数
                    $exportData[$v['data_time']]["play_{$v['game_item_id']}"] += $v['create_access_count'];
                } else if (self::DATA_TYPE_NUM == $attr['data_type']) {
                    // 子玩法人数
                    $exportData[$v['data_time']]["play_{$v['game_item_id']}"] += $v['two_count'] * 2;
                    $exportData[$v['data_time']]["play_{$v['game_item_id']}"] += $v['three_count'] * 3;
                    $exportData[$v['data_time']]["play_{$v['game_item_id']}"] += $v['four_count'] * 4;
                }
            }
        }

        // 按时间排序
        uksort($exportData, function($a, $b) {
            if ($a == $b) {
                return 0;
            }
            return ($a > $b) ? -1 :1;
        });

        $ret['data'] = $exportData;
        return $ret;
    }

    /**
     * 根据条件获取玩法统计（按人数）的导出数据
     * @author Carter
     */
    public function queryGameRoomNumExportData($gameId, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $where = array(
            'game_id' => $gameId,
            'game_item_id' => 0,
            'data_time' => array(
                array('egt', strtotime($attr['start_date'])),
                array('elt', strtotime($attr['end_date'])),
            ),
        );
        if ($attr['room_type']) {
            $where['room_type'] = $attr['room_type'];
        }

        try {
            $field = 'create_count,create_access_count,two_count,three_count,four_count,data_time';
            $list = $this->field($field)->where($where)->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGameRoomNumExportData] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $exportData = array();
        foreach ($list as $v) {
            if (!isset($exportData[$v['data_time']])) {
                $exportData[$v['data_time']] = array(
                    'data_time' => date('Y-m-d', $v['data_time']),
                    'create_count' => 0,
                    'create_access_count' => 0,
                    'four_count' => 0,
                    'three_count' => 0,
                    'two_count' => 0,
                );
            }
            // 开局次数
            $exportData[$v['data_time']]['create_count'] += $v['create_count'];
            // 成功开局次数
            $exportData[$v['data_time']]['create_access_count'] += $v['create_access_count'];
            // 4人局次数
            $exportData[$v['data_time']]['four_count'] += $v['four_count'];
            // 3人局次数
            $exportData[$v['data_time']]['three_count'] += $v['three_count'];
            // 2人局次数
            $exportData[$v['data_time']]['two_count'] += $v['two_count'];
        }

        // 按时间排序
        uksort($exportData, function($a, $b) {
            if ($a == $b) {
                return 0;
            }
            return ($a > $b) ? -1 :1;
        });

        $ret['data'] = $exportData;
        return $ret;
    }

    /**
     * 插入玩法统计数据
     * @author Carter
     */
    public function insertStatGameRoom($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $insertData = array(
            'game_id' => $attr['gameId'],
            'game_item_id' => $attr['gameItemId'],
            'room_type' => $attr['roomType'],
            'create_count' => $attr['createCount'],
            'create_access_count' => $attr['createAccessCount'],
            'two_count' => $attr['twoCount'],
            'three_count' => $attr['threeCount'],
            'four_count' => $attr['fourCount'],
            'data_time' => $attr['dataTime'],
        );
        try {
            $id = $this->add($insertData);
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertStatGameRoom] update failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data']['id'] = $id;
        $ret['data']['insert'] = $insertData;

        return $ret;
    }

    /**
     * 更新玩法统计数据
     * @author Carter
     */
    public function updateStatGameRoom($id, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $updateData = array(
            'create_count' => $attr['createCount'],
            'create_access_count' => $attr['createAccessCount'],
            'two_count' => $attr['twoCount'],
            'three_count' => $attr['threeCount'],
            'four_count' => $attr['fourCount'],
        );
        try {
            $this->where(array('id' => $id))->save($updateData);
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateStatGameRoom] update failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $updateData;

        return $ret;
    }
}
