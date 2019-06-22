<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Logic;

use Common\Service\DbLoadConfigService;
use Home\Model\DsqpDict\DictConfigModel;
use Home\Model\Activity\ActivityListModel;
use Home\Model\Activity\BagListModel;
use Home\Model\Activity\RewardListModel;
use Home\Model\GameModel;

/**
 * Description of ActivityLogic
 *
 * @author SDF-KaiF
 */
class ActivityLogic {

    /*****************活动配置***************************/
    public function getActList($params)
    {
        $actModel = new ActivityListModel();
        $list = $actModel->getList($params);
        if (!$list) {
            return $list;
        }

        $gameModel = new GameModel();
        $gameList = $gameModel->queryGameAllList(1, ['game_id', 'game_name']);
        $gameMap = array_column($gameList['data'], 'game_name', 'game_id');

        $ret = [];
        foreach ($list as $val) {
            $val['status'] = $actModel->getStatusName($val['status']);
            $val['pName'] = isset($gameMap[$val['pid']]) ? $gameMap[$val['pid']] : '';
            $val['actTime'] = date('Y/m/d H:i:s', $val['start_time']). ' ~ '. date('Y/m/d H:i:s', $val['end_time']);
            $val['updateInfo'] = $val['update_by']. ' <i>('. date('Y/m/d', $val['update_time']). ')</i>';
            $ret[] = $val;
        }
        return $ret;
    }

    public function getGameList()
    {
        $gameModel = new GameModel();
        $gameList = $gameModel->queryGameAllList(1, ['game_id', 'game_name']);
        return $gameList['code'] === ERRCODE_SUCCESS ? array_column($gameList['data'], 'game_name', 'game_id') : [];
    }

    /**
     * 获取活动服的配置
     * @author rave
     */
    public function getActConf()
    {
        $ret = [];
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DICT_DB', 0)) {
            try {
                $modconfig = new DictConfigModel();
                $ret = $modconfig->getConfig('php_activity_config');
            } catch (\Exception $e) {
                $ret['code'] = ERRCODE_DB_SELECT_ERR;
                $ret['msg'] = "数据库连接失败，错误信息：" . $e->getMessage();
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = "数据库配置加载失败，请确认数据库配置信息";
            return $ret;
        }

        return $ret['code'] === ERRCODE_SUCCESS ? json_decode($ret['data'], true) : [];
    }

    /**
     * 更新活动服的配置
     * @author rave
     */
    public function updateSrvConf($data)
    {
        $ret = [];
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DICT_DB', 0)) {
            try {
                $modconfig = new DictConfigModel();
            } catch (\Exception $e) {
                $ret['code'] = ERRCODE_DB_SELECT_ERR;
                $ret['msg'] = "数据库连接失败，错误信息：" . $e->getMessage();
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = "数据库配置加载失败，请确认数据库配置信息";
            return $ret;
        }

        return $modconfig->setConfig('php_activity_config', json_encode($data));
    }

    public function saveActInfo($data)
    {
        $gameList = $data['pid'];
        if (in_array(0, $gameList)) {
            $gameModel = new GameModel();
            $gameArr = $gameModel->queryGameAllList(1, ['game_id', 'game_name']);
            $gameList = array_column($gameArr['data'], 'game_id');
        }

        $saveData['act_id'] = $data['act_id'];
        $saveData['name'] = $data['name'];
        $saveData['status'] = 0;
        $saveData['remark'] = $data['remark'];
        $saveData['start_time'] = strtotime($data['stime']);
        $saveData['end_time'] = strtotime($data['etime']);
        $saveData['create_time'] = time();
        $saveData['update_time'] = $saveData['create_time'];
        $saveData['create_by'] = C('G_USER.username');
        $saveData['update_by'] = $saveData['create_by'];
        
        $actModel = new ActivityListModel();
        foreach ($gameList as $val) {
            $saveData['pid'] = $val;
            $actModel->add($saveData);
        }
        return true;
    }

    /*****************礼包配置***************************/
    public function getBagList($params)
    {
        $bagModel = new BagListModel();
        $list = $bagModel->getList($params);

        return $list ?: [];
    }

    public function saveBagInfo($data)
    {
        $ret = [];
        if (!is_array($data) || empty($data['data'])) {
            return $ret;
        }

        //格式化数据
        $saveData = [];
        $content = $data['data'];
        foreach ($content['name'] as $key => $val) {
            $tmp = [];
            $tmp['id'] = $key + 1;
            $tmp['name'] = $val;
            $tmp['type'] = $content['type'][$key];
            $tmp['val'] = (float)$content['val'][$key];
            $tmp['percent'] = (float)$content['percent'][$key];
            $tmp['showPanel'] = $content['showPanel'][$key];
            $saveData['data'][] = $tmp;
        }

        $saveData['name'] = $data['name'];
        $saveData['luck'] = $data['luck'];
        $saveData['data'] = json_encode($saveData['data']);
        $saveData['limit0'] = $data['limit0'];
        $saveData['limit1'] = $data['limit1'];
        $saveData['limit2'] = $data['limit2'];
        $saveData['limit3'] = $data['limit3'];
        $saveData['update_time'] = time();
        $saveData['update_by'] = C("G_USER.username");

        $bagModel = new BagListModel();
        if ($data['id']) {
            $rs = $bagModel->where(['id' => $data['id']])->save($saveData);
            $id = $data['id'];
        } else {
            $saveData['create_time'] = $saveData['update_time'];
            $saveData['create_by'] = $saveData['update_by'];
            $id = $bagModel->add($saveData);
        }

        if (!$id) {
            return $ret;
        }

        $ret = $saveData;
        $ret['id'] = $id;
        $ret['data'] = json_decode($saveData['data'], true);
        unset($ret['create_time'], $ret['update_time'], $ret['create_by'], $ret['update_by']);
        return $ret;
    }

    /*****************实物奖品配置***************************/
    public function getRewardList()
    {
        return [];
    }
}
