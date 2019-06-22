<?php
namespace Home\Logic;

use Common\Service\ApiService;
use Common\Service\DbLoadConfigService;
use Home\Model\DsqpActivity\ActActivityModel;
use Home\Model\DsqpActivity\ActConfigModel;
use Home\Model\DsqpDict\ActivityModel;
use Home\Model\DsqpDict\DictConfigModel;
use Home\Model\DsqpDict\DictPlaceModel;
use Home\Model\DsqpDict\DictPlaceGameModel;
use Home\Model\DsqpDict\PlaceConfigModel;
use Home\Model\DsqpDict\PlaceGameModel;
use Home\Model\DsqpDict\TaskModel;
use Home\Model\GameAppSubversionModel;
use Home\Model\GameDev\DevActivityModel;
use Home\Model\GameAppVersionModel;
use Home\Model\GameBlackListModel;
use Home\Model\GameChannelModel;
use Home\Model\GameConfModel;
use Home\Model\GameDev\SQuestModel;
use Home\Model\GameLandpageModel;
use Home\Model\GameModel;
use Home\Model\GameShareModel;
use Home\Model\GameShareAppidModel;
use Home\Model\GameShareDomainModel;
use Home\Model\GameUpdateWhiteListModel;
use Home\Model\GameWhiteListModel;
use Home\Model\StatLandpageModel;
use Home\Model\SysCacheModel;

/**
 * Description of GameconfLogic
 */
class GameconfLogic
{
    /************************************ 运营配置，改版前 ************************************/

    /**
     * 解析运营配置参数
     */
    private function parseShareConf($conf)
    {
        $dailyshare = get_object_vars(json_decode($conf['dailyShare']));
        $inviteshare = get_object_vars(json_decode($conf['inviteShare']));
        $clubshare = get_object_vars(json_decode($conf['clubShare']));
        $horse = $conf['marqueenTipMap'];
        $arr = explode("|", $horse);
        $horsetime = 0;
        $horsecont = array();
        if (count($arr) == 1) {
            $horsetime = 0;
            $horsecont[] = $horse;
        } else {
            foreach ($arr as $k=>$v) {
                if ($k == 0)
                    $horsetime = intval($v);
                else
                    $horsecont[] = $v;
            }
        }

        $weixincont = '';
        $weixintime = 0;
        $weixin = array();
        $proxycont = "";
        $proxytime = 0;
        $proxy = array();
        $cms = $conf['pyjRechargeTips'];
        $ar1 = explode("|", $cms);
        if (count($ar1) == 2) {
            $str = $ar1[0];
            if (strpos($str, '<') !== false) {
                $weixincont = substr($str, 0, strpos($str, '<'));
                $fp = strpos($str, '<');
                $lp = strpos($str, '>');
                if ($lp !== false && $fp !== false) {
                    $ct = substr($str, $fp+1, $lp-$fp-1);
                    $ar2 = explode(",", $ct);
                    $weixintime = intval($ar2[0]);
                    for ($ix = 1; $ix < count($ar2); $ix++) {
                        $weixin[] = $ar2[$ix];
                    }
                }
            }

            $str = $ar1[1];
            if (strpos($str, '<') !== false) {
                $proxycont = substr($str, 0, strpos($str, '<'));
                $fp = strpos($str, '<');
                $lp = strpos($str, '>');
                if ($lp !== false && $fp !== false) {
                    $ct = substr($str, $fp+1, $lp-$fp-1);
                    $ar2 = explode(",", $ct);
                    $proxytime = intval($ar2[0]);
                    for ($ix = 1; $ix < count($ar2); $ix++) {
                        $proxy[] = $ar2[$ix];
                    }
                }
            }
        }
        $imgurl = '';
        if($conf['pyjAdvertisementURLs']){
            //$imgdata = $conf['pyjAdvertisementURLs'];
            //$tmpdata = explode("|", $imgdata);
            //if (count($tmpdata) == 2) {
                $imgdata = json_decode($conf['pyjAdvertisementURLs']);//$tmpdata[0]
                $tmpdata = array();
                foreach ($imgdata as $v) {
		    $path = C('RESOURCE_SERVER_IPHOST').':'.C('RESOURCE_SERVER_PORT').'/'.PlaceConfigModel::ADV_PIC_PATH;
                    $dd = array();
                    $dd['delay'] = (intval($v->delay) == 0) ? 4: intval($v->delay);
                    $dd['imgName'] = $path.$v->imgName;
		    $path = C('RESOURCE_SERVER_IPHOST').':'.C('RESOURCE_SERVER_PORT').'/'.PlaceConfigModel::ADV_PIC_PATH;
                    $dd['imgSmall'] = $path.$v->imgSmall;
                    $dd['wechatids'] = ($v->wechat ? $v->wechat->id : array());
                    $dd['wechattime'] = intval($v->wechat->delay);
                    $tmpdata[] = $dd;
                }
                $imgurl = $tmpdata;
            //}
        }
        if (!empty($dailyshare['shareImageUrl'])) {
            $dailyshare['shareImageUrl'] = C('RESOURCE_SERVER_IPHOST').':'.C('RESOURCE_SERVER_PORT').'/'.$dailyshare['shareImageUrl'];
        }
        if (!empty($dailyshare['landingPageBackground'])) {
            $dailyshare['landingPageBackground'] = C('RESOURCE_SERVER_IPHOST').':'.C('RESOURCE_SERVER_PORT').'/'.$dailyshare['landingPageBackground'];
        }
        $logadv = array();
        if($conf['popupAdURLs']){
            $ladata = json_decode($conf['popupAdURLs']);
            foreach ($ladata as $v) {
                $ar = array();
                $ar['title'] = $v->title;
                $ar['wx'] = $v->wechat;
                $ar['pic'] = C('RESOURCE_SERVER_IPHOST').':'.C('RESOURCE_SERVER_PORT').'/'.PlaceConfigModel::ADV_PIC_PATH.$v->img;
                $logadv[] = $ar;
            }
        }
        $callagent = get_object_vars(json_decode($conf['recruitSetting']));
        if (!empty($callagent['wxid']))
            $wxids = explode("|", $callagent['wxid']);
        else
            $wxids = array();
        $callagent['wxid'] = $wxids;
        $callagent['wxtime'] = intval($callagent['wxtime']);
        $url = C('RESOURCE_SERVER_IPHOST').':'.C('RESOURCE_SERVER_PORT').'/'.$callagent['banner'];
        if (!empty($callagent['banner']) && check_url_file_exist($url))
            $callagent['banner'] = $url;
        else
            $callagent['banner'] = '';
        $dailyreward = array();
        if (!empty($conf['awardList'])) {
            $sep = explode(":", $conf["awardList"]);
            if (count($sep) == 2) {
                $dailyreward['reward'] = $sep[0];
                $dailyreward['number'] = $sep[1];
            }
        }

        $ret = array('sharetext'=>$dailyshare, 'shareclub'=>$clubshare,'shareinvite'=>$inviteshare,
            'horsetime'=>$horsetime, 'horsecont'=>$horsecont, 'paytip'=>$conf['pyjRechargeTips'],
            'weixincont'=>$weixincont, 'weixintime'=>$weixintime, 'weixin'=>$weixin,
            'proxycont'=>$proxycont, 'proxytime'=>$proxytime, 'proxy'=>$proxy, 'image'=>$imgurl,
            'logadv'=>$logadv, 'logadvswitch'=>intval($conf['popupAdEnable']), 'callagent'=>$callagent, 'dailyreward'=>$dailyreward);
        return $ret;
    }

    /**
     * 返回运营配置列表
     */
    public function getOperateList()
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $areaList = array();
        $confList = array();
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DICT_DB', 0)) {
            try {
                $modplace = new DictPlaceModel();
                $modconfig = new PlaceConfigModel();
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
        $modRet = $modplace->queryAreaList();
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $list = $modRet['data'];
        foreach ($list as $v) {
            if ($v['placeLevel'] == 1) {
                $be = false;
                foreach ($areaList as $vv) {
                    if ($vv['id'] == $v['placeID']) {
                        $be = true;
                        break;
                    }
                }
                if (!$be) {
                    $modRet = $modconfig->queryConfigByPlaceId($v['placeID']);
                    if (ERRCODE_SUCCESS !== $modRet['code']) {
                        $ret['code'] = $modRet['code'];
                        $ret['msg'] = $modRet['msg'];
                        return $ret;
                    }
                    $conf = $modRet['data'];
                    if ($conf) {
                        $confList[$v['placeID']] = $this->parseShareConf($conf);
                        $areaList[] = array('id'=>$v['placeID'], 'name'=>$v['placeName'], 'conf'=>true, 'sub'=>array());
                    } else {
                        $areaList[] = array('id'=>$v['placeID'], 'name'=>$v['placeName'], 'conf'=>false, 'sub'=>array());
                    }
                }
            } else if ($v['placeLevel'] == 2) {
                foreach ($areaList as $kk=>$vv) {
                    if ($vv['id'] == $v['parentPlaceID']) {
                        $modRet = $modconfig->queryConfigByPlaceId($v['placeID']);
                        if (ERRCODE_SUCCESS !== $modRet['code']) {
                            $ret['code'] = $modRet['code'];
                            $ret['msg'] = $modRet['msg'];
                            return $ret;
                        }
                        $conf = $modRet['data'];
                        if ($conf) {
                            $confList[$v['placeID']] = $this->parseShareConf($conf);
                            $areaList[$kk]['sub'][] = array('id'=>$v['placeID'], 'name'=>$v['placeName'], 'conf'=>true, 'sub'=>array());
                        } else {
                            $areaList[$kk]['sub'][] = array('id'=>$v['placeID'], 'name'=>$v['placeName'], 'conf'=>false, 'sub'=>array());
                        }
                        break;
                    }
                }
            } else if ($v['placeLevel'] == 3) {
                foreach ($areaList as $kk=>$vv) {
                    foreach ($vv['sub'] as $kkk=>$vvv) {
                        if ($vvv['id'] == $v['parentPlaceID']) {
                            $modRet = $modconfig->queryConfigByPlaceId($v['placeID']);
                            if (ERRCODE_SUCCESS !== $modRet['code']) {
                                $ret['code'] = $modRet['code'];
                                $ret['msg'] = $modRet['msg'];
                                return $ret;
                            }
                            $conf = $modRet['data'];
                            if ($conf) {
                                $confList[$v['placeID']] = $this->parseShareConf($conf);
                                $areaList[$kk]['sub'][$kkk]['sub'][] = array('id'=>$v['placeID'], 'name'=>$v['placeName'], 'conf'=>true, 'sub'=>array());
                            } else {
                                $areaList[$kk]['sub'][$kkk]['sub'][] = array('id'=>$v['placeID'], 'name'=>$v['placeName'], 'conf'=>false, 'sub'=>array());
                            }
                            break;
                        }
                    }
                }
            }
        }
        $ret['data']['placeList'] = $areaList;
        $ret['data']['confList'] = $confList;
        return $ret;
    }

    /**
     * 设置跑马灯
     */
    public function saveHorse($data)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DICT_DB', 0)) {
            try {
                $modconfig = new PlaceConfigModel();
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
        return $modconfig->updateHorse($data);
    }

    /**
     * 设置客服界面设置
     */
    public function saveCms($data)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DICT_DB', 0)) {
            try {
                $modconfig = new PlaceConfigModel();
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
        return $modconfig->updateCms($data);
    }

    /**
     * 广告配置
     */
    public function saveAdvImg($data)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DICT_DB', 0)) {
            try {
                $modconfig = new PlaceConfigModel();
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
        return $modconfig->updateAdv($data);
    }

    /**
     * 获取登陆广告配置列表
     * @author daniel
     */
    public function getLoginAdOperateList($hasLocal = false)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => '',
            'data' => array()
        );
        $areaList = array();
        $confList = array();
        $confSer = new DbLoadConfigService();
        $gameMod = new GameModel();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DICT_DB', 0)) {
            try {
                $modplace = new DictPlaceModel();
                $modconfig = new PlaceConfigModel();
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
        // 获取市包结构
        $localMap = $gameMod->localMap;
        $gameId = C('G_USER.gameid');
        if ($hasLocal && isset($localMap[$gameId])) {
            $firstId = array_merge([$gameId], array_keys($localMap[$gameId]));
        } else {
            $firstId = $gameId;
        }
        $modRet = $modplace->queryAreaList($firstId);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $list = $modRet['data'];
        foreach ($list as $v) {
            if ($v['placeLevel'] == 1) {
                $be = false;
                foreach ($areaList as $vv) {
                    if ($vv['id'] == $v['placeID']) {
                        $be = true;
                        break;
                    }
                }
                if (!$be) {
                    $modRet = $modconfig->queryConfigByPlaceId($v['placeID'], 'popupAdURLs, popupAdEnable');
                    if (ERRCODE_SUCCESS !== $modRet['code']) {
                        $ret['code'] = $modRet['code'];
                        $ret['msg'] = $modRet['msg'];
                        return $ret;
                    }
                    $conf = $modRet['data'];
                    if ($conf) {
                        $confList[$v['placeID']] = $this->parseLoginAd($conf);
                        $areaList[] = array('id' => $v['placeID'], 'name' => $v['placeName'], 'conf' => true, 'sub' => array());
                    } else {
                        $areaList[] = array('id' => $v['placeID'], 'name' => $v['placeName'], 'conf' => false, 'sub' => array());
                    }
                }
            } else if ($v['placeLevel'] == 2) {
                foreach ($areaList as $kk => $vv) {
                    if ($vv['id'] == $v['parentPlaceID']) {
                        $modRet = $modconfig->queryConfigByPlaceId($v['placeID'], 'popupAdURLs, popupAdEnable');
                        if (ERRCODE_SUCCESS !== $modRet['code']) {
                            $ret['code'] = $modRet['code'];
                            $ret['msg'] = $modRet['msg'];
                            return $ret;
                        }
                        $conf = $modRet['data'];
                        if ($conf) {
                            $confList[$v['placeID']] = $this->parseLoginAd($conf);
                            $areaList[$kk]['sub'][] = array('id' => $v['placeID'], 'name' => $v['placeName'], 'conf' => true, 'sub' => array());
                        } else {
                            $areaList[$kk]['sub'][] = array('id' => $v['placeID'], 'name' => $v['placeName'], 'conf' => false, 'sub' => array());
                        }
                        break;
                    }
                }
            } else if ($v['placeLevel'] == 3) {
                foreach ($areaList as $kk => $vv) {
                    foreach ($vv['sub'] as $kkk => $vvv) {
                        if ($vvv['id'] == $v['parentPlaceID']) {
                            $modRet = $modconfig->queryConfigByPlaceId($v['placeID'], 'popupAdURLs, popupAdEnable');
                            if (ERRCODE_SUCCESS !== $modRet['code']) {
                                $ret['code'] = $modRet['code'];
                                $ret['msg'] = $modRet['msg'];
                                return $ret;
                            }
                            $conf = $modRet['data'];
                            if ($conf) {
                                $confList[$v['placeID']] = $this->parseLoginAd($conf);
                                $areaList[$kk]['sub'][$kkk]['sub'][] = array('id' => $v['placeID'], 'name' => $v['placeName'], 'conf' => true, 'sub' => array());
                            } else {
                                $areaList[$kk]['sub'][$kkk]['sub'][] = array('id' => $v['placeID'], 'name' => $v['placeName'], 'conf' => false, 'sub' => array());
                            }
                            break;
                        }
                    }
                }
            }
        }
        $ret['data']['placeList'] = $areaList;
        $ret['data']['confList'] = $confList;
        return $ret;
    }

    /**
     * 解析登录广告配置
     * @author daniel
     */
    public function parseLoginAd($conf)
    {
        $logadv = array();
        if($conf['popupAdURLs']){
            $ladata = json_decode($conf['popupAdURLs']);
            foreach ($ladata as $v) {
                $ar = array();
                $ar['title'] = $v->title;
                $ar['wx'] = $v->wechat;
                $ar['pic'] = C('RESOURCE_SERVER_IPHOST').':'.C('RESOURCE_SERVER_PORT').'/'.PlaceConfigModel::ADV_PIC_PATH.$v->img;
                $logadv[] = $ar;
            }
        }
        $ret = [
            'logadv' => $logadv,
            'logadvswitch' => intval($conf['popupAdEnable'])
        ];
        return $ret;
    }

    /**
     * 登录广告配置
     */
    public function saveLogAdv($data) {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $confSer = new DbLoadConfigService();
        $gameid = C('G_USER.gameid');
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DICT_DB', 0)) {
            try {
                $modconfig = new PlaceConfigModel();
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

        // 判断此游戏和省级包横竖版是否一致，若不一致则不能关闭
        $placeIdMap = $modconfig->loginAdDirectionPlaceIdMap[$gameid];
        if ($data['switch'] == 0) {
            if (isset($placeIdMap[$data['confid']]) && $placeIdMap[$gameid] !== $placeIdMap[$data['confid']]) {
                $ret['code'] = ERRCODE_PARAM_INVALID;
                $direct = '';
                if ($placeIdMap[$data['confid']] == $modconfig::LOGIN_AD_HORIZONTAL) {
                    $direct = '横版';
                } elseif ($placeIdMap[$data['confid']] == $modconfig::LOGIN_AD_VERTICAL) {
                    $direct = '竖版';
                }
                $ret['msg'] = "此游戏登录广告为{$direct},与省包不一致,不能关闭";
                return $ret;
            }
        }
        $updateRet = $modconfig->updateLogAdv($data);
        if (ERRCODE_SUCCESS !== $updateRet['code']) {
            $ret['code'] = $updateRet['code'];
            $ret['msg'] = $updateRet['msg'];
            return $ret;
        }
        return $ret;
    }

    /**
     * 招募代理配置
     */
    public function saveCallAgent($data) {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DICT_DB', 0)) {
            try {
                $modconfig = new PlaceConfigModel();
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
        return $modconfig->updateCallAgent($data);
    }

    /**
     * 每日分享奖励配置
     */
    public function saveDailyReward($data)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DICT_DB', 0)) {
            try {
                $modconfig = new PlaceConfigModel();
                $modPlace = new DictPlaceModel();
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

        $isProvincePackage = $modPlace->queryIsProvincePackage($data['confid']);
        if (ERRCODE_SUCCESS !== $isProvincePackage['code']) {
            $ret['code'] = $isProvincePackage['code'];
            $ret['msg'] = $isProvincePackage['msg'];
            return $ret;
        }
        if ($isProvincePackage['data']) {
            if ((empty($data['club_diamond']) && empty($data['club_yuanbao'])) || (empty($data['no_club_diamond']) && empty($data['no_club_yuanbao']))) {
                $ret['code'] = ERRCODE_DATA_ERR;
                $ret['msg'] = "省包配置是缺省配置，亲友圈建立和非亲友圈奖励都必须存在一个";
                return $ret;
            }
        }
        return $modconfig->updateDailyReward($data);
    }

    /**
     * 返回每日奖励配置列表
     * @author daniel
     */
    public function getDailyRewardOperateList($hasLocal)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $areaList = array();
        $confList = array();
        $confSer = new DbLoadConfigService();
        $gameMod = new GameModel();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DICT_DB', 0)) {
            try {
                $modplace = new DictPlaceModel();
                $modconfig = new PlaceConfigModel();
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
        // 获取市包结构
        $localMap = $gameMod->localMap;
        $gameId = C('G_USER.gameid');
        if ($hasLocal && isset($localMap[$gameId])) {
            $firstId = array_merge([$gameId], array_keys($localMap[$gameId]));
        } else {
            $firstId = $gameId;
        }
        $modRet = $modplace->queryAreaList($firstId);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $list = $modRet['data'];
        foreach ($list as $v) {
            if ($v['placeLevel'] == 1) {
                $be = false;
                foreach ($areaList as $vv) {
                    if ($vv['id'] == $v['placeID']) {
                        $be = true;
                        break;
                    }
                }
                if (!$be) {
                    $modRet = $modconfig->queryConfigByPlaceId($v['placeID'], 'awardList');
                    if (ERRCODE_SUCCESS !== $modRet['code']) {
                        $ret['code'] = $modRet['code'];
                        $ret['msg'] = $modRet['msg'];
                        return $ret;
                    }
                    $conf = $modRet['data'];
                    if (isset($conf['awardList']) && !empty($conf['awardList'])) {
                        $confList[$v['placeID']] = $this->_parseDailyRewardConf($conf);
                        $areaList[] = array('id' => $v['placeID'], 'name' => $v['placeName'], 'conf' => true, 'sub' => array());
                    } else {
                        $areaList[] = array('id' => $v['placeID'], 'name' => $v['placeName'], 'conf' => false, 'sub' => array());
                    }
                }
            } else if ($v['placeLevel'] == 2) {
                foreach ($areaList as $kk => $vv) {
                    if ($vv['id'] == $v['parentPlaceID']) {
                        $modRet = $modconfig->queryConfigByPlaceId($v['placeID'], 'awardList');
                        if (ERRCODE_SUCCESS !== $modRet['code']) {
                            $ret['code'] = $modRet['code'];
                            $ret['msg'] = $modRet['msg'];
                            return $ret;
                        }
                        $conf = $modRet['data'];
                        if (isset($conf['awardList']) && !empty($conf['awardList'])) {
                            $confList[$v['placeID']] = $this->_parseDailyRewardConf($conf);
                            $areaList[$kk]['sub'][] = array('id' => $v['placeID'], 'name' => $v['placeName'], 'conf' => true, 'sub' => array());
                        } else {
                            $areaList[$kk]['sub'][] = array('id' => $v['placeID'], 'name' => $v['placeName'], 'conf' => false, 'sub' => array());
                        }
                        break;
                    }
                }
            } else if ($v['placeLevel'] == 3) {
                foreach ($areaList as $kk => $vv) {
                    foreach ($vv['sub'] as $kkk => $vvv) {
                        if ($vvv['id'] == $v['parentPlaceID']) {
                            $modRet = $modconfig->queryConfigByPlaceId($v['placeID'], 'awardList');
                            if (ERRCODE_SUCCESS !== $modRet['code']) {
                                $ret['code'] = $modRet['code'];
                                $ret['msg'] = $modRet['msg'];
                                return $ret;
                            }
                            $conf = $modRet['data'];
                            if (isset($conf['awardList']) && !empty($conf['awardList'])) {
                                $confList[$v['placeID']] = $this->_parseDailyRewardConf($conf);
                                $areaList[$kk]['sub'][$kkk]['sub'][] = array('id' => $v['placeID'], 'name' => $v['placeName'], 'conf' => true, 'sub' => array());
                            } else {
                                $areaList[$kk]['sub'][$kkk]['sub'][] = array('id' => $v['placeID'], 'name' => $v['placeName'], 'conf' => false, 'sub' => array());
                            }
                            break;
                        }
                    }
                }
            }
        }
        $ret['data']['placeList'] = $areaList;
        $ret['data']['confList'] = $confList;
        return $ret;
    }

    /**
     * 解析奖励配置
     * dict_place_config表awardList字段释义(后端定义)
     * 非亲友圈钻石数量|非亲友圈元宝数量OR亲友圈钻石数量|亲友圈元宝数量
     * @author daniel
     */
    private function _parseDailyRewardConf($conf)
    {
        $rewardMap = array(
            10008 => 'diamond',
            10009 => 'yuanbao'
        );
        $dailyreward = array(
            "clubUser" => array(
                "diamond" => 0,
                "yuanbao" => 0,
            ),
            "notClubUser" => array(
                "diamond" => 0,
                "yuanbao" => 0
            )
        );
        if (!empty($conf['awardList'])) {
            if (strpos($conf['awardList'], 'OR') !== false) {
                // 新版本分享奖励需区分分享用户是否为亲友圈用户
                // 格式:10008:1|10009:100OR10008:1|10009:100
                $clubSplit = explode('OR', $conf['awardList']);
                $notClubRewardSplit = explode('|', $clubSplit[0]);
                $notClubDiamondInfo = explode(':', $notClubRewardSplit[0]);
                $notClubYuanbaoInfo = explode(':', $notClubRewardSplit[1]);

                $clubRewardSplit = explode('|', $clubSplit[1]);
                $clubDiamondInfo = explode(':', $clubRewardSplit[0]);
                $clubYuanbaoInfo = explode(':', $clubRewardSplit[1]);
                if (count($notClubDiamondInfo) == 2 && count($notClubYuanbaoInfo) == 2 && count($clubDiamondInfo) == 2 && count($clubYuanbaoInfo) == 2) {
                    $dailyreward['clubUser'][$rewardMap[$clubDiamondInfo[0]]] = $clubDiamondInfo[1];
                    $dailyreward['clubUser'][$rewardMap[$clubYuanbaoInfo[0]]] = $clubYuanbaoInfo[1];
                    $dailyreward['notClubUser'][$rewardMap[$notClubDiamondInfo[0]]] = $notClubDiamondInfo[1];
                    $dailyreward['notClubUser'][$rewardMap[$notClubYuanbaoInfo[0]]] = $notClubYuanbaoInfo[1];
                }

            } else {
                // 旧版本不区分是否为亲友圈用户
                // 格式 10008:1或10008:1|10009:100
                if (strpos($conf['awardList'], '|') !== false) {
                    $rewardSplit = explode('|', $conf['awardList']);
                    $firstRewardInfo = explode(':', $rewardSplit[0]);
                    $secondRewardInfo = explode(':', $rewardSplit[1]);
                    $dailyreward['clubUser'][$rewardMap[$firstRewardInfo[0]]] = $dailyreward['notClubUser'][$rewardMap[$firstRewardInfo[0]]] = $firstRewardInfo[1];
                    $dailyreward['clubUser'][$rewardMap[$secondRewardInfo[0]]] = $dailyreward['notClubUser'][$rewardMap[$secondRewardInfo[0]]] = $secondRewardInfo[1];
                } else {
                    $sep = explode(":", $conf['awardList']);
                    if (count($sep) == 2) {
                        $dailyreward['clubUser'][$rewardMap[$sep[0]]] = $dailyreward['notClubUser'][$rewardMap[$sep[0]]] = $sep[1];
                    }
                }
            }
        }

        $ret = ['dailyreward' => $dailyreward];
        return $ret;
    }

    /**
     * 删除每日分享奖励配置
     * @author daniel
     */
    public function removeDailyRewardConf($placeId)
    {
        $ret = [
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => []
        ];

        $dbLoadService = new DbLoadConfigService();
        if (true == $dbLoadService->load(C('G_USER.gameid'), 'GAME_DICT_DB', 0)) {
            try {
                $modConfig = new PlaceConfigModel();
                $modPlace = new DictPlaceModel();
            } catch (\Exception $e) {
                $ret['code'] = ERRCODE_DB_SELECT_ERR;
                $ret['msg'] = "数据库连接失败，错误信息" . $e->getMessage();
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = "数据库配置加载失败，请确认数据库配置信息";
            return $ret;
        }
        $isProvincePackage = $modPlace->queryIsProvincePackage($placeId);
        if (ERRCODE_SUCCESS !== $isProvincePackage['code']) {
            $ret['code'] = $isProvincePackage['code'];
            $ret['msg'] = $isProvincePackage['msg'];
            return $ret;
        }
        if ($isProvincePackage['data']) {
            $ret['code'] = ERRCODE_DATA_ERR;
            $ret['msg'] = "省包配置是缺省配置，不能进行删除操作";
            return $ret;
        }
        $deleteRet = $modConfig->deleteDailyRewardConfig($placeId);
        if (ERRCODE_SUCCESS !== $deleteRet['code']) {
            $ret['code'] = $deleteRet['code'];
            $ret['msg'] = $deleteRet['msg'];
            return $ret;
        }
        return $deleteRet;
    }

    /**
     * 粘帖配置
     */
    public function pasteConfig($data)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DICT_DB', 0)) {
            try {
                $modconfig = new PlaceConfigModel();
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
        return $modconfig->pasteConfig($data['sourceid'], $data['destid']);
    }

    /**
     * 删除运营配置
     */
    public function deleteConfig($data)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DICT_DB', 0)) {
            try {
                $modconfig = new PlaceConfigModel();
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
        return $modconfig->deleteConfig($data['id']);
    }

    /**
     * 得到限时免钻信息
     */
    public function getFreeDiamod()
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $statusInfo = array();
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DEV_DB', 0)) {
            try {
                $devMod = new DevActivityModel();
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
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DICT_DB', 0)) {
            try {
                $mod = new ActivityModel();
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
        $modRet = $devMod->getActivityList();
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $activityList = $modRet['data'];
        foreach($activityList as $v) {
            $field = '';
            $modRet = $mod->queryInfoById($v['activityId']);
            if ($modRet['code'] != ERRCODE_SUCCESS) {
                $ret['code'] = ERRCODE_DB_UPDATE_ERR;
                $ret['msg'] = "数据库查询失败：".$modRet['msg'];
                return $ret;
            }
            $info = $modRet['data'];
            if ($info['type'] == 600) {// && $info['gameId'] == C('G_USER.gameid')
                $statusInfo['starttime'] = $info['startTime'];
                $statusInfo['endtime'] = $info['endTime'];
                if ($info['status'] == 1 && $v['status'] == 1) {
                    $statusInfo['activity'] = 1;
                } else {
                    $statusInfo['activity'] = 0;
                }
                break;
            }
        }
        $ret['data']['list'] = $statusInfo;
        return $ret;
    }

    /**
     * 更新限时免钻配置
     */
    public function setFreeDiamod($data)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DEV_DB', 0)) {
            try {
                $devMod = new DevActivityModel();
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
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DICT_DB', 0)) {
            try {
                $mod = new ActivityModel();
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
        $modRet = $devMod->getActivityList();
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $activityList = $modRet['data'];
        $activityid = 0;
        $curId = 0;
        foreach($activityList as $v) {
            $field = '';
            $modRet = $mod->queryInfoById($v['activityId']);
            if ($modRet['code'] != ERRCODE_SUCCESS) {
                $ret['code'] = ERRCODE_DB_UPDATE_ERR;
                $ret['msg'] = "数据库查询失败：".$modRet['msg'];
                return $ret;
            }
            $info = $modRet['data'];
            if ($info['type'] == 600) {// && $info['gameId'] == C('G_USER.gameid')
                $curId = $info['id'];
                $activityid = $v['id'];
                break;
            }
        }
        $updateData = array();
        $updateData['type'] = 600;
        $updateData['startTime'] = $data['starttime'];
        $updateData['vanishTime'] = $updateData['endTime'] = $data['endtime'];
        $active = 0;
        if ($data['active'] == 1)
            $active = 1;
        $updateData['status'] = $active;
        if ($curId <= 0) {
            $updateData['user'] = $updateData['content'] = $updateData['url'] = $updateData['share'] = '';
            $updateData['title'] = '限时免钻';
            $updateData['ui'] = $updateData['contentType'] = $updateData['flag'] = 0;
            $updateData['gameId'] = C('G_USER.gameid');
        }
        $modRet = $mod->updateInfo($curId, $updateData);

        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = "数据库更新失败：".$modRet['msg'];
            return $ret;
        }
        if ($curId <= 0)
            $curId = $modRet['data'];
        try {
            $tmpdata = array();
            if ($activityid > 0) {
                $tmpdata = array('status'=>$active);
            } else {
                $tmpdata = array('activityId'=>$curId, 'status'=>$active, 'createTime'=>date("Y-m-d H:i:s"));
            }
            $modRet = $devMod->updateInfo($activityid, $tmpdata);
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                if ($activityid<=0) {
                    $modRet = $mod->deleteInfo($curId);
                    if ($modRet['code'] != ERRCODE_SUCCESS) {
                        $ret['code'] = ERRCODE_DB_DELETE_ERR;
                        $ret['msg'] = "数据库删除失败：".$modRet['msg'];
                        return $ret;
                    }
                }
                $ret['code'] = ERRCODE_DB_UPDATE_ERR;
                $ret['msg'] = "数据库更新失败，错误信息：";
                return $ret;
            }
        } catch (\Exception $e) {
            if ($activityid<=0) {
                $mod->deleteInfo($curId);
            }
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = "数据库更新失败，错误信息：".$e->getMessage();
            return $ret;
        }
        $apiSer = new ApiService();
        // 调用服务端接口，刷新缓存
        $svrRet = $apiSer->kaifangApiQuery('/consoleactivity/?act=reload');
        if (ERRCODE_SUCCESS !== $svrRet['code']) {
            $ret['code'] = $svrRet['code'];
            $ret['msg'] = $svrRet['msg'];
            return $ret;
        }
        $svrRet = $apiSer->kaifangApiQuery('/consoletest/?act=toall');
        if (ERRCODE_SUCCESS !== $svrRet['code']) {
            $ret['code'] = $svrRet['code'];
            $ret['msg'] = $svrRet['msg'];
            return $ret;
        }
        return $ret;
    }

    /**
     * 得到新手红包信息
     */
    public function getNewRedPack() {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_ACTIVITY_DB', 0)) {
            try {
                $model = new ActActivityModel();
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
        $modRet = $model->getData(700);
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = "数据库更新失败：".$modRet['msg'];
            return $ret;
        }
        $info = $modRet['data'][0];
        $redpackinfo = array();
        $redpackinfo['starttime'] = $info['startTime'];
        $redpackinfo['endtime'] = $info['endTime'];
        $redpackinfo['activity'] = $info['status'];
        $cont = get_object_vars(json_decode($info['content']));
        $redpackinfo['title'] = $cont['title'];
        $redpackinfo['count'] = $cont['playCount'];
        $redpackinfo['code'] = $cont['wechat'];
        $redpackinfo['area'] = $cont['province'];
        $redpackinfo['display'] = $cont['displayActivityTime'];
        $ret['data'] = $redpackinfo;
        return $ret;
    }

    /**
     * 设置新手红包
     */
    public function setNewRedPack($data) {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_ACTIVITY_DB', 0)) {
            try {
                $model = new ActActivityModel();
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
        $modRet = $model->updateRedpackInfo($data);
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = "数据库更新失败：".$modRet['msg'];
            return $ret;
        }
        return $ret;
    }

    /**
     * 设置填写邀请人ID奖励
     * @author liyao
     */
    public function setInviteFriendId($data)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $sVal = '';
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DICT_DB', 0)) {
            try {
                $sVal = $data['number'];//$data['type'].':'.
                $mod = new DictConfigModel();
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
        $modRet = $mod->setConfig('invite_friend_reward_diamond', $sVal);
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = "数据库更新失败：".$modRet['msg'];
            return $ret;
        }
        /*$apiSer = new ApiService();
        // 调用服务端接口，刷新缓存
        $svrRet = $apiSer->kaifangApiQuery('/api/sys/?act=setSConfig&sKey=invite_friend_reward_diamond&sValue='.$sVal);
        if (ERRCODE_SUCCESS !== $svrRet['code']) {
            $ret['code'] = $svrRet['code'];
            $ret['msg'] = $svrRet['msg'];
            return $ret;
        }*/
        return $ret;
    }

    /**
     * 设置邀请好友奖励配置
     * @author liyao
     */
    public function setInviteFriendConf($data)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DEV_DB', 0)) {
            try {
                $devMod = new DevActivityModel();
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
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DICT_DB', 0)) {
            try {
                $modActive = new ActivityModel();
                $modTask = new TaskModel();
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
        $modRet = $devMod->getActivityList();
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $activityList = $modRet['data'];
        $activityid = 0;
        $curId = 0;
        foreach($activityList as $v) {
            $field = '';
            $modRet = $modActive->queryInfoById($v['activityId']);
            if ($modRet['code'] != ERRCODE_SUCCESS) {
                $ret['code'] = ERRCODE_DB_UPDATE_ERR;
                $ret['msg'] = "数据库查询失败：".$modRet['msg'];
                return $ret;
            }
            $info = $modRet['data'];
            if ($info['type'] == 200 && $info['flag'] != 998) {
                $curId = $info['id'];
                $activityid = $v['id'];
                break;
            }
        }
        $updateData = array();
        $updateData['type'] = 200;
        $active = 1;
        $updateData['status'] = $active;
        $updateData['startTime'] = date("Y-m-d");
        $updateData['endTime'] = '2099-1-1';//date("Y-m-d");
        $updateData['vanishTime'] = '2099-1-1';
        if ($curId <= 0) {
            $updateData['user'] = $updateData['url'] = $updateData['share'] = '';
            $updateData['title'] = $updateData['content'] = '邀请好友';
            $updateData['ui'] = $updateData['contentType'] = $updateData['flag'] = 0;
            $updateData['gameId'] = C('G_USER.gameid');
        }
        $modRet = $modActive->updateInfo($curId, $updateData);

        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = "数据库更新失败：".$modRet['msg'];
            return $ret;
        }
        if ($curId <= 0)
            $curId = $modRet['data'];
        try {
            if ($activityid > 0) {
                $tmpdata = array('status'=>$active);
            } else {
                $tmpdata = array('activityId'=>$curId, 'status'=>$active, 'createTime'=>date("Y-m-d H:i:s"));
            }
            $modRet = $devMod->updateInfo($activityid, $tmpdata);
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                if ($activityid<=0) {
                    $modRet = $modActive->deleteInfo($curId);
                    if ($modRet['code'] != ERRCODE_SUCCESS) {
                        $ret['code'] = ERRCODE_DB_DELETE_ERR;
                        $ret['msg'] = "数据库删除失败：".$modRet['msg'];
                        return $ret;
                    }
                }
                $ret['code'] = ERRCODE_DB_UPDATE_ERR;
                $ret['msg'] = "数据库更新失败，错误信息：";
                return $ret;
            }
        } catch (\Exception $e) {
            if ($activityid<=0) {
                $modActive->deleteInfo($curId);
            }
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = "数据库更新失败，错误信息：".$e->getMessage();
            return $ret;
        }
        $modTask->deleteInfo($curId);
        $no = 1;
        foreach ($data as $v) {
            $v['no'] = $no++;
            $v['activityId'] = $curId;
            $modRet = $modTask->addRewardInfo($v);
            if ($modRet['code'] != ERRCODE_SUCCESS) {
                $ret['code'] = ERRCODE_DB_UPDATE_ERR;
                $ret['msg'] = "数据库更新失败：".$modRet['msg'];
                return $ret;
            }
        }
        $apiSer = new ApiService();
        // 调用服务端接口，刷新缓存
        $svrRet = $apiSer->kaifangApiQuery('/consoleactivity/?act=reload');
        if (ERRCODE_SUCCESS !== $svrRet['code']) {
            $ret['code'] = $svrRet['code'];
            $ret['msg'] = $svrRet['msg'];
            return $ret;
        }
        return $ret;
    }

    /**
     * 取得好友配置
     * @author liyao
     */
    public function getInviteFriend()
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $statusInfo = array('type'=>'','number'=>0, 'act'=>0,'list'=>array());
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DEV_DB', 0)) {
            try {
                $devMod = new DevActivityModel();
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
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DICT_DB', 0)) {
            try {
                $modActive = new ActivityModel();
                $modTask = new TaskModel();
                $devConfMod = new DictConfigModel();
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
        $modRet = $devMod->getActivityList();
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $activityList = $modRet['data'];
        $modRet = $devConfMod->getConfig('invite_friend_reward_diamond');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $statusInfo['type'] = 10008;
        $statusInfo['number'] = $modRet['data'];
        $activeId = 0;
        $actActive = 0;
        foreach($activityList as $v) {
            $field = '';
            $modRet = $modActive->queryInfoById($v['activityId']);
            if ($modRet['code'] != ERRCODE_SUCCESS) {
                $ret['code'] = ERRCODE_DB_UPDATE_ERR;
                $ret['msg'] = "数据库查询失败：".$modRet['msg'];
                return $ret;
            }
            $info = $modRet['data'];
            if ($info['type'] == 200 && $info['flag'] != 998) {
                if ($info['status'] == 1 && $v['status'] == 1) {
                    $activeId = $v['activityId'];
                //    break;
                }
            }
            if ($info['type'] == 200 && $info['gameId'] == C('G_USER.gameid') && $info['flag'] == 998
                && $info['status'] == 1 && $v['status'] == 1 && time() >= strtotime($info['startTime'])
                && time() < strtotime($info['endTime'])) {
                $actActive = 1;
            }
        }
        $statusInfo['act'] = $actActive;
        $statusInfo['list'] = array();
        if ($activeId > 0) {
            $modRet = $modTask->queryRewardById($activeId);
            if ($modRet['code'] != ERRCODE_SUCCESS) {
                $ret['code'] = ERRCODE_DB_UPDATE_ERR;
                $ret['msg'] = "数据库查询失败：".$modRet['msg'];
                return $ret;
            }
            $no = 1;
            foreach ($modRet['data'] as $v) {
                $arr = array();
                $arr['no'] = $no;
                $arr['event_num'] = $v['eventNum'];
                $tar2 = explode(",", $v["reward"]);//get_object_vars(json_decode($v['reward']));
                $tar = array();
                foreach ($tar2 as $tv) {
                    $vv = explode(":", $tv);
                    $tar[$vv[0]] = $vv[1];
                }
                $idx = 1;
                foreach ($tar as $kk=>$vv) {
                    $arr['reward_type'.$idx] = $kk;
                    $arr['reward_val'.$idx] = $vv;
                    $idx++;
                }
                $statusInfo['list'][] = $arr;
                $no++;
            }
        }
        $ret['data']['list'] = $statusInfo;
        return $ret;
    }

    /**
     * 设置活动配置的邀请好友奖励配置
     * @author liyao
     */
    public function setActInviteFriendConf($data) {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DEV_DB', 0)) {
            try {
                $devMod = new DevActivityModel();
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
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DICT_DB', 0)) {
            try {
                $modActive = new ActivityModel();
                $modTask = new TaskModel();
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
        $modRet = $devMod->getActivityList();
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $activityList = $modRet['data'];
        $activityid = 0;
        $curId = 0;
        $ivtId = $ivtActId =0;
        foreach($activityList as $v) {
            $field = '';
            $modRet = $modActive->queryInfoById($v['activityId']);
            if ($modRet['code'] != ERRCODE_SUCCESS) {
                $ret['code'] = ERRCODE_DB_UPDATE_ERR;
                $ret['msg'] = "数据库查询失败：".$modRet['msg'];
                return $ret;
            }
            $info = $modRet['data'];
            if ($info['type'] == 200 && $info['gameId'] == C('G_USER.gameid') && $info['flag'] == 998) {
                $curId = $info['id'];
                $activityid = $v['id'];
            }
            if ($info['type'] == 200 && $info['flag'] != 998) {
                $ivtId = $v['id'];
                $ivtActId = $info['id'];
            }
        }
        $updateData = array();
        $updateData['type'] = 200;
        $active = $data['status'];
        $updateData['status'] = $active;
        $updateData['startTime'] = $data['starttime'];
        $updateData['endTime'] = $data['endtime'];
        $updateData['vanishTime'] = $data['endtime'];
        if ($curId <= 0) {
            $updateData['user'] = $updateData['url'] = $updateData['share'] = '';
            $updateData['title'] = $updateData['content'] = '邀请好友';
            $updateData['ui'] = $updateData['contentType'] = 0;
            $updateData['flag'] = 998;
            $updateData['gameId'] = C('G_USER.gameid');
        }
        $modRet = $modActive->updateInfo($curId, $updateData);

        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = "数据库更新失败：".$modRet['msg'];
            return $ret;
        }
        if ($curId <= 0)
            $curId = $modRet['data'];
        try {
            if ($activityid > 0) {
                $tmpdata = array('status'=>$active);
            } else {
                $tmpdata = array('activityId'=>$curId, 'status'=>$active, 'createTime'=>date("Y-m-d H:i:s"));
            }
            $modRet = $devMod->updateInfo($activityid, $tmpdata);
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                if ($activityid<=0) {
                    $modRet = $modActive->deleteInfo($curId);
                    if ($modRet['code'] != ERRCODE_SUCCESS) {
                        $ret['code'] = ERRCODE_DB_DELETE_ERR;
                        $ret['msg'] = "数据库删除失败：".$modRet['msg'];
                        return $ret;
                    }
                }
                $ret['code'] = ERRCODE_DB_UPDATE_ERR;
                $ret['msg'] = "数据库更新失败，错误信息：";
                return $ret;
            }
        } catch (\Exception $e) {
            if ($activityid<=0) {
                $modActive->deleteInfo($curId);
            }
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = "数据库更新失败，错误信息：".$e->getMessage();
            return $ret;
        }
        $modTask->deleteInfo($curId);
        $no = 1;
        foreach ($data['reward'] as $v) {
            $v['no'] = $no++;
            $v['activityId'] = $curId;
            $modRet = $modTask->addRewardInfo($v);
            if ($modRet['code'] != ERRCODE_SUCCESS) {
                $ret['code'] = ERRCODE_DB_UPDATE_ERR;
                $ret['msg'] = "数据库更新失败：".$modRet['msg'];
                return $ret;
            }
        }
        if ($ivtId > 0 && $ivtActId > 0) { // 打开或关闭立即更改邀请好友配置
            $active = $active?0:1;
            if ($active || time() >= strtotime($data['starttime'])
                && time() < strtotime($data['endtime'])) {
                $modRet = $modActive->updateInfo($ivtActId, array("status"=>$active));
                if ($modRet['code'] != ERRCODE_SUCCESS) {
                    $ret['code'] = ERRCODE_DB_UPDATE_ERR;
                    $ret['msg'] = "数据库更新失败：".$modRet['msg'];
                    return $ret;
                }
                $modRet = $devMod->updateInfo($ivtId, array("status"=>$active));
                if (ERRCODE_SUCCESS !== $modRet['code']) {
                    $ret['code'] = ERRCODE_DB_UPDATE_ERR;
                    $ret['msg'] = "数据库更新失败：".$modRet['msg'];
                    return $ret;
                }
            }
        }
        $apiSer = new ApiService();
        // 调用服务端接口，刷新缓存
        $svrRet = $apiSer->kaifangApiQuery('/consoleactivity/?act=reload');
        if (ERRCODE_SUCCESS !== $svrRet['code']) {
            $ret['code'] = $svrRet['code'];
            $ret['msg'] = $svrRet['msg'];
            return $ret;
        }
        return $ret;
    }

    /**
     * 取得活动配置的邀请好友配置
     * @author liyao
     */
    public function getActInviteFriend() {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $statusInfo = array('status'=>0, 'activing'=>0,'starttime'=>'','endtime'=>'','list'=>array());
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DEV_DB', 0)) {
            try {
                $devMod = new DevActivityModel();
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
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DICT_DB', 0)) {
            try {
                $modActive = new ActivityModel();
                $modTask = new TaskModel();
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
        $modRet = $devMod->getActivityList();
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $activityList = $modRet['data'];
        $activeId = 0;
        foreach($activityList as $v) {
            $field = '';
            $modRet = $modActive->queryInfoById($v['activityId']);
            if ($modRet['code'] != ERRCODE_SUCCESS) {
                $ret['code'] = ERRCODE_DB_UPDATE_ERR;
                $ret['msg'] = "数据库查询失败：".$modRet['msg'];
                return $ret;
            }
            $info = $modRet['data'];
            if ($info['type'] == 200 && $info['gameId'] == C('G_USER.gameid') && $info['flag'] == 998) {
                //if ($info['status'] == 1 && $v['status'] == 1) {
                $statusInfo['status'] = $info['status'];
                $statusInfo['starttime'] = $info['startTime'];
                $statusInfo['endtime'] = $info['endTime'];
                    $activeId = $v['activityId'];
                    break;
                //}
            }
        }
        $statusInfo['list'] = array();
        if ($activeId > 0) {
            $modRet = $modTask->queryRewardById($activeId);
            if ($modRet['code'] != ERRCODE_SUCCESS) {
                $ret['code'] = ERRCODE_DB_UPDATE_ERR;
                $ret['msg'] = "数据库查询失败：".$modRet['msg'];
                return $ret;
            }
            $no = 1;
            foreach ($modRet['data'] as $v) {
                $arr = array();
                $arr['no'] = $no;
                $arr['event_num'] = $v['eventNum'];
                $tar2 = explode(",", $v["reward"]);//get_object_vars(json_decode($v['reward']));
                $tar = array();
                foreach ($tar2 as $tv) {
                    $vv = explode(":", $tv);
                    $tar[$vv[0]] = $vv[1];
                }
                $idx = 1;
                foreach ($tar as $kk=>$vv) {
                    $arr['reward_type'.$idx] = $kk;
                    $arr['reward_val'.$idx] = $vv;
                    $idx++;
                }
                $statusInfo['list'][] = $arr;
                $no++;
            }
        }
        if ($statusInfo['status'] && time() >= strtotime($statusInfo['starttime'])
            && time() < strtotime($statusInfo['endtime'])) {
            $statusInfo['activing'] = 1;
        }
        $ret['data']['list'] = $statusInfo;
        return $ret;
    }

    /**
     * 判断配置是否生效
     * @author daniel
     */
    public function getValid($key)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => '',
            'data' => []
        );

        if (empty($key) || !is_string($key)) {
            $ret['code'] = ERRCODE_PARAM_INVALID;
            $ret['msg'] = '参数错误';
            return $ret;
        }

        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DICT_DB', 0)) {
            try {
                $dictConfigMod = new DictConfigModel();
            } catch (\Exception $e) {
                $ret['code'] = ERRCODE_DB_SELECT_ERR;
                $ret['msg'] = '数据库连接失败，错误信息:' . $e->getMessage();
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = "数据库配置加载失败，请确认数据库配置信息";
            return $ret;
        }
        $modRet = $dictConfigMod->getValid($key);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $ret['data'] = $modRet['data'];
        return $ret;
    }

    /**
     * 取得红包配置
     */
    public function getRedpack()
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $statusInfo = array('gold'=>'','redeemcodeday'=>'' ,'redeemcodehour'=>'','redeemcodemin'=>'');
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DICT_DB', 0)) {
            try {
                $devConfMod = new DictConfigModel();
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
        $modRet = $devConfMod->getConfig('coin_certificate');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $statusInfo['gold'] = $modRet['data'];
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_ACTIVITY_DB', 0)) {
            try {
                $actModel = new ActConfigModel();
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
        $modRet = $actModel->getConfig('mall_code_expire_time');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $redeemcode = $modRet['data'];
        if ($redeemcode) {
            $statusInfo['redeemcodeday'] = intval($redeemcode/(24*3600));
            $statusInfo['redeemcodehour'] = intval(($redeemcode%(24*3600))/(3600));
            $statusInfo['redeemcodemin'] = intval(($redeemcode%(3600))/60);
        }

        $ret['data']['list'] = $statusInfo;
        return $ret;
    }

    /**
     * 设置元宝掉落配置
     * @author liyao
     */
    public function setRedpack($data)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $modRet = $this->getRedpack();
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = "数据库查詢失败：".$modRet['msg'];
            return $ret;
        }
        $gold = $modRet['data']['list']['gold'];
        $sVal = '';
        $sVal = "2:".$data['rate2']."|3:".$data['rate3'].'|4:100$4:'.$data['low4'].'~'.$data['high4'].'|8:'.$data['low8'].'~'.$data['high8'].'|16:'.$data['low16'].'~'.$data['high16'];
        $td = explode("$", $gold);
        if (count($td) == 2) {
            $td2 = explode("|", $td[1]);
            if (count($td2)>3) {
                for ($ix = 3; $ix < count($td2); $ix++) {
                    $sVal .= '|'.$td2[$ix];
                }
            }
        }
        $ret['data'] = $sVal;
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DICT_DB', 0)) {
            try {
                $devConfMod = new DictConfigModel();
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
        $modRet = $devConfMod->setConfig('coin_certificate', $sVal);
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = "数据库更新失败：".$modRet['msg'];
            return $ret;
        }
        /*$apiSer = new ApiService();
        // 调用服务端接口，刷新缓存
        $svrRet = $apiSer->kaifangApiQuery('/api/sys/?act=setSConfig&sKey=coin_certificate&sValue='.$sVal);
        if (ERRCODE_SUCCESS !== $svrRet['code']) {
            $ret['code'] = $svrRet['code'];
            $ret['msg'] = $svrRet['msg'];
            return $ret;
        }*/
        return $ret;
    }

    /**
     * 设置红包活动日期配置
     * @author liyao
     */
    public function setRedConf($data) {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DICT_DB', 0)) {
            try {
                $devConfMod = new DictConfigModel();
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
        $isValid = $devConfMod-> getValid('coin_certificate');
        if ($isValid['code'] != ERRCODE_SUCCESS) {
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $isValid['msg'];
            return $ret;
        }
        if ($isValid['data'] === 0) {
            $addRet = $devConfMod->setConfig('coin_certificate', $devConfMod->yuanBaoDropRate);
            if ($addRet['code'] != ERRCODE_SUCCESS) {
                $ret['code'] = ERRCODE_DB_UPDATE_ERR;
                $ret['msg'] = $addRet['msg'];
                return $ret;
            }
        }
        if ($data['active'] == 0) {
            $modRet = $devConfMod->setConfig('coin_certificate', "");
        } elseif ($data['active'] == 1) {
            $modRet = $devConfMod->setConfig('coin_certificate', $devConfMod->yuanBaoDropRate);
        }
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = "数据库更新失败：".$modRet['msg'];
            return $ret;
        }

        /*if ($data['active'] == 0) {
            $apiSer = new ApiService();
            // 调用服务端接口，刷新缓存
            $svrRet = $apiSer->kaifangApiQuery('/api/sys/?act=setSConfig&sKey=coin_certificate&sValue=');
            if (ERRCODE_SUCCESS !== $svrRet['code']) {
                $ret['code'] = $svrRet['code'];
                $ret['msg'] = $svrRet['msg'];
                return $ret;
            }
        }
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_ACTIVITY_DB', 0)) {
            try {
                $sVal = $data['redeemcodeday'] * 24 * 3600 + $data['redeemcodehour'] * 3600 + $data['redeemcodemin'] * 60;
                $actModel = new ActConfigModel();
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
        $modRet = $actModel->setConfig('mall_code_expire_time', $sVal, '兑换商城红包码过期时间（秒）');
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = "数据库更新失败：".$modRet['msg'];
            return $ret;
        }*/
        return $ret;
    }

    /**
     * 重置元宝掉落概率
     */
    public function resetRedConf()
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $dbLoadConf = new DbLoadConfigService();
        if (true === $dbLoadConf->load(C('G_USER.gameid'), 'GAME_DICT_DB', 0)) {
            try {
                $dictConfMod = new DictConfigModel();
            } catch (\Exception $e) {
                $ret['code'] = ERRCODE_DB_SELECT_ERR;
                $ret['msg'] = '数据库连接失败，错误信息: ' . $e->getMessage();
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = '数据库配置加载失败，请确认数据库配置信息';
            return $ret;
        }
        $dictConfRet = $dictConfMod->setConfig('coin_certificate', $dictConfMod->yuanBaoDropRate);
        if ($dictConfRet['code'] != ERRCODE_SUCCESS) {
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = "数据库更新失败：".$dictConfRet['msg'];
            return $ret;
        }
        // 默认打开
        $dictStatusConf = $dictConfMod->updateValid('coin_certificate', '1');
        if ($dictStatusConf['code'] != ERRCODE_SUCCESS) {
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = "数据库更新失败：".$dictStatusConf['msg'];
            return $ret;
        }
        return $ret;
    }

    /**
     * 查询游戏内的 玩法列表
     * @author tangjie
     */
    public function getAppGameListLogic($gameId = 0 ){

        $result = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $gameId = $gameId ? $gameId :  C('G_USER.gameid') ;
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load( $gameId , 'GAME_DICT_DB', 0)) {
            try {
                $configModel = new PlaceGameModel();
            } catch (\Exception $e) {
                $result['code'] = ERRCODE_DB_SELECT_ERR;
                $result['msg'] = "数据库连接失败，错误信息：".$e->getMessage();
                return $result;
            }
        } else {
            $result['code'] = ERRCODE_DB_SELECT_ERR;
            $result['msg'] = "数据库配置加载失败，请确认数据库配置信息";
            return $result;
        }
        //查询游戏的配置数据

        try {
            $configData = $configModel->getGameListByGameId($gameId);
        } catch (\Exception $e) {
            $result['code'] = ERRCODE_DB_SELECT_ERR;
            $result['msg']  = "查询数据库失败，错误信息：".$e->getMessage();
        }

        $result['msg'] = "获取成功";
        $result['data'] = $configData;

        return $result ;
    }

    /**
     * 房费查询逻辑
     * @author tangjie
     */
    public function getGameFeedSettingLogic($placeId = 0 ,$gameId = 0)
    {
        $result = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $confSer = new DbLoadConfigService();
        if (true === $confSer->load( C('G_USER.gameid') , 'GAME_DICT_DB', 0)) {
            try {
                $configModel = new PlaceGameModel();
            } catch (\Exception $e) {
                $result['code'] = ERRCODE_DB_SELECT_ERR;
                $result['msg'] = "数据库连接失败，错误信息：".$e->getMessage();
                return $result;
            }
        } else {
            $result['code'] = ERRCODE_DB_SELECT_ERR;
            $result['msg'] = "数据库配置加载失败，请确认数据库配置信息";
            return $result;
        }

        //查询游戏的配置数据
        try {
            $configData = $configModel->getGameConfigByPlaceId($placeId,$gameId);
        } catch (\Exception $e) {
            $result['code'] = ERRCODE_DB_SELECT_ERR;
            $result['msg'] = "查询数据库失败，错误信息：".$e->getMessage();
        }

        //格式化处理数据
        $roomSetting = $this->_formatSettingData($configData['roomFee'] );

        $result['msg'] = "获取成功";
        $result['data'] = $roomSetting;

        return $result ;
    }

    /**
     * 房费更新逻辑
     * @author tangjie
     */
    public function saveGameRoomSettingLogic()
    {
        $result = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $gameId = C('G_USER.gameid') ;
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load( $gameId , 'GAME_DICT_DB', 0)) {
            try {
                $configModel = new PlaceGameModel();
            } catch (\Exception $e) {
                $result['code'] = ERRCODE_DB_SELECT_ERR;
                $result['msg'] = "数据库连接失败，错误信息：".$e->getMessage();
                return $result;
            }
        } else {
            $result['code'] = ERRCODE_DB_SELECT_ERR;
            $result['msg'] = "数据库配置加载失败，请确认数据库配置信息";
            return $result;
        }

        $postdata = I('post.setting');
        $placeid = (int) I('post.placeid');
        $gid = (int) I('post.gid');
        //格式化处理数据
        $roomSetting = $this->_enCodeSettingData( $postdata );

        if($roomSetting === false || empty($postdata) ){
            $result['code'] = ERRCODE_PARAM_INVALID;
            $result['msg'] = "设置的参数错误，请联系管理员";
            return $result;
        }

        //查询游戏的配置数据
        try {
            $result = $configModel->updateGameConfigData($placeid,$roomSetting,$gid);
            $result['ret_param1'] = $roomSetting;
	    $modRet = $configModel->getPlayNameByPlayId($gid);
	    if ($modRet['code'] == ERRCODE_SUCCESS) {
                $result['ret_param2'] = $modRet['data']['gameName'];
	    }
        } catch (\Exception $e) {
            $result['code'] = ERRCODE_DB_SELECT_ERR;
            $result['msg'] = "数据库更新失败，错误信息：".$e->getMessage();
        }

        return $result ;
    }

    /**
     * 房费新增逻辑
     * @author tangjie
     */
    public function addGameRoomSettingLogic()
    {
        $result = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $gameId = C('G_USER.gameid') ;
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load( $gameId , 'GAME_DICT_DB', 0)) {
            try {
                $configModel = new PlaceGameModel();
            } catch (\Exception $e) {
                $result['code'] = ERRCODE_DB_SELECT_ERR;
                $result['msg'] = "数据库连接失败，错误信息：".$e->getMessage();
                return $result;
            }
        } else {
            $result['code'] = ERRCODE_DB_SELECT_ERR;
            $result['msg'] = "数据库配置加载失败，请确认数据库配置信息";
            return $result;
        }

        $placeid = (int) I('post.placeid');
        $gid = (int) I('post.gid');
        try {
            $configData = $configModel->getGameConfigByPlaceId($placeid,$gid);
        } catch (\Exception $e) {
            $result['code'] = ERRCODE_DB_SELECT_ERR;
            $result['msg'] = "查询数据库失败，错误信息：".$e->getMessage();
        }

        //格式化处理数据
        $oldSetting = $this->_formatSettingData($configData['roomFee'] );

        //接受新数据
        $postdata = I('post.setting');

        if($oldSetting){//有旧数据，处理旧数据合并逻辑
            $oldKeys = array_keys($oldSetting);
            $newKey = max( array_keys($postdata) );

            if(array_search($newKey, $oldKeys) !== false){
                $result['code'] = ERRCODE_PARAM_INVALID;
                $result['msg'] = "已存在相同的牌局配置";
                return $result;
            }

            $oldSetting[ $newKey ] = $postdata[$newKey];

            $newSetting = $oldSetting ;

        }else{
            $newSetting = $postdata;
        }

        //格式化处理数据
        $roomSetting = $this->_enCodeSettingData( $newSetting );

        if($roomSetting === false || empty($newSetting) ){
            $result['code'] = ERRCODE_PARAM_INVALID;
            $result['msg'] = "设置的参数错误，请联系管理员";
            return $result;
        }

        //查询游戏的配置数据
        try {
            $result = $configModel->updateGameConfigData($placeid,$roomSetting,$gid);

            $result['ret_param1'] = $roomSetting;
	    $modRet = $configModel->getPlayNameByPlayId($gid);
	    if ($modRet['code'] == ERRCODE_SUCCESS) {
                $result['ret_param2'] = $modRet['data']['gameName'];
	    }
        } catch (\Exception $e) {
            $result['code'] = ERRCODE_DB_SELECT_ERR;
            $result['msg'] = "数据库更新失败，错误信息：".$e->getMessage();
        }

        return $result ;
    }

    /*
     * 游戏主库配置查询
     */
    public function getGameUserConfigLogic()
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $gameId = C('G_USER.gameid') ;

        $confSer = new DbLoadConfigService();
        if (true === $confSer->load( $gameId , 'GAME_DICT_DB', 0)) {
            try {
                $mod = new DictConfigModel();
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

        $initDiamond = $mod->getConfig( 'private_room_diamond' ,true ); //初始化钻石 private_room_diamond
        $initGold = $mod->getConfig( 'private_room_paper'  ,true ); //初始化元宝

        if($initDiamond['code'] != ERRCODE_SUCCESS || $initGold['code'] != ERRCODE_SUCCESS ){
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = "数据库查询失败";
            return $ret;
        }

        $ret['data'] = array(
            'initdiamond' => is_null( $initDiamond['data'])  ? false : intval($initDiamond['data'] ),
            'initgold' =>  is_null(  $initGold['data'] ) ? false : intval( $initGold['data'] )
        );

        return $ret ;
    }

    /**
     * 游戏主库配置查询
     */
    public function setGameUserConfigLogic()
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $gameId = C('G_USER.gameid') ;

        $confSer = new DbLoadConfigService();
        if (true === $confSer->load( $gameId , 'GAME_DICT_DB', 0)) {
            try {
                $mod = new DictConfigModel();
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

        $postdata =  I('post.', '', 'trim');
        //钻石参数
        $diamond = $postdata['initdiamond'] ? intval($postdata['initdiamond']) : false ;
        if($diamond ){
            $ret = $mod->setConfigByApi('private_room_diamond', $diamond);
            if($ret['code'] != ERRCODE_SUCCESS ){
                $ret['code'] = ERRCODE_API_ERR;
                $ret['msg'] = "修改初始化钻石参数错误";
                return $ret;
            }
        }

        //元宝参数
        $gold = $postdata['initgold'] ? intval($postdata['initgold']) : false ;
        if($gold ){
            $ret = $mod->setConfigByApi('private_room_paper', $gold);
            if($ret['code'] != ERRCODE_SUCCESS ){
                $ret['code'] = ERRCODE_API_ERR;
                $ret['msg'] = "修改初始化元宝参数错误";
                return $ret;
            }
        }
        $ret['ret_param1'] = $diamond;
        $ret['ret_param2'] = $gold;

        return $ret ;
    }

    // 组装对应格式的数据，并且JSON
    private function _enCodeSettingData($postdata)
    {
        if( empty($postdata) ){
            return false;
        }
        $postdata = array_filter($postdata);

        foreach($postdata as $index => $item){
            foreach($item as $key => $value){
                $value = array_filter($value);
                foreach ( $value as $k => $v ){
                    $setting[$key][$k][$index] = $v;
                }
            }
        }
        unset($postdata);

        return json_encode($setting );
    }

    // 格式化处理数据
    private function _formatSettingData($data)
    {
        //json编译
        $setting = json_decode( $data );
        if(empty($setting)){
            return '';
        }

        //结构处理
        foreach ($setting as $key => $item) {
            foreach ($item as $index => $value) {
                foreach ($value as $kk => $vv) {
                    $room[$kk][$key][$index] = $vv;
                }
            }
        }
        return $room;
    }

    /************************************ 运营配置 ************************************/

    /**
     * 获取指定游戏的地区树
     * @author Carter
     */
    public function getPlaceTreeByGameId($gameId, $placeId, $hasLocal = false)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $gameMod = new GameModel();
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load($gameId, 'GAME_DICT_DB', 0)) {
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

        $localMap = $gameMod->localMap;

        // 若要获取市包结构
        if ($hasLocal && isset($localMap[$gameId])) {
            $firstId = array_merge(array($gameId), array_keys($localMap[$gameId]));
        } else {
            $firstId = $gameId;
        }
        $field = "placeID,firstID,placeName,placeLevel,parentPlaceID";
        $modRet = $placeMod->queryDsqpPlaceListByFirstId($firstId, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $placeList = array();
        foreach ($modRet['data'] as $v) {
            $placeList[$v['placeLevel']][$v['placeID']] = array(
                'firstID' => $v['firstID'],
                'parentPlaceID' => $v['parentPlaceID'],
                'placeID' => $v['placeID'],
                'placeName' => $v['placeName'],
                'node' => array(),
            );
        }
        krsort($placeList);

        // 通过列表整理地区树
        foreach ($placeList as $lv => $arr) {
            if ($lv > 1) {
                foreach ($arr as $plcId => $v) {
                    if (is_null($placeList[$lv - 1][$v['parentPlaceID']])) {
                        set_exception(__FILE__, __LINE__, "[getPlaceTreeByGameId] get null parent place for {$plcId}");
                        continue;
                    }
                    $placeList[$lv - 1][$v['parentPlaceID']]['node'][] = $placeList[$lv][$plcId];
                }
            }
        }
        $tree = $placeList[1];
        usort($tree, function($a, $b) {
            if ($a['firstID'] == $b['firstID']) {
                return 0;
            }
            return ($a['firstID'] < $b['firstID']) ? -1 : 1;
        });

        // 获取地区名称标题
        $headTitle = '';
        foreach ($tree as $v1) {
            if ($v1['placeID'] == $placeId) {
                $headTitle = $v1['placeName'];
                break;
            }
            foreach ($v1['node'] as $v2) {
                if ($v2['placeID'] == $placeId) {
                    $headTitle = "{$v1['placeName']} - {$v2['placeName']}";
                    break 2;
                }
                foreach ($v2['node'] as $v3) {
                    if ($v3['placeID'] == $placeId) {
                        $headTitle = "{$v1['placeName']} - {$v2['placeName']} - {$v3['placeName']}";
                        break 3;
                    }
                }
            }
        }

        $ret['data'] = array(
            'tree' => $tree,
            'pageHead' => $headTitle,
        );
        return $ret;
    }

    /**
     * 获取分享配置的地区状态map，用于区分哪些地区存在配置项，哪些无配置项
     * @author Carter
     */
    public function getGConfShareContValidPlace($gameId, $shareSource)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $gameMod = new GameModel();
        $confMod = new GameShareModel();

        $localMap = $gameMod->localMap[$gameId];
        $gameIdArr = array_merge(array($gameId), array_keys($localMap));

        $attr = array(
            'gameId' => $gameIdArr,
            'source' => $shareSource,
        );
        $modRet = $confMod->queryGameShareByAttr($attr, 'place_id');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $ret['data'] = array_values(array_unique(array_column($modRet['data'], 'place_id')));

        return $ret;
    }

    /************************************ 运营配置 - 朋友圈-分享配置 ************************************/

    /**
     * 根据域名生成二维码，与背景图贴合后上传至资源服
     * @author Carter
     */
    private function _createQrcodeBgImage($gameId, $confId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        vendor('phpqrcode.phpqrcode');

        // 资源服地址
        $resSvr = C('RESOURCE_API_IPHOST').":".C('RESOURCE_API_PORT').'/';

        $qrObj = new \QRcode();
        $apiSer = new ApiService();
        $confMod = new GameShareModel();
        $domainMod = new GameShareDomainModel();

        $modRet = $confMod->queryGameShareById($confId, 'game_id,share_type,image,address,qrcode_x,qrcode_y');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $conf = $modRet['data'];

        if ($confMod::SHARE_TYPE_SYS != $conf['share_type']) {
            set_exception(__FILE__, __LINE__, "[_createQrcodeBgImage] {$confId} share type err");
            $ret['code'] = ERRCODE_SYSTEM;
            $ret['msg'] = '只有系统分享方式可以合成二维码背景图';
            return $ret;
        }

        // 域名数组
        $addrArr = array();
        if (empty($conf['address']) || '%domain%' == $conf['address']) {
            // 从动态域名获取域名列表
            $queryAttr = array(
                'game_id' => $gameId, // 这里不要用配置里面的game_id，其有可能是市包游戏id
                'status' => $domainMod::STATUS_NORMAL,
            );
            $modRet = $domainMod->queryGameShareDomainListByAttr($queryAttr, 'id,link');
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $ret['code'] = $modRet['code'];
                $ret['msg'] = $modRet['msg'];
                return $ret;
            }
            if (empty($modRet['data'])) {
                $ret['code'] = ERRCODE_SYSTEM;
                $ret['msg'] = '二维码生成失败，没有配置任何动态域名';
                return $ret;
            }
            foreach ($modRet['data'] as $v) {
                $addrArr[$v['id']] = array('file' => '', 'link' => $v['link']);
            }
        } else {
            $addrArr[0] = array('file' => '', 'link' => $conf['address']);
        }

        // 生成二维码图
        $qrImageArr = array();
        // 容错级别
        $errorCorrectionLevel = 3;
        // 生成图片大小
        $matrixPointSize = 4;
        foreach ($addrArr as $linkId => $v) {
            // 定义二维码图片生成后的文件路径
            $qrFilename = ROOT_PATH."FileUpload/ShareImg/qr_{$confId}_{$linkId}.png";
            // 生成二维码png文件
            $qrObj->png($v['link'], $qrFilename, $errorCorrectionLevel, $matrixPointSize, 2);

            // 判断二维码图片是否创建成功
            if (!file_exists($qrFilename)) {
                $ret['code'] = ERRCODE_SYSTEM;
                $ret['msg'] = "域名{$v['link']}生成二维码失败";
                return $ret;
            }
            $addrArr[$linkId]['file'] = $qrFilename;
        }

        // 将二维码与背景图合并后在本地保存
        $qrBgImgArr = array();
        foreach ($addrArr as $linkId => $v) {
            // 创建背景图GD图像
            $bgGdImg = imagecreatefromjpeg($resSvr.$conf['image']);
            if (false === $bgGdImg) {
                set_exception(__FILE__, __LINE__, "[_createQrcodeBgImage]imagecreatefromjpeg {$resSvr}{$conf['image']} failed");
                continue;
            }
            $bgImgW = imagesx($bgGdImg);
            $bgImgH = imagesy($bgGdImg);

            // 二维码图
            $qrGdImg = imagecreatefrompng($v['file']);
            if (false === $qrGdImg) {
                set_exception(__FILE__, __LINE__, "[_createQrcodeBgImage]imagecreatefrompng {$v['file']} failed");
                continue;
            }
            $srcX = 0;
            $srcY = 0;
            $srcW = imagesx($qrGdImg);
            $srcH = imagesy($qrGdImg);

            $dstX = intval(($bgImgW - $srcW) * $conf['qrcode_x'] / 1000);
            $dstY = intval(($bgImgH - $srcH) * $conf['qrcode_y'] / 1000);

            if (true !== imagecopymerge($bgGdImg, $qrGdImg, $dstX, $dstY, $srcX, $srcY, $srcW, $srcH, 100)) {
                set_exception(__FILE__, __LINE__, "[_createQrcodeBgImage] {$conf['image']} and {$v['file']} imagecopymerge failed");
                continue;
            }

            $finishFileName = ROOT_PATH."FileUpload/ShareImg/qrbg_{$confId}_{$linkId}.jpg";
            if (true !== imagejpeg($bgGdImg, $finishFileName)) {
                set_exception(__FILE__, __LINE__, "[_createQrcodeBgImage] {$finishFileName} imagejpeg failed");
                continue;
            }
            $qrBgImgArr[$linkId] = $finishFileName;

            // 释放资源
            imagedestroy($bgGdImg);
            imagedestroy($qrGdImg);
        }

        // 校验位
        $checkBit = substr(md5($conf['image'].$conf['address'].$conf['qrcode_x'].$conf['qrcode_y']), 0, 4);

        // 将合成图上传到资源服
        foreach ($qrBgImgArr as $linkId => $v) {
            $imgSource = ROOT_PATH."FileUpload/ShareImg/qrbg_{$confId}_{$linkId}.jpg";
            $svrPath = "Admin/GameShare/QrBgImg/{$confId}/qrbg_{$confId}_{$linkId}_{$checkBit}.jpg";
            $svrRet = $apiSer->resourceServerUploadImg($svrPath, $imgSource);
            if (ERRCODE_SUCCESS !== $svrRet['code']) {
                $ret['code'] = $svrRet['code'];
                $ret['msg'] = $svrRet['msg'];
                return $ret;
            }
        }
        $ret['data'] = $qrBgImgArr;

        return $ret;
    }

    /**
     * 将资源服上指定配置的二维码背景合成图删除
     * @author Carter
     */
    private function _removeQrcodeBgImage($confId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $apiSer = new ApiService();

        $svrRet = $apiSer->resourceServerRmImgDir("Admin/GameShare/QrBgImg/{$confId}");
        if (ERRCODE_SUCCESS !== $svrRet['code']) {
            $ret['code'] = $svrRet['code'];
            $ret['msg'] = $svrRet['msg'];
            return $ret;
        }

        return $ret;
    }

    /**
     * 粘贴朋友圈-分享配置
     * @author Carter
     */
    public function pasteGameShareConf($gameId, $copyId, $placeId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $resServer = C('RESOURCE_SERVER_IPHOST').':'.C('RESOURCE_SERVER_PORT');
        $fileDir = ROOT_PATH."FileUpload";

        $confMod = new GameShareModel();
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load($gameId, 'GAME_DICT_DB', 0)) {
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

        $shareSource = array(
            $confMod::SOURCE_HALL_NOAWARD,
            $confMod::SOURCE_HALL_AWARD,
            $confMod::SOURCE_DIAMOND,
            $confMod::SOURCE_CLUB,
        );

        // 地区之间能否粘贴，取决于后端地区字典表是否在相同产品id内
        $modRet = $placeMod->queryDsqpPlaceByPlaceId(array($copyId, $placeId), 'placeID,firstID');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        if (2 != count($modRet['data'])) {
            $ret['code'] = ERRCODE_DATA_ERR;
            $ret['msg'] = "地区信息缺失";
            return $ret;
        }
        foreach ($modRet['data'] as $v) {
            if ($v['firstID'] != $gameId) {
                $ret['code'] = ERRCODE_DATA_ERR;
                $ret['msg'] = "{$v['placeID']}不在游戏地区范围内，不能进行粘贴操作";
                return $ret;
            }
        }

        // 若覆盖前已有配置，删掉覆盖前的配置
        $attr = array(
            'gameId' => $gameId,
            'place_id' => $placeId,
            'source' => $shareSource,
        );
        $modRet = $confMod->queryGameShareByAttr($attr, 'id');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        if (!empty($modRet['data'])) {
            $lgcRet = $this->removeGameShareConf($gameId, $placeId);
            if (ERRCODE_SUCCESS !== $lgcRet['code']) {
                $ret['code'] = $lgcRet['code'];
                $ret['msg'] = $lgcRet['msg'];
                return $ret;
            }
            $ret['data']['delConfId'] = $lgcRet['data'];
        }

        // 获取需要粘贴的内容
        $attr = array(
            'gameId' => $gameId,
            'place_id' => $copyId,
            'source' => $shareSource,
        );
        $field = 'source,share_type,title,desc,image,address,qrcode_x,qrcode_y';
        $modRet = $confMod->queryGameShareByAttr($attr, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        foreach ($modRet['data'] as $v) {
            // 资源服上图片要重新复制一张，共用图片的话，删除配置的时候容易起冲突，不好管理
            $savename = uniqid().".".array_pop(explode('.', basename($v['image'])));
            $source = "{$resServer}/{$v['image']}";
            $dest = "{$fileDir}/ShareImg/{$savename}";
            if (!copy($source, $dest)) {
                set_exception(__FILE__, __LINE__, "[pasteGameShareConf] copy failed: {$v['image']}");
                continue;
            }

            $addAttr = array(
                'first_id' => $gameId,
                'place_id' => $placeId,
                'source' => $v['source'],
                'savename' => $savename,
                'share_type' => $v['share_type'],
            );
            // 动态分享
            if ($confMod::SHARE_TYPE_DYMC == $v['share_type']) {
                $addAttr['title'] = $v['title'];
                $addAttr['desc'] = $v['desc'];
            }
            // 系统分享
            else if ($confMod::SHARE_TYPE_SYS == $v['share_type']) {
                $addAttr['cont'] = $v['desc'];
                $addAttr['address'] = $v['address'];
                $addAttr['qrcode_x'] = $v['qrcode_x'] / 1000;
                $addAttr['qrcode_y'] = $v['qrcode_y'] / 1000;
            }

            // 将粘贴的内容复制进库
            $lgcRet = $this->addGameShareConf($gameId, $addAttr);
            if (ERRCODE_SUCCESS !== $lgcRet['code']) {
                $ret['code'] = $lgcRet['code'];
                $ret['msg'] = $lgcRet['msg'];
                return $ret;
            }
            $ret['data']['add'][] = $lgcRet['data'];
        }

        return $ret;
    }

    /**
     * 添加朋友圈-分享配置
     * @author Carter
     */
    public function addGameShareConf($gameId, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $confMod = new GameShareModel();

        // 验证参数中游戏id是否有效
        if ($gameId != $attr['first_id']) {
            $ret['code'] = ERRCODE_PARAM_INVALID;
            $ret['msg'] = "无效的游戏id{$attr['first_id']}";
            return $ret;
        }

        $modRet = $confMod->insertGameShareConf($attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $ret['data'] = $cInfo = $modRet['data'];

        // 系统分享方式，需要将二维码贴到背景图上
        if ($confMod::SHARE_TYPE_SYS == $cInfo['share_type']) {
            $funcRet = $this->_createQrcodeBgImage($gameId, $cInfo['id']);
            if (ERRCODE_SUCCESS !== $funcRet['code']) {
                $ret['code'] = $funcRet['code'];
                $ret['msg'] = $funcRet['msg'];
                return $ret;
            }
            $ret['data']['qrbg'] = $funcRet['data'];
        }

        return $ret;
    }

    /**
     * 修改朋友圈-分享配置
     * @author Carter
     */
    public function editGameShareConf($gameId, $confId, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $confMod = new GameShareModel();

        $modRet = $confMod->updateGameShareConf($confId, $attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $ret['data']['upData'] = $upData = $modRet['data'];

        // 分享方式有改变，从系统分享改为了动态分享，直接删掉所有二维码背景合成图
        if (isset($upData['share_type']) && $confMod::SHARE_TYPE_DYMC == $upData['share_type']) {
            $funcRet = $this->_removeQrcodeBgImage($confId);
            if (ERRCODE_SUCCESS !== $funcRet['code']) {
                $ret['code'] = $funcRet['code'];
                $ret['msg'] = $funcRet['msg'];
                return $ret;
            }
        }
        // 分享方式有改变，从动态分享改为了系统分享，需要重新合成二维码背景图
        else if (isset($upData['share_type']) && $confMod::SHARE_TYPE_SYS == $upData['share_type']) {
            $funcRet = $this->_createQrcodeBgImage($gameId, $confId);
            if (ERRCODE_SUCCESS !== $funcRet['code']) {
                $ret['code'] = $funcRet['code'];
                $ret['msg'] = $funcRet['msg'];
                return $ret;
            }
            $ret['data']['qrbg'] = $funcRet['data'];
        }
        // 分享方式无改变，依然为系统分享，只要背景图或二维码地址有修改，都要删掉所有旧的合成图再重新合成
        else if ($confMod::SHARE_TYPE_SYS == $attr['share_type']) {
            if (isset($upData['image']) || isset($upData['address']) || isset($upData['qrcode_x']) || isset($upData['qrcode_y'])) {
                // 删旧图
                $funcRet = $this->_removeQrcodeBgImage($confId);
                if (ERRCODE_SUCCESS !== $funcRet['code']) {
                    $ret['code'] = $funcRet['code'];
                    $ret['msg'] = $funcRet['msg'];
                    return $ret;
                }
                // 合新图
                $funcRet = $this->_createQrcodeBgImage($gameId, $confId);
                if (ERRCODE_SUCCESS !== $funcRet['code']) {
                    $ret['code'] = $funcRet['code'];
                    $ret['msg'] = $funcRet['msg'];
                    return $ret;
                }
                $ret['data']['qrbg'] = $funcRet['data'];
            }
        }

        return $ret;
    }

    /**
     * 删除朋友圈-分享配置
     * @author Carter
     */
    public function removeGameShareConf($gameId, $placeId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $confMod = new GameShareModel();

        if ($gameId == $placeId) {
            $ret['code'] = ERRCODE_DATA_ERR;
            $ret['msg'] = "省包配置是缺省配置，不能进行删除操作";
            return $ret;
        }

        $shareSource = array(
            $confMod::SOURCE_HALL_NOAWARD,
            $confMod::SOURCE_HALL_AWARD,
            $confMod::SOURCE_DIAMOND,
            $confMod::SOURCE_CLUB,
        );

        $modRet = $confMod->deleteGameShareByPlaceId($gameId, $placeId, $shareSource);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $sysTypeId = $modRet['data']['sysTypeId'];
        $ret['data']['delId'] = $modRet['data']['delId'];

        // 如果配置中有系统配置，那就删除资源服上的二维码背景图
        if (!empty($sysTypeId)) {
            foreach ($sysTypeId as $v) {
                $funcRet = $this->_removeQrcodeBgImage($v);
                if (ERRCODE_SUCCESS !== $funcRet['code']) {
                    $ret['code'] = $funcRet['code'];
                    $ret['msg'] = $funcRet['msg'];
                    return $ret;
                }
            }
        }

        return $ret;
    }

    /**
     * 更新分享配置文件，根据游戏进行更新，一个包名一个配置文件
     * @author Carter
     */
    public function refrashShareConfFile($gameId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $apiSvr = new ApiService();
        $gameMod = new GameModel();
        $shareMod = new GameShareModel();
        $appidMod = new GameShareAppidModel();
        $domainMod = new GameShareDomainModel();
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load($gameId, 'GAME_DICT_DB', 0)) {
            try {
                $placeMod = new DictPlaceModel();
            } catch (\Exception $e) {
                $ret['code'] = ERRCODE_DB_SELECT_ERR;
                $ret['msg'] = "数据库连接失败，错误信息：".$e->getMessage().", GAME_DICT_DB ".var_export(C('GAME_DICT_DB'), true);
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = "数据库配置加载失败，请确认数据库配置信息";
            return $ret;
        }

        $resSvrAddr = C('RESOURCE_SERVER_IPHOST').':'.C('RESOURCE_SERVER_PORT');

        $localMap = $gameMod->localMap;
        if (isset($localMap[$gameId])) {
            $gameIdArr = array_merge(array($gameId), array_keys($localMap[$gameId]));
        } else {
            $gameIdArr = $gameId;
        }

        // 获取当前游戏包名
        $field = 'ios_package_name,android_package_name,game_status';
        $modRet = $gameMod->queryGameInfoById($gameId, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $gameInfo = $modRet['data'];
        if ($gameMod::GAME_STATUS_ON != $gameInfo['game_status']) {
            $ret['code'] = ERRCODE_DATA_ERR;
            $ret['msg'] = "游戏 {$gameId} 状态异常";
            return $ret;
        }

        // 包名 map，包括热更包的
        $packageMap = array();
        $packageMap[$gameInfo['ios_package_name']] = array(
            'gameId' => $gameId,
            'firstId' => $gameId,
        );
        $packageMap[$gameInfo['android_package_name']] = array(
            'gameId' => $gameId,
            'firstId' => $gameId,
        );
        if (isset($localMap[$gameId])) {
            foreach ($localMap[$gameId] as $i => $n) {
                $packageMap[$n['pkName']] = array(
                    'gameId' => $gameId,
                    'firstId' => $i,
                );
            }
        }

        // 地区父子级 map
        $field = "placeID,placeLevel,parentPlaceID";
        $modRet = $placeMod->queryDsqpPlaceListByFirstId($gameIdArr, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $placeParentMap = array();
        foreach ($modRet['data'] as $p) {
            if ($p['placeLevel'] == 1) {
                $placeParentMap[$p['placeID']] = '0';
            } else {
                $placeParentMap[$p['placeID']] = $p['parentPlaceID'];
            }
        }

        // 朋友圈-分享配置
        $field = 'id,place_id,play_id,source,share_type,title,desc,image,address,qrcode_x,qrcode_y';
        $modRet = $shareMod->queryGameShareByAttr(array('gameId' => $gameId), $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $confMap = array();
        foreach ($modRet['data'] as $v) {
            // 不同分享功能取对应的 key
            switch ($v['source']) {
                case $shareMod::SOURCE_HALL_NOAWARD: // 大厅分享（无奖励，竖版独有）朋友圈
                case $shareMod::SOURCE_HALL_AWARD: // 大厅分享（有奖励）朋友圈
                case $shareMod::SOURCE_DIAMOND: // 领取钻石-朋友圈分享
                case $shareMod::SOURCE_CLUB: // 俱乐部-朋友圈分享
                case $shareMod::SOURCE_FRIEND_NOAWARD: // 大厅分享给好友（无奖励）
                case $shareMod::SOURCE_FRIEND_AWARD: // 领取钻石-分享给好友
                case $shareMod::SOURCE_FRIEND_CLUB: // 俱乐部分享给好友
                    if ($gameId == $v['place_id']) {
                        $srcKey = 0;
                    } else {
                        $srcKey = $v['place_id'];
                    }
                    break;
                case $shareMod::SOURCE_ROOM: // 房间等待界面-邀请好友（区分玩法）
                    $srcKey = $v['play_id'];
                    break;
            }

            // 动态链接分享
            if ($shareMod::SHARE_TYPE_DYMC == $v['share_type']) {
                if (empty($v['image'])) {
                    $thumbimg = '';
                } else {
                    $thumbimg = $resSvrAddr.'/'.$v['image'];
                }
                $confMap[$v['source']][$srcKey] = array(
                    'share_type' => $v['share_type'],
                    'title' => $v['title'],
                    'thumbimg' => $thumbimg,
                    'desc' => $v['desc'],
                );
            }
            // 系统分享图片
            else if ($shareMod::SHARE_TYPE_SYS == $v['share_type']) {
                $confMap[$v['source']][$srcKey] = array(
                    'share_type' => $v['share_type'],
                    'conf_id' => $v['id'],
                    'address' => $v['address'],
                    'desc' => $v['desc'],
                    'resource_svr' => $resSvrAddr.'/Admin/GameShare/QrBgImg',
                    'check_bit' => substr(md5($v['image'].$v['address'].$v['qrcode_x'].$v['qrcode_y']), 0, 4),
                );
                // 房间分享的系统分享需要配置标题
                if ($shareMod::SOURCE_ROOM == $v['source']) {
                    $confMap[$v['source']][$srcKey]['title'] = $v['title'];
                }
            }
        }

        // appid 配置（包括省包、热更包）
        $field = 'id,game_id,appid';
        $attr = array(
            'game_id' => $gameIdArr,
            'is_blockade' => $appidMod::IS_BLOCKADE_FALSE,
            'status' => $appidMod::STATUS_NORMAL,
        );
        $modRet = $appidMod->queryGameShareAppidListByAttr($attr, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $appidMap = array();
        foreach ($modRet['data'] as $v) {
            $appidMap[$v['game_id']][] = array(
                'id' => $v['id'],
                'appid' => $v['appid'],
            );
        }

        // 域名配置
        $field = 'id,link';
        $attr = array(
            'game_id' => $gameId,
            'is_blockade' => $domainMod::IS_BLOCKADE_FALSE,
            'status' => $domainMod::STATUS_NORMAL,
        );
        $modRet = $domainMod->queryGameShareDomainListByAttr($attr, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $domainMap = array();
        foreach ($modRet['data'] as $v) {
            $domainMap[] = $v;
        }

        // 分包名单独生成配置文件并推送到分享服
        foreach ($packageMap as $pkgName => $v) {
            $fileCont = array(
                'gameId' => $v['gameId'],
                'firstId' => $v['firstId'],
                'parent' => $placeParentMap,
                'conf' => $confMap,
                'appid' => $appidMap,
                'domain' => $domainMap,
            );

            $filename = ROOT_PATH."FileUpload/conf_{$pkgName}.json";
            if (false === file_put_contents($filename, json_encode($fileCont))) {
                $ret['code'] = ERRCODE_SYSTEM;
                $ret['msg'] = "写入配置文件{$filename}失败";
                return $ret;
            }

            $svrRet = $apiSvr->shareServerSendConfFile($filename);
            if (ERRCODE_SUCCESS !== $svrRet['code']) {
                $ret['code'] = $svrRet['code'];
                $ret['msg'] = $svrRet['msg'];
                return $ret;
            }
        }

        return $ret;
    }

    /************************************ 运营配置 - 好友/群-分享配置 ************************************/

    /**
     * 获取好友/群-分享配置的地区状态map，用于区分哪些地区存在配置项，哪些无配置项
     * @author Carter
     */
    public function getFriendShareContValidPlace($gameId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $gameMod = new GameModel();
        $confMod = new GameShareModel();

        $localMap = $gameMod->localMap[$gameId];
        $gameIdArr = array_merge(array($gameId), array_keys($localMap));

        $attr = array(
            'gameId' => $gameIdArr,
            'source' => array(
                $confMod::SOURCE_FRIEND_NOAWARD,
                $confMod::SOURCE_FRIEND_AWARD,
            ),
        );
        $modRet = $confMod->queryGameShareByAttr($attr, 'place_id');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $ret['data'] = array_values(array_unique(array_column($modRet['data'], 'place_id')));

        return $ret;
    }

    /**
     * 粘贴好友/群-分享配置
     * @author Carter
     */
    public function pasteFriendShareConf($gameId, $copyId, $placeId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $confMod = new GameShareModel();
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load($gameId, 'GAME_DICT_DB', 0)) {
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

        $shareSource = array(
            $confMod::SOURCE_FRIEND_NOAWARD,
            $confMod::SOURCE_FRIEND_AWARD,
            $confMod::SOURCE_FRIEND_CLUB,
        );

        // 地区之间能否粘贴，取决于后端地区字典表是否在相同产品id内
        $modRet = $placeMod->queryDsqpPlaceByPlaceId(array($copyId, $placeId), 'placeID,firstID');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        if (2 != count($modRet['data'])) {
            $ret['code'] = ERRCODE_DATA_ERR;
            $ret['msg'] = "地区信息缺失";
            return $ret;
        }
        foreach ($modRet['data'] as $v) {
            if ($v['firstID'] != $gameId) {
                $ret['code'] = ERRCODE_DATA_ERR;
                $ret['msg'] = "{$v['placeID']}不在游戏地区范围内，不能进行粘贴操作";
                return $ret;
            }
        }

        // 若覆盖前已有配置，删掉覆盖前的配置
        $attr = array(
            'gameId' => $gameId,
            'place_id' => $placeId,
            'source' => $shareSource,
        );
        $modRet = $confMod->queryGameShareByAttr($attr, 'id');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        if (!empty($modRet['data'])) {
            $lgcRet = $this->removeFriendShareConf($gameId, $placeId);
            if (ERRCODE_SUCCESS !== $lgcRet['code']) {
                $ret['code'] = $lgcRet['code'];
                $ret['msg'] = $lgcRet['msg'];
                return $ret;
            }
            $ret['data']['delConfId'] = $lgcRet['data'];
        }

        // 获取需要粘贴的内容
        $attr = array(
            'gameId' => $gameId,
            'place_id' => $copyId,
            'source' => $shareSource,
        );
        $field = 'source,title,desc';
        $modRet = $confMod->queryGameShareByAttr($attr, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        foreach ($modRet['data'] as $v) {
            $addAttr = array(
                'first_id' => $gameId,
                'place_id' => $placeId,
                'source' => $v['source'],
                'title' => $v['title'],
                'desc' => $v['desc'],
                'savename' => '',
            );

            // 将粘贴的内容复制进库
            $lgcRet = $this->addFriendShareConf($gameId, $addAttr);
            if (ERRCODE_SUCCESS !== $lgcRet['code']) {
                $ret['code'] = $lgcRet['code'];
                $ret['msg'] = $lgcRet['msg'];
                return $ret;
            }
            $ret['data']['add'][] = $lgcRet['data'];
        }

        return $ret;
    }

    /**
     * 好友/群-分享添加配置
     * @author Carter
     */
    public function addFriendShareConf($gameId, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $confMod = new GameShareModel();

        // 验证参数中游戏id是否有效
        if ($gameId != $attr['first_id']) {
            $ret['code'] = ERRCODE_PARAM_INVALID;
            $ret['msg'] = "无效的游戏id{$attr['first_id']}";
            return $ret;
        }

        $attr['share_type'] = $confMod::SHARE_TYPE_DYMC;
        $modRet = $confMod->insertGameShareConf($attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $ret['data'] = $cInfo = $modRet['data'];

        return $ret;
    }

    /**
     * 好友/群-分享配置修改
     * @author Carter
     */
    public function editFriendShareConf($gameId, $confId, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $confMod = new GameShareModel();

        $attr['share_type'] = $confMod::SHARE_TYPE_DYMC;
        $modRet = $confMod->updateGameShareConf($confId, $attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $ret['data']['upData'] = $upData = $modRet['data'];

        return $ret;
    }

    /**
     * 删除好友/群-分享配置
     * @author Carter
     */
    public function removeFriendShareConf($gameId, $placeId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $confMod = new GameShareModel();

        if ($gameId == $placeId) {
            $ret['code'] = ERRCODE_DATA_ERR;
            $ret['msg'] = "省包配置是缺省配置，不能进行删除操作";
            return $ret;
        }

        $shareSource = array(
            $confMod::SOURCE_FRIEND_NOAWARD,
            $confMod::SOURCE_FRIEND_AWARD,
            $confMod::SOURCE_FRIEND_CLUB,
        );

        $modRet = $confMod->deleteGameShareByPlaceId($gameId, $placeId, $shareSource);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $ret['data']['delId'] = $modRet['data']['delId'];

        return $ret;
    }
    /************************************ 运营配置 - 绑定手机奖励配置 ************************************/
    /**
     * 获取绑定手机奖励配置
     * @param $gameId int 游戏Id
     * @return array
     * @author daniel
     */
    public function getBindPhoneConf($gameId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => '',
            'data' => array(),
        );
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load($gameId, 'GAME_DEV_DB', 0)) {
            try {
                $sQuestMod = new SQuestModel();
            } catch (\Exception $e) {
                $ret['code'] = ERRCODE_DB_SELECT_ERR;
                $ret['msg'] = '数据库连接失败,错误信息: ' . $e->getMessage();
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = '数据库配置加载失败，请确认数据库配置信息';
            return $ret;
        }

        $modRet = $sQuestMod->getBindOption();
        if ($modRet['code'] !== ERRCODE_SUCCESS) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }

        $ret['data']['option'] = $modRet['data']['option'];
        $ret['data']['value'] = $modRet['data']['value'];

        return $ret;
    }

    /**
     * 修改绑定手机奖励配置
     * @param $gameId
     * @param $data
     * @return array
     * @author daniel
     */
    public function saveBindPhoneConf($gameId, $data)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => '',
            'data' => array(),
        );
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load($gameId, 'GAME_DEV_DB', 0)) {
            try {
                $sQuestMod = new SQuestModel();
            } catch (\Exception $e) {
                $ret['code'] = ERRCODE_DB_SELECT_ERR;
                $ret['msg'] = '数据库连接失败,错误信息: ' . $e->getMessage();
                return $ret;
            }
        } else {
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = '数据库配置加载失败，请确认数据库配置信息';
            return $ret;
        }

        $modRet = $sQuestMod->saveBindOption($data);
        if ($modRet['code'] !== ERRCODE_SUCCESS) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }

        $apiSer = new ApiService();
        $svrRet = $apiSer->dsSvrApiGetQuery($gameId, '/console/?act=reload');
        if (ERRCODE_SUCCESS !== $svrRet['code']) {
            $ret['code'] = $svrRet['code'];
            $ret['msg'] = $svrRet['msg'];
            return $ret;
        }

        $ret['data']['option'] = $modRet['data']['option'];
        $ret['data']['value'] = $modRet['data']['value'];

        return $ret;
    }

    /************************************ 游戏配置 ************************************/

    /**
     * 获取指定游戏的玩法列表
     * @author Carter
     */
    public function getDictPlayMapByGameId($gameId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $confSer = new DbLoadConfigService();
        if (true === $confSer->load($gameId, 'GAME_DICT_DB', 0)) {
            try {
                $gameMod = new DictPlaceGameModel();
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

        $modRet = $placeMod->queryDsqpPlaceListByFirstId($gameId, 'placeID');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $placeArr = array_column($modRet['data'], 'placeID');

        $modRet = $gameMod->queryDsqpPlaceGameByPlaceId($placeArr, 'gameId,gameName,placeID');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }

        $playMap = array();
        foreach ($modRet['data'] as $v) {
            $playMap[$v['gameId']] = array(
                'playName' => $v['gameName'],
                'placeId' => $v['placeID'],
            );
        }
        $ret['data'] = $playMap;

        return $ret;
    }

    /**
     * 获取指定玩法房间配置信息
     * @author Carter
     */
    public function getPlayConfInfoById($gameId, $placeId, $playId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $shareMod = new GameShareModel();
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load($gameId, 'GAME_DICT_DB', 0)) {
            try {
                $gameMod = new DictPlaceGameModel();
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

        // 获取初始分数、解散时间
        $field = 'initScore,expiredTime';
        $modRet = $gameMod->queryDsqpPlaceGameByPlacePlayId($placeId, $playId, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $gameInfo = $modRet['data'];

        // 获取分享配置
        $field = 'share_type,title,desc,image,address,qrcode_x,qrcode_y';
        $attr = array(
            'gameId' => $gameId,
            'play_id' => $playId,
            'source' => $shareMod::SOURCE_ROOM,
        );
        $modRet = $shareMod->queryGameShareByAttr($attr, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        if (empty($modRet['data'])) {
            $shareInfo = array();
        } else {
            $shareInfo = current($modRet['data']);
        }

        $ret['data'] = array(
            'game' => $gameInfo,
            'share' => $shareInfo,
        );

        return $ret;
    }

    /**
     * 修改房间配置
     * @author Carter
     */
    public function saveGameRoomConf($gameId, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $apiSer = new ApiService();
        $shareMod = new GameShareModel();
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load($gameId, 'GAME_DICT_DB', 0)) {
            try {
                $gameMod = new DictPlaceGameModel();
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

        // 先更新游戏字典库的房间配置
        $modRet = $gameMod->updateDsqpPlaceGameByPlacePlayId($attr['place_id'], $attr['play_id'], $attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        // 存在修改项，那么要重载游戏缓存
        if (!empty($modRet['data'])) {
            $ret['data']['gameUpdate'] = $modRet['data'];

            $svrRet = $apiSer->dsSvrApiGetQuery($gameId, '/console/?act=reload');
            if (ERRCODE_SUCCESS !== $svrRet['code']) {
                $ret['code'] = $svrRet['code'];
                $ret['msg'] = $svrRet['msg'];
                return $ret;
            }
            $apiRet = json_decode($svrRet['data'], true);
            if (0 !== $apiRet['result']) {
                $ret['code'] = ERRCODE_API_ERR;
                $ret['msg'] = $apiRet['desc'] ? : '调取接口失败，错误信息：'.var_export($apiRet, true);
                return $ret;
            }
        }

        // 再更新朋友圈-分享配置，先获取分享配置，如果无配置需要插入数据，若有配置则进行修改操作
        $shareAttr = array(
            'gameId' => $gameId,
            'play_id' => $attr['play_id'],
            'source' => $shareMod::SOURCE_ROOM,
        );
        $modRet = $shareMod->queryGameShareByAttr($shareAttr, 'id');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $shareInfo = current($modRet['data']);

        // 如果分享配置有插入或更新，将刷文件标志置1，就会进行配置文件刷新
        $updateConfFileFlag = 0;

        // 无分享配置，插入新配置
        if (false == $shareInfo) {
            // 对缺失的参数赋值后，才可以成功插入数据
            $attr['first_id'] = $gameId;
            $attr['share_type'] = $shareMod::SHARE_TYPE_DYMC;
            $attr['source'] = $shareMod::SOURCE_ROOM;

            $modRet = $shareMod->insertGameShareConf($attr);
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $ret['code'] = $modRet['code'];
                $ret['msg'] = $modRet['msg'];
                return $ret;
            }

            $updateConfFileFlag = 1;
            $ret['data']['shareInsert'] = $modRet['data'];
        }
        // 已存在配置，仅作更新操作
        else {
            $attr['place_id'] = 0;
            $attr['source'] = $shareMod::SOURCE_ROOM;
            $modRet = $shareMod->updateGameShareConf($shareInfo['id'], $attr);
            if (ERRCODE_SUCCESS !== $modRet['code'] && ERRCODE_UPDATE_NONE !== $modRet['code']) {
                $ret['code'] = $modRet['code'];
                $ret['msg'] = $modRet['msg'];
                return $ret;
            }
            if (!empty($modRet['data'])) {
                $updateConfFileFlag = 1;
                $ret['data']['shareUpdate'] = $upData = $modRet['data'];
            }
        }

        // 需要刷新配置文件，调用文件刷新推送接口
        if ($updateConfFileFlag) {
            $lgcRet = $this->refrashShareConfFile($gameId);
            if (ERRCODE_SUCCESS !== $lgcRet['code']) {
                $ret['code'] = $lgcRet['code'];
                $ret['msg'] = $lgcRet['msg'];
                return $ret;
            }
        }

        return $ret;
    }

    /************************************ 产品配置 - 域名配置 ************************************/

    /**
     * 编辑动态域名的时候，对满足条件的分享配置，新增一张二维码合成图
     * @author Carter
     */
    private function _increaseQrcodeBgImage($gameId, $linkId, $address)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        vendor('phpqrcode.phpqrcode');

        // 资源服地址
        $resSvr = C('RESOURCE_SERVER_IPHOST').":".C('RESOURCE_SERVER_PORT').'/';

        $qrObj = new \QRcode();
        $apiSer = new ApiService();
        $gameMod = new GameModel();
        $confMod = new GameShareModel();

        // 如果存在市包，也要去获取市包的配置
        $localMap = $gameMod->localMap;
        if (!empty($localMap[$gameId])) {
            $gameId = array_merge(array($gameId), array_keys($localMap[$gameId]));
        }

        // 获取所有该游戏的分享内容
        $attr = array(
            'gameId' => $gameId,
            'shareType' => $confMod::SHARE_TYPE_SYS,
        );
        $modRet = $confMod->queryGameShareByAttr($attr, 'id,address,image,qrcode_x,qrcode_y');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $confList = array();
        foreach ($modRet['data'] as $v) {
            // 对于系统分享方式且二维码地址为动态域名的内容，要新加一张新域名的二维码背景图合成图
            if (empty($v['address']) || '%domain%' == $v['address']) {
                $confList[] = $v;
            }
        }
        if (empty($confList)) {
            return $ret;
        }

        // 生成二维码图
        $qrImageArr = array();
        // 容错级别
        $errorCorrectionLevel = 3;
        // 生成图片大小
        $matrixPointSize = 4;
        // 定义二维码图片生成后的文件路径
        $qrFilename = ROOT_PATH."FileUpload/ShareImg/qr_0_{$linkId}.png";
        // 生成二维码png文件
        $qrObj->png($address, $qrFilename, $errorCorrectionLevel, $matrixPointSize, 2);

        // 将二维码与背景图合并后在本地保存
        $qrBgImgArr = array();
        foreach ($confList as $v) {
            // 创建背景图GD图像
            $bgGdImg = imagecreatefromjpeg($resSvr.$v['image']);
            $bgImgW = imagesx($bgGdImg);
            $bgImgH = imagesy($bgGdImg);

            // 二维码图
            $qrGdImg = imagecreatefrompng($qrFilename);
            $srcX = 0;
            $srcY = 0;
            $srcW = imagesx($qrGdImg);
            $srcH = imagesy($qrGdImg);

            $dstX = intval(($bgImgW - $srcW) * $v['qrcode_x'] / 1000);
            $dstY = intval(($bgImgH - $srcH) * $v['qrcode_y'] / 1000);

            if (true !== imagecopymerge($bgGdImg, $qrGdImg, $dstX, $dstY, $srcX, $srcY, $srcW, $srcH, 100)) {
                set_exception(__FILE__, __LINE__, "[_increaseQrcodeBgImage] {$v['image']} and {$qrFilename} imagecopymerge failed");
                continue;
            }

            $finishFileName = ROOT_PATH."FileUpload/ShareImg/qrbg_{$v['id']}_{$linkId}.jpg";
            if (true !== imagejpeg($bgGdImg, $finishFileName)) {
                set_exception(__FILE__, __LINE__, "[_increaseQrcodeBgImage] {$finishFileName} imagejpeg failed");
                continue;
            }
            $qrBgImgArr[] = array(
                'confId' => $v['id'],
                'checkBit' => substr(md5($v['image'].$v['address'].$v['qrcode_x'].$v['qrcode_y']), 0, 4),
                'imgFile' => $finishFileName,
            );

            // 释放资源
            imagedestroy($bgGdImg);
            imagedestroy($qrGdImg);
        }

        // 将合成图上传到资源服
        foreach ($qrBgImgArr as $v) {
            $svrPath = "Admin/GameShare/QrBgImg/{$v['confId']}/qrbg_{$v['confId']}_{$linkId}_{$v['checkBit']}.jpg";
            $svrRet = $apiSer->resourceServerUploadImg($svrPath, $v['imgFile']);
            if (ERRCODE_SUCCESS !== $svrRet['code']) {
                $ret['code'] = $svrRet['code'];
                $ret['msg'] = $svrRet['msg'];
                return $ret;
            }
        }
        $ret['data'] = $qrBgImgArr;

        return $ret;
    }

    /**
     * 对满足条件的分享配置，移除一张指定的二维码背景合成图
     * @author Carter
     */
    private function _decreaseQrcodeBgImage($gameId, $linkId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $apiSer = new ApiService();
        $gameMod = new GameModel();
        $confMod = new GameShareModel();

        // 如果存在市包，也要去获取市包的配置
        $localMap = $gameMod->localMap;
        if (!empty($localMap[$gameId])) {
            $gameId = array_merge(array($gameId), array_keys($localMap[$gameId]));
        }

        // 获取所有该游戏的分享内容
        $attr = array(
            'gameId' => $gameId,
            'shareType' => $confMod::SHARE_TYPE_SYS,
        );
        $modRet = $confMod->queryGameShareByAttr($attr, 'id,address,image,qrcode_x,qrcode_y');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $confList = array();
        foreach ($modRet['data'] as $v) {
            // 对于系统分享方式且二维码地址为动态域名的内容，要移除指定域名的二维码背景图合成图
            if (empty($v['address']) || '%domain%' == $v['address']) {
                $confList[] = $v;
            }
        }
        if (empty($confList)) {
            return $ret;
        }

        $ret['data']['qrbg'] = array();
        foreach ($confList as $v) {
            $checkBit = substr(md5($v['image'].$v['address'].$v['qrcode_x'].$v['qrcode_y']), 0, 4);
            $svrPath = "Admin/GameShare/QrBgImg/{$v['id']}/qrbg_{$v['id']}_{$linkId}_{$checkBit}.jpg";
            $svrRet = $apiSer->resourceServerDeleteImg($svrPath);
            if (ERRCODE_SUCCESS === $svrRet['code']) {
                $ret['data']['qrbg'][] = $svrPath;
            } else {
                set_exception(__FILE__, __LINE__, "[_decreaseQrcodeBgImage] delete {$svrPath} failed: ".$svrRet['msg']);
            }
        }

        return $ret;
    }

    /**
     * 添加动态域名配置
     * @author Carter
     */
    public function addGameAppDomainConf($gameId, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $domainMod = new GameShareDomainModel();

        // 插入域名配置
        $modRet = $domainMod->insertShareDomainConf($gameId, $attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $ret['data']['insertData'] = $modRet['data'];
        $linkId = $modRet['data']['id'];

        $funcRet = $this->_increaseQrcodeBgImage($gameId, $linkId, $attr['link']);
        if (ERRCODE_SUCCESS !== $funcRet['code']) {
            $ret['code'] = $funcRet['code'];
            $ret['msg'] = $funcRet['msg'];
            return $ret;
        }
        $ret['data']['qrbg'] = $funcRet['data'];

        return $ret;
    }

    /**
     * 修改动态域名配置
     * @author Carter
     */
    public function editShareDomainConf($gameId, $linkId, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $domainMod = new GameShareDomainModel();

        $modRet = $domainMod->updateShareDomainConf($gameId, $attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }
        $linkInfo = $modRet['data']['link'];
        $ret['data']['updateData'] = $upData = $modRet['data']['upData'];

        // 新状态为关闭，则移除符合条件的二维码背景合成图
        if ($domainMod::STATUS_CLOSE == $upData['status']) {
            $funcRet = $this->_decreaseQrcodeBgImage($gameId, $linkId);
            if (ERRCODE_SUCCESS !== $funcRet['code']) {
                $ret['code'] = $funcRet['code'];
                $ret['msg'] = $funcRet['msg'];
                return $ret;
            }
            $ret['data']['qrbg'] = $funcRet['data'];
        }
        // 新状态为打开，则新增符合条件的二维码背景合成图
        else if ($domainMod::STATUS_NORMAL == $upData['status']) {
            $funcRet = $this->_increaseQrcodeBgImage($gameId, $linkId, $linkInfo['link']);
            if (ERRCODE_SUCCESS !== $funcRet['code']) {
                $ret['code'] = $funcRet['code'];
                $ret['msg'] = $funcRet['msg'];
                return $ret;
            }
            $ret['data']['qrbg'] = $funcRet['data'];
        }

        return $ret;
    }

    /**
     * 删除动态域名配置
     * @author Carter
     */
    public function removeShareDomainConf($gameId, $linkId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $domainMod = new GameShareDomainModel();

        $modRet = $domainMod->deleteShareDomainConf($linkId);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }
        $linkConf = $modRet['data'];

        // 若之前该域名配置时正常状态，那么删除后要进行相关二维码背景合成图的清理
        if ($domainMod::IS_BLOCKADE_FALSE == $linkConf['is_blockade'] && $domainMod::STATUS_NORMAL == $linkConf['status']) {
            $funcRet = $this->_decreaseQrcodeBgImage($gameId, $linkId);
            if (ERRCODE_SUCCESS !== $funcRet['code']) {
                $ret['code'] = $funcRet['code'];
                $ret['msg'] = $funcRet['msg'];
                return $ret;
            }
            $ret['data']['qrbg'] = $funcRet['data'];
        }

        return $ret;
    }

    /************************************ 产品配置 - 人工排查 ************************************/

    /**
     * 更新人工排查的分享配置文件
     * @author Carter
     */
    public function refrashManualShareConfFile($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $apiSvr = new ApiService();
        $appidMod = new GameShareAppidModel();
        $domainMod = new GameShareDomainModel();

        $uploadPath = ROOT_PATH."FileUpload/shareConf/";
        if (!is_dir($uploadPath)) {
            if (!mkdir($uploadPath, 0777, true)) {
                $retData['code'] = ERRCODE_SYSTEM;
                $retData['msg'] = "mkdir {$uploadPath} failed";
                $this->ajaxReturn($retData);
            }
        }

        $filename = $uploadPath.'shareConfForManual.json';
        if (!is_file($filename)) {
            $fileConf = array(
                'conf_id' => 0,
                'appid' => '',
                'link_id' => 0,
                'link' => '',
            );
        } else {
            $fileConf = json_decode(file_get_contents($filename), true);
            if (false == $fileConf) {
                $ret['code'] = ERRCODE_DATA_ERR;
                $ret['msg'] = '配置文件内容有误';
                return $ret;
            }
        }

        // 修改 appid
        if (1 == $attr['type']) {
            $modRet = $appidMod->queryGameShareAppidById($attr['id'], 'appid');
            if (ERRCODE_SUCCESS !== $modRet['code']) {
                $ret['code'] = $modRet['code'];
                $ret['msg'] = $modRet['msg'];
                return $ret;
            }
            $fileConf['conf_id'] = $attr['id'];
            $fileConf['appid'] = $modRet['data']['appid'];
        }
        // 修改域名
        else if (2 == $attr['type']) {
            if (0 == $attr['id']) {
                $fileConf['link_id'] = 0;
                $fileConf['link'] = 'http://www.qq.com/';
            } else {
                $modRet = $domainMod->queryGameShareDomainById($attr['id'], 'link');
                if (ERRCODE_SUCCESS !== $modRet['code']) {
                    $ret['code'] = $modRet['code'];
                    $ret['msg'] = $modRet['msg'];
                    return $ret;
                }
                $fileConf['link_id'] = $attr['id'];
                $fileConf['link'] = $modRet['data']['link'];
            }
        }

        if (false === file_put_contents($filename, json_encode($fileConf))) {
            $ret['code'] = ERRCODE_SYSTEM;
            $ret['msg'] = "写入配置文件{$filename}失败";
            return $ret;
        }

        $svrRet = $apiSvr->shareServerSendConfFile($filename);
        if (ERRCODE_SUCCESS !== $svrRet['code']) {
            $ret['code'] = $svrRet['code'];
            $ret['msg'] = $svrRet['msg'];
            return $ret;
        }

        return $ret;
    }

    /************************************ 产品配置 - 维护控制、白名单、版本管理、落地页配置 ************************************/

    /**
     * 维护控制通知后端
     * @author Carter
     */
    public function notifyServerGameConfUpdate($gameId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $apiSer = new ApiService();
        $confMod = new GameConfModel();

        //秘钥，签名使用
        $privateKey = C('GAME_API_PRIVATEKEY') ;

        $field = 'game_status,upgrade_time,upgrade_dismiss_time,upgrade_notify_rule,';
        $field .= 'upgrade_msg,upgrade_notify_status,upgrade_notify_start_time,';
        $field .= 'upgrade_notify_end_time,upgrade_notify_title,upgrade_notify_content';
        $modRet = $confMod->queryGameConfByGameId($gameId, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $conf = $modRet['data'];

        // 启用标志，为了提高安全性，只有维护中或通知维护时启动功能，其他状态包括异常状态都不启动维护通知
        if (
            $confMod::GAME_STATUS_UPDATE == $conf['game_status'] ||
            $confMod::GAME_STATUS_READING == $conf['game_status']
        ) {
            $enabled = 'true';
        } else {
            $enabled = 'false';
        }

        // 通知提示的规则
        $notifyRule = unserialize($conf['upgrade_notify_rule']);
        if (empty($conf['upgrade_notify_rule'])) {
            $notifyRule = array();
        }

        $param = array();

        // 操作
        $param['act'] = 'shutdown';

        // 是否启用
        $param['enabled'] = $enabled;

        // 关服时间
        $param['timestamp'] = $conf['upgrade_time'];

        // 房间解散倒计时
        $param['countdown'] = $conf['upgrade_dismiss_time'];

        // 客户端参数
        $client = array(
            // 产品ID
            'gameAppId' => $gameId,
            // 游戏状态
            'gameStatus' => $confMod->gameStatusMap[$conf['game_status']]['val'] ? : 'online',
            // 是否定时关服
            'enabled' => $enabled,
            // 维护时间
            'gameUpgradeTime' => $conf['upgrade_time'],
            // 维护前多久解散房间
            'gameCloseRoomTime' => $conf['upgrade_dismiss_time'],
            // 跑马灯通知相关内容
            'gameMessage' => array(
                // 通知提示的规则，默认包括30分钟、15分钟
                'messageRule' => array_merge(array('30', '15'), $notifyRule),
                // 显示内容
                'updateMessage' => $conf['upgrade_msg'],
            ),
            // 维护公告面板
            'gameNotify' => array(
                // 面板开关
                'notifyStatus' => $confMod->notifyStatusMap[$conf['upgrade_notify_status']]['val'],
                // 公告开始时间
                'notifyStartTime' => $conf['upgrade_notify_start_time'],
                // 公告结束时间
                'notifyEndTime' => $conf['upgrade_notify_end_time'],
                // 公告标题
                'notifyTitle' => $conf['upgrade_notify_title'],
                // 公告内容
                'notifyContent' => $conf['upgrade_notify_content'],
            ),
        );
        $param['client'] = json_encode($client);

        // 签名，要先对数据进行升序排序
        ksort($param);
        $param['signature'] = md5($privateKey.json_encode($param).$privateKey);

        // 调用接口
        $svrRet = $apiSer->dsSvrApiPostQuery($gameId, '/api/sys/', $param);
        if (ERRCODE_SUCCESS !== $svrRet['code']) {
            $ret['code'] = $svrRet['code'];
            $ret['msg'] = $svrRet['msg'];
            return $ret;
        }
        $apiRet = $svrRet['data'];
        if (0 !== $apiRet['result']) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = $apiRet['desc'] ? : '调取接口失败，错误信息：'.var_export($apiRet, true);
            return $ret;
        }

        return $ret;
    }

    /**
     * 更新白名单文件(包括产品信息、维护信息、白名单信息)
     * 兼容 EKEY_APPCONF，所有游戏更新到新版本后，本方法删除
     * @author Carter
     */
    public function refrashGameAppConfFileOld($gameId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $apiSer = new ApiService();
        $confMod = new GameConfModel();
        $whiteMod = new GameWhiteListModel();
        $blackMod = new GameBlackListModel();
        $updatewhiteMod = new GameUpdateWhiteListModel();
        $syscacheMod = new SysCacheModel();
        $versionMod = new GameAppSubversionModel();

        // 获取游戏基本维护信息
        $field = 'game_status,upgrade_time,upgrade_dismiss_time,upgrade_notify_rule,';
        $field .= 'upgrade_msg,upgrade_notify_status,upgrade_notify_start_time,';
        $field .= 'upgrade_notify_end_time,upgrade_notify_title,upgrade_notify_content';
        $modRet = $confMod->queryGameConfByGameId($gameId, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $conf = $modRet['data'];

        // 启用标志，为了提高安全性，只有维护中或通知维护时启动功能，其他状态包括异常状态都不启动维护通知
        if (
            $confMod::GAME_STATUS_UPDATE == $conf['game_status'] ||
            $confMod::GAME_STATUS_READING == $conf['game_status']
        ) {
            $enabled = 'true';
        } else {
            $enabled = 'false';
        }

        // 通知提示的规则
        $notifyRule = unserialize($conf['upgrade_notify_rule']);
        if (empty($conf['upgrade_notify_rule'])) {
            $notifyRule = array();
        }

        // 获取白名单数据
        $whiteIp = array();
        $whiteUnionid = array();
        $whiteUid = array();
        $modRet = $whiteMod->queryAllGameWhiteListByAttr($gameId);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        foreach ($modRet['data'] as $v) {
            switch ($v['white_type']) {
                // IP
                case $whiteMod::WHITE_TYPE_IP:
                    $whiteIp[] = $v['white_val'];
                    break;
                // 微信unionId
                case $whiteMod::WHITE_TYPE_UNIONID:
                    $whiteUnionid[] = $v['white_val'];
                    break;
                // 游戏uid
                case $whiteMod::WHITE_TYPE_UID:
                    $whiteUid[] = $v['white_val'];
                    break;
            }
        }

        // 获取白名单数据(热更新)
        $updatewhiteIp = array();
        $updatewhiteUnionid = array();
        $updatewhiteUid = array();
        $modRet = $updatewhiteMod->queryAllGameUpdateWhiteListByAttr($gameId);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        foreach ($modRet['data'] as $v) {
            switch ($v['white_type']) {
                // IP
                case $updatewhiteMod::WHITE_TYPE_IP:
                    $updatewhiteIp[] = $v['white_val'];
                    break;
                // 微信unionId
                case $updatewhiteMod::WHITE_TYPE_UNIONID:
                    $updatewhiteUnionid[] = $v['white_val'];
                    break;
                // 游戏uid
                case $updatewhiteMod::WHITE_TYPE_UID:
                    $updatewhiteUid[] = $v['white_val'];
                    break;
            }
        }

        // 获取黑名单数据
        $blackUid = array();
        $modRet = $blackMod->queryAllGameBlackListByAttr($gameId);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        foreach ($modRet['data'] as $v) {
            switch ($v['black_type']) {
                // 游戏uid
                case $blackMod::BLACK_TYPE_UID:
                    $blackUid[] = $v['black_val'];
                    break;
            }
        }

        $cData = array();

        // 产品ID
        $cData['gameAppId'] = $gameId;

        // 游戏状态：online 正常运行；update 维护中；reading 准备维护
        $cData['gameStatus'] = $confMod->gameStatusMap[$conf['game_status']]['val'] ? : 'online';

        // 维护管理 - 是否定时关服，online 为 false，update、reading 为 true
        $cData['enabled'] = $enabled;

        // 维护管理 - 维护时间
        $cData['gameUpgradeTime'] = $conf['upgrade_time'];

        // 维护管理 - 维护前多久解散房间，单位分钟
        $cData['gameCloseRoomTime'] = $conf['upgrade_dismiss_time'];

        // 维护管理- 跑马灯通知信息
        $cData['gameMessage'] = array(
            // 通知提示的规则，默认包括30分钟、15分钟
            'messageRule' => array_merge(array('30', '15'), $notifyRule),
            // 显示内容
            'updateMessage' => $conf['upgrade_msg'],
        );

        // 维护公告面板
        $cData['gameNotify'] = array(
            // 面板开关
            'notifyStatus' => $confMod->notifyStatusMap[$conf['upgrade_notify_status']]['val'],
            // 公告开始时间
            'notifyStartTime' => $conf['upgrade_notify_start_time'],
            // 公告结束时间
            'notifyEndTime' => $conf['upgrade_notify_end_time'],
            // 公告标题
            'notifyTitle' => $conf['upgrade_notify_title'],
            // 公告内容
            'notifyContent' => $conf['upgrade_notify_content'],
        );

        // 白名单列表
        $cData['gameWhiteList'] = array(
            // IP
            'ip' => $whiteIp,
            // 微信unionId
            'unionId' => $whiteUnionid,
            // 游戏uid
            'uid' => $whiteUid,
        );

        // 白名单列表(热更新)
        $modRet = $syscacheMod->querySysCacheByKey($gameId, 'sadmin_update_whitelist_status');
        $whiteListStatus = ERRCODE_SUCCESS == $modRet['code'] && !empty($modRet['data']) ? (string)$modRet['data']['cache_sting'] : 'Off';
        $modRet = $syscacheMod->querySysCacheByKey($gameId, 'sadmin_update_whitelist_version');
        $whiteListVersion = ERRCODE_SUCCESS == $modRet['code'] && !empty($modRet['data']) ? (string)$modRet['data']['cache_sting'] : '';

        // 子游戏版本
        $modRet = $versionMod->queryGameAllSubversion($gameId, 'version, play_id as playId');
        $subVersion = ERRCODE_SUCCESS == $modRet['code'] && !empty($modRet['data']) ? $modRet['data'] : [];

        $cData['updateWhiteList'] = array(
            // IP
            'ip' => $updatewhiteIp,
            // 微信unionId
            'unionId' => $updatewhiteUnionid,
            // 游戏uid
            'uid' => $updatewhiteUid,
            // 状态status
            'status' => $whiteListStatus,
            // 版本version
            'curVersion' => $whiteListVersion,
            // 子游戏版本
            'subVersion' => $subVersion
        );

        // 黑名单列表
        $cData['gameBlackList'] = array(
            // 游戏uid
            'uid' => $blackUid,
        );

        // 先将白名单文件写到 FileUpload 目录
        $uploadPath = ROOT_PATH."FileUpload/GameConfFile/";
        if (!is_dir($uploadPath)) {
            if (!mkdir($uploadPath, 0777, true)) {
                $ret['code'] = ERRCODE_SYSTEM;
                $ret['msg'] = "mkdir {$uploadPath} failed";
                return $ret;
            }
        }
        $fname = "gameconf_{$gameId}.json";
        if (false === file_put_contents($uploadPath.$fname, json_encode($cData))) {
            $ret['code'] = ERRCODE_SYSTEM;
            $ret['msg'] = "写入配置文件{$fname}失败";
            return $ret;
        }

        // 发送json文件到cnd 域名的服务器
        $svrRet = $apiSer->whitelistSendConfFile($fname, $uploadPath.$fname);
        if (ERRCODE_SUCCESS !== $svrRet['code']) {
            $ret['code'] = $svrRet['code'];
            $ret['msg'] = $svrRet['msg'];
            set_exception(__FILE__, __LINE__, $svrRet['msg']);
            return $ret;
        }

        return  $ret;
    }

    /**
     * 更新白名单文件(包括产品信息、维护信息、白名单、版本管理信息)
     * @author Carter
     */
    public function refrashGameAppConfFile($gameId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $apiSer = new ApiService();
        $chnMod = new GameChannelModel();
        $confMod = new GameConfModel();
        $whiteMod = new GameWhiteListModel();
        $blackMod = new GameBlackListModel();
        $updatewhiteMod = new GameUpdateWhiteListModel();
        $verMod = new GameAppVersionModel();
        $syscacheMod = new SysCacheModel();
        $versionMod = new GameAppSubversionModel();

        // 获取渠道信息
        $modRet = $chnMod->queryGameAllChannelByAttr(array('game_id' => $gameId), 'code');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $channel = array_column($modRet['data'], 'code');

        // 获取游戏基本维护信息
        $field = 'game_status,upgrade_time,upgrade_dismiss_time,upgrade_notify_rule,';
        $field .= 'upgrade_msg,upgrade_notify_status,upgrade_notify_start_time,';
        $field .= 'upgrade_notify_end_time,upgrade_notify_title,upgrade_notify_content';
        $modRet = $confMod->queryGameConfByGameId($gameId, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $conf = $modRet['data'];

        // 启用标志，为了提高安全性，只有维护中或通知维护时启动功能，其他状态包括异常状态都不启动维护通知
        if (
            $confMod::GAME_STATUS_UPDATE == $conf['game_status'] ||
            $confMod::GAME_STATUS_READING == $conf['game_status']
        ) {
            $enabled = 'true';
        } else {
            $enabled = 'false';
        }

        // 通知提示的规则
        $notifyRule = unserialize($conf['upgrade_notify_rule']);
        if (empty($conf['upgrade_notify_rule'])) {
            $notifyRule = array();
        }

        // 获取白名单数据
        $whiteIp = array();
        $whiteUnionid = array();
        $whiteUid = array();
        $modRet = $whiteMod->queryAllGameWhiteListByAttr($gameId);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        foreach ($modRet['data'] as $v) {
            switch ($v['white_type']) {
                // IP
                case $whiteMod::WHITE_TYPE_IP:
                    $whiteIp[] = $v['white_val'];
                    break;
                    // 微信unionId
                case $whiteMod::WHITE_TYPE_UNIONID:
                    $whiteUnionid[] = $v['white_val'];
                    break;
                    // 游戏uid
                case $whiteMod::WHITE_TYPE_UID:
                    $whiteUid[] = $v['white_val'];
                    break;
            }
        }

        // 获取白名单数据(热更新)
        $updatewhiteIp = array();
        $updatewhiteUnionid = array();
        $updatewhiteUid = array();
        $modRet = $updatewhiteMod->queryAllGameUpdateWhiteListByAttr($gameId);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        foreach ($modRet['data'] as $v) {
            switch ($v['white_type']) {
                // IP
                case $updatewhiteMod::WHITE_TYPE_IP:
                    $updatewhiteIp[] = $v['white_val'];
                    break;
                // 微信unionId
                case $updatewhiteMod::WHITE_TYPE_UNIONID:
                    $updatewhiteUnionid[] = $v['white_val'];
                    break;
                // 游戏uid
                case $updatewhiteMod::WHITE_TYPE_UID:
                    $updatewhiteUid[] = $v['white_val'];
                    break;
            }
        }

        // 获取黑名单数据
        $blackUid = array();
        $modRet = $blackMod->queryAllGameBlackListByAttr($gameId);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        foreach ($modRet['data'] as $v) {
            switch ($v['black_type']) {
                // 游戏uid
                case $blackMod::BLACK_TYPE_UID:
                    $blackUid[] = $v['black_val'];
                    break;
            }
        }

        // 获取渠道最新版本信息
        $attr = array(
            'game_id' => $gameId,
            'latest_flag' => 1,
        );
        $field = 'channel_code,update_mode,update_version,update_url,status';
        $modRet = $verMod->queryGameAllAppVersionByAttr($attr, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $verMap = array();
        foreach ($modRet['data'] as $v) {
            if ($verMod::STATUS_PUBLISHED != $v['status']) {
                set_exception(__FILE__, __LINE__, "[refrashGameAppConfFile] ".var_export($v, true));
                continue;
            }
            $verMap[$v['channel_code']] = array(
                'update_mode' => $v['update_mode'],
                'update_version' => $v['update_version'],
                'update_url' => $v['update_url'],
            );
        }

        $verModeMap = $verMod->updateModeMap;

        $cCommonData = array();

        // 产品ID
        $cCommonData['gameAppId'] = $gameId;

        // 游戏状态：online 正常运行；update 维护中；reading 准备维护
        $cCommonData['gameStatus'] = $confMod->gameStatusMap[$conf['game_status']]['val'] ? : 'online';

        // 正常运行状态，则不记录维护信息或白名单信息
        if ('online' != $cCommonData['gameStatus']) {

            // 维护管理 - 是否定时关服，online 为 false，update、reading 为 true
            $cCommonData['enabled'] = $enabled;

            // 维护管理 - 维护时间
            $cCommonData['gameUpgradeTime'] = $conf['upgrade_time'];

            // 维护管理 - 维护前多久解散房间，单位分钟
            $cCommonData['gameCloseRoomTime'] = $conf['upgrade_dismiss_time'];

            // 维护管理- 跑马灯通知信息
            $cCommonData['gameMessage'] = array(
                // 通知提示的规则，默认包括30分钟、15分钟
                'messageRule' => array_merge(array('30', '15'), $notifyRule),
                // 显示内容
                'updateMessage' => $conf['upgrade_msg'],
            );

            // 维护公告面板
            $cCommonData['gameNotify'] = array(
                // 面板开关
                'notifyStatus' => $confMod->notifyStatusMap[$conf['upgrade_notify_status']]['val'],
                // 公告开始时间
                'notifyStartTime' => $conf['upgrade_notify_start_time'],
                // 公告结束时间
                'notifyEndTime' => $conf['upgrade_notify_end_time'],
                // 公告标题
                'notifyTitle' => $conf['upgrade_notify_title'],
                // 公告内容
                'notifyContent' => $conf['upgrade_notify_content'],
            );

            // 白名单列表
            $cCommonData['gameWhiteList'] = array(
                // IP
                'ip' => $whiteIp,
                // 微信unionId
                'unionId' => $whiteUnionid,
                // 游戏uid
                'uid' => $whiteUid,
            );
        }

        // 白名单列表(热更新)
        $modRet = $syscacheMod->querySysCacheByKey($gameId, 'sadmin_update_whitelist_status');
        $whiteListStatus = ERRCODE_SUCCESS == $modRet['code'] && !empty($modRet['data']) ? (string)$modRet['data']['cache_sting'] : 'Off';
        $modRet = $syscacheMod->querySysCacheByKey($gameId, 'sadmin_update_whitelist_version');
        $whiteListVersion = ERRCODE_SUCCESS == $modRet['code'] && !empty($modRet['data']) ? (string)$modRet['data']['cache_sting'] : '';

        // 子游戏版本
        $modRet = $versionMod->queryGameAllSubversion($gameId, 'version, play_id as playId');
        $subVersion = ERRCODE_SUCCESS == $modRet['code'] && !empty($modRet['data']) ? $modRet['data'] : [];

        $cCommonData['updateWhiteList'] = array(
            // IP
            'ip' => $updatewhiteIp,
            // 微信unionId
            'unionId' => $updatewhiteUnionid,
            // 游戏uid
            'uid' => $updatewhiteUid,
            // 状态status
            'stauts' => $whiteListStatus,
            // 版本version
            'curVersion' => $whiteListVersion,
            // 子游戏版本
            'subVersion' => $subVersion
        );

        // 黑名单列表
        $cCommonData['gameBlackList'] = array(
            // 游戏uid
            'uid' => $blackUid,
        );

        // 先将白名单文件写到 FileUpload 目录
        $uploadPath = ROOT_PATH."FileUpload/GameConfFile/";
        if (!is_dir($uploadPath)) {
            if (!mkdir($uploadPath, 0777, true)) {
                $ret['code'] = ERRCODE_SYSTEM;
                $ret['msg'] = "mkdir {$uploadPath} failed";
                return $ret;
            }
        }

        // 逐个渠道上传配置文件
        foreach ($channel as $chnCode) {
            if (!isset($verMap[$chnCode])) {
                // 渠道未发布任何版本，需要发布一次才允许上传配置文件
                continue;
            }

            $cVersionData = array(
                // 渠道ID
                'channelId' => $chnCode,
                // 主版本信息
                'mainVersion' => array(
                    // 当前渠道版本号
                    'version' => $verMap[$chnCode]['update_version'],
                    // 当前渠道版本更新方式，replace强更，packs热更
                    'type' => $verModeMap[$verMap[$chnCode]['update_mode']]['val'],
                ),
            );

            // 强更
            if ($verMod::UPDATE_MODE_REPLACE == $verMap[$chnCode]['update_mode']) {
                // 当前渠道版本资源地址
                $cVersionData['mainVersion']['versionUrl'] = $verMap[$chnCode]['update_url'];
            }
            // 热更
            else if ($verMod::UPDATE_MODE_PACKS == $verMap[$chnCode]['update_mode']) {
                $imgSvr = C('RESOURCE_SERVER_IPHOST').':'.C('RESOURCE_SERVER_PORT').'/';
                $cVersionData['mainVersion']['resUrl'] = $imgSvr.$verMap[$chnCode]['update_url'];
            }

            $cData = array_merge($cCommonData, $cVersionData);

            $fname = time().".json";
            if (false === file_put_contents($uploadPath.$fname, json_encode($cData))) {
                $ret['code'] = ERRCODE_SYSTEM;
                $ret['msg'] = "写入配置文件{$fname}失败";
                return $ret;
            }

            $svrPath = "gameConf/{$gameId}/{$chnCode}/conf.json";

            // 发送json文件到cnd 域名的服务器
            $svrRet = $apiSer->whitelistSendConfFile($svrPath, $uploadPath.$fname);
            if (ERRCODE_SUCCESS !== $svrRet['code']) {
                $ret['code'] = $svrRet['code'];
                $ret['msg'] = $svrRet['msg'];
                set_exception(__FILE__, __LINE__, $svrRet['msg']);
                return $ret;
            }
        }

        return  $ret;
    }

    /**
     * 添加维护控制信息记录
     * @author Carter
     */
    public function addGameAppConf($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $mod = new \Think\Model();
        $confMod = new GameConfModel();

        // 起一个事务，若接口或文件更新失败可以回滚
        $mod->startTrans();

        // 先配置入库
        $modRet = $confMod->insertGameAppConf($attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            $mod->rollback();
            return $ret;
        }
        $ret['data'] = $modRet['data'];

        // 然后调用接口
        $lgcRet = $this->notifyServerGameConfUpdate($attr['game_id']);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $ret['code'] = $lgcRet['code'];
            $ret['msg'] = $lgcRet['msg'];
            $mod->rollback();
            return $ret;
        }

        // 接口成功后推送配置文件（旧版白名单文件）
        // EKEY_APPCONF 兼容
        // ----- start -----
        $lgcRet = $this->refrashGameAppConfFileOld($attr['game_id']);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $ret['code'] = $lgcRet['code'];
            $ret['msg'] = $lgcRet['msg'];
            $mod->rollback();
            return $ret;
        }
        // ----- end -----

        // 推送白名单配置文件
        $lgcRet = $this->refrashGameAppConfFile($attr['game_id']);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $ret['code'] = $lgcRet['code'];
            $ret['msg'] = $lgcRet['msg'];
            $mod->rollback();
            return $ret;
        }

        $mod->commit();
        return $ret;
    }

    /**
     * 修改维护控制信息
     * @author Carter
     */
    public function editGameAppConf($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $mod = new \Think\Model();
        $confMod = new GameConfModel();

        // 起一个事务，若接口或文件更新失败可以回滚
        $mod->startTrans();

        // 先配置入库
        $modRet = $confMod->updateGameAppConf($attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            $mod->rollback();
            return $ret;
        }
        $ret['data'] = $modRet['data'];

        // 然后调用接口
        $lgcRet = $this->notifyServerGameConfUpdate($attr['game_id']);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $ret['code'] = $lgcRet['code'];
            $ret['msg'] = $lgcRet['msg'];
            $mod->rollback();
            return $ret;
        }

        // 接口成功后推送配置文件（旧版白名单文件）
        // EKEY_APPCONF 兼容
        // ----- start -----
        $lgcRet = $this->refrashGameAppConfFileOld($attr['game_id']);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $ret['code'] = $lgcRet['code'];
            $ret['msg'] = $lgcRet['msg'];
            $mod->rollback();
            return $ret;
        }
        // ----- end -----

        // 推送白名单配置文件
        $lgcRet = $this->refrashGameAppConfFile($attr['game_id']);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $ret['code'] = $lgcRet['code'];
            $ret['msg'] = $lgcRet['msg'];
            $mod->rollback();
            return $ret;
        }

        $mod->commit();
        return $ret;
    }

    /**
     * 获取版本管理渠道列表信息
     * @author Carter
     */
    public function getGAppVersionChannelList($gameId, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $chnMod = new GameChannelModel();
        $verMod = new GameAppVersionModel();

        // 获取渠道列表
        $attr['game_id'] = $gameId;
        $modRet = $chnMod->queryGameAllChannelByAttr($attr, 'code,name,package_name');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $chnList = $modRet['data'];

        // 获取渠道最新版本号
        $verAttr = array(
            'game_id' => $gameId,
            'latest_flag' => 1,
        );
        $modRet = $verMod->queryGameAllAppVersionByAttr($verAttr, 'channel_code,update_version');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $verMap = array_combine(
            array_column($modRet['data'], 'channel_code'),
            array_column($modRet['data'], 'update_version')
        );

        // 给每个渠道赋最新版本号
        foreach ($chnList as $k => $v) {
            if (isset($verMap[$v['code']])) {
                $chnList[$k]['latest_version'] = $verMap[$v['code']];
            } else {
                $chnList[$k]['latest_version'] = '未有发布记录';
            }
        }

        $ret['data'] = $chnList;

        return $ret;
    }

    /**
     * 获取落地页配置
     * @author daniel
     */
    public function getLandPageConfig($gameId, $placeId)
    {
        $ret = [
            'code' => ERRCODE_SUCCESS,
            'msg' => '',
            'data' => []
        ];

        if (empty($gameId) || empty($placeId)) {
            $ret['code'] = ERRCODE_DATA_ERR;
            $ret['msg'] = '参数错误';
            return $ret;
        }

        $landPageMod = new GameLandpageModel();
        $sqlWhere = '';
        if ($gameId == $placeId) { // 兼容省级包新增字段place_id为0的情况
            $sqlWhere = "game_id = " . C('G_USER.gameid') . " AND (place_id = $placeId OR place_id = 0)";
        } else {
            $sqlWhere = "game_id = " . C('G_USER.gameid') . " AND place_id = $placeId";
        }
        $landPageRet = $landPageMod->getGameData($sqlWhere);
        if (ERRCODE_SUCCESS !== $landPageRet['code']) {
            $ret['code'] = $landPageRet['code'];
            $ret['msg'] = $landPageRet['msg'];
            return $ret;
        }
        // 兼容省级包新增字段place_id为0的情况
        $data = $landPageRet['data'];
        $tmp = $data[0];
        if (count($data) > 1) {
            foreach ($data as $key => $config) {
                if ($config['place_id'] == $placeId) {
                    $tmp = $data[$key];
                }
            }
        }
        $ret['data'] = $tmp;
        return $ret;
    }

    /**
     * 获取地址树有效映射
     * @author daniel
     */
    public function getLandPageValidPlace($gameId)
    {
        $ret = [
            'code' => ERRCODE_SUCCESS,
            'msg' => '',
            'data' => []
        ];

        $gameMod = new GameModel();
        $gameLandPageMod = new GameLandpageModel();

        $validLocalMap = $gameMod->localMap[$gameId];
        $gameIdArr = array_merge([$gameId], array_keys($validLocalMap));

        $attr = ['game_id' => $gameIdArr];
        $field = 'place_id, game_id';
        $validPlaceRet = $gameLandPageMod->getGameData($attr, $field);
        if(ERRCODE_SUCCESS !== $validPlaceRet['code']) {
            $ret['code'] = $validPlaceRet['code'];
            $ret['msg'] = $validPlaceRet['msg'];
            return $ret;
        }

        // 兼容旧版本只有省级包有配置，且新增place_id字段为0
        $retPlaceList = [];
        if (is_array($validPlaceRet['data'])) {
            foreach ($validPlaceRet['data'] as $place) {
                $tmp = 0;
                if ($place['place_id'] == 0) {
                    $tmp = $place['game_id'];
                } else {
                    $tmp = $place['place_id'];
                }

                if (!in_array($tmp, $retPlaceList)) {
                    $retPlaceList[] = $tmp;
                }
            }
        }
        $ret['data'] = $retPlaceList;
        return $ret;
    }
}
