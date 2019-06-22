<?php

namespace Home\Model\DsqpDict;
use Common\Service\ApiService;
use Think\Model;

class PlaceConfigModel extends Model
{
    /* 文件命名不符合规范，已经新建了 DictPlaceConfigModel，本Model未来要删掉 */

    // 初始配置
    protected $connection = 'GAME_DICT_DB';
    protected $trueTableName = 'dict_place_config';
    const ADV_PIC_PATH = "SA/GameConfig/Place/";
    public static $DailyRewardDiamond = array(
        5539 => 10,
    );

    // 登录广告横竖版配置Map
    const LOGIN_AD_HORIZONTAL = 1; // 登录广告为横版
    const LOGIN_AD_VERTICAL = 2;   // 登录广告为竖版
    public $loginAdDirectionPlaceIdMap = [
        3422 => [ // 江苏麻将
            3422 => self::LOGIN_AD_VERTICAL,     // 江苏麻将(省包)
            342201 => self::LOGIN_AD_VERTICAL,   // 66无锡麻将(合服)
            342202 => self::LOGIN_AD_VERTICAL,   // 66徐州麻将(合服)
            342204 => self::LOGIN_AD_VERTICAL,   // 66宿迁麻将(合服)
            342209 => self::LOGIN_AD_HORIZONTAL, // 66常州麻将(合服)

        ],
        4156 => [ // 河南麻将全集
            4156 => self::LOGIN_AD_HORIZONTAL,   // 河南麻将全集(省包)
            415601 => self::LOGIN_AD_HORIZONTAL, // 66商丘麻将(合服)
            415615 => self::LOGIN_AD_HORIZONTAL, // 66焦作麻将(合服)
        ],
        4444 => [ // 广东麻将
            4444 => self::LOGIN_AD_VERTICAL,     // 广东麻将
        ],
        5537 => [ // 安徽麻将大全
            5537 => self::LOGIN_AD_VERTICAL,     // 安徽麻将大全(省包)
            553701 => self::LOGIN_AD_VERTICAL,   // 来来蚌埠麻将(合服)
            553704 => self::LOGIN_AD_VERTICAL,   // 来来淮南麻将(合服)
            553705 => self::LOGIN_AD_HORIZONTAL, // 来来宿州麻将(合服)
        ],
        5538 => [ // 安徽麻将全集
            5538 => self::LOGIN_AD_HORIZONTAL,   // 安徽麻将全集(省包)
            553802 => self::LOGIN_AD_HORIZONTAL, // 来来阜阳麻将(合服)
        ],
        5539 => [ // 安徽麻将精华版
            5539 => self::LOGIN_AD_HORIZONTAL,   // 安徽麻将精华版(省包)
            553901 => self::LOGIN_AD_HORIZONTAL, // 来来安庆麻将(合服)
        ],
        5541 => [ // 新淮北麻将
            5541 => self::LOGIN_AD_VERTICAL,     // 新淮北麻将
        ]
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取地区配置
     * @param $placeId
     * @param mixed $field ['field1', 'field2'...] 或 "field1, field2, field3...."
     * @return array
     * @author liyao
     */
    public function queryConfigByPlaceId($placeId, $field = [])
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        if (empty($field) || !isset($field) || $field == '*') {
            $field = "*";
        } else {
            if (is_array($field)) {
                $field = implode(',', $field);
            }
        }
        $where = array('placeID'=>$placeId);
        try{
            $list = $this->field($field)->where($where)->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryConfigByPlaceId] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $list;
        return $ret;
    }

    /**
     * 修改跑马灯设置
     * @author liyao
     */
    public function updateHorse($data)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // 更新数据库
        try {
            $arr = array();
            $arr[] = $data['horsetime'];
            foreach ($data['horse'] as $v) {
                $arr[] = html_entity_decode($v);
            }
            if (count($arr) <= 1)
                $arr = array();
            $updateData = array('marqueenTipMap' => implode("|", $arr));
            $id = $this->where(array('placeID' => $data['confid']))->getField('placeID');
            if ($id)
                $this->where(array('placeID' => $data['confid']))->save($updateData);
            else {
                $updateData['placeID'] = $data['confid'];
                $this->data($updateData)->add();
            }
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateHorse] update failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $apiSer = new ApiService();
        // 调用服务端接口，刷新缓存
        $serRet = $apiSer->kaifangApiQuery('/console/?act=reload');
        if (ERRCODE_SUCCESS !== $serRet['code']) {
            $ret['code'] = $serRet['code'];
            $ret['msg'] = $serRet['msg'];
            return $ret;
        }

        return $ret;
    }

    /**
     * 修改客服页面配置
     */
    public function updateCms($data) {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // 更新数据库
        try {
            $arr = array();
            $arr[] = $data['weixincont'].'<'.$data['weixintime'].','.implode(',',$data['weixin']).'>';
            $arr[] = $data['proxycont'].'<'.$data['proxytime'].','.implode(',',$data['proxy']).'>';
            $cmscont = implode("|", $arr);
            $updateData = array('pyjRechargeTips' => $cmscont);
            $id = $this->where(array('placeID' => $data['confid']))->getField('placeID');
            if ($id)
                $this->where(array('placeID' => $data['confid']))->save($updateData);
            else {
                $updateData['placeID'] = $data['confid'];
                $this->data($updateData)->add();
            }
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateCms] update failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $apiSer = new ApiService();
        // 调用服务端接口，刷新缓存
        $serRet = $apiSer->kaifangApiQuery('/console/?act=reload');
        if (ERRCODE_SUCCESS !== $serRet['code']) {
            $ret['code'] = $serRet['code'];
            $ret['msg'] = $serRet['msg'];
            return $ret;
        }
        $ret['data'] = $cmscont;

        return $ret;
    }

    /**
     * 修改广告
     */
    public function updateAdv($data) {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // 更新数据库
        try {
            $arris = array();
            if ($data['imgsmall']) {
                for ($ix = 0; $ix < count($data['imgsmall']); $ix++) {
                    $rt = $this->_updateImgToService($data['imgsmall'][$ix]);
                    if($rt['code'] != ERRCODE_SUCCESS){
                        return $rt;
                    }
                    $name = $rt['data'];
                    $arris[] = $name;
                }
            }
            $arrib = array();
            if ($data['imgname']) {
                for ($ix = 0; $ix < count($data['imgname']); $ix++) {
                    $rt = $this->_updateImgToService($data['imgname'][$ix]);
                    if($rt['code'] != ERRCODE_SUCCESS){
                        return $rt;
                    }
                    $name = $rt['data'];
                    $arrib[] = $name;
                }
            }
            $pictime = empty($data['pictime']) ? 4 : intval($data['pictime']);
            $arrchat = array();
            if ($data['imgsmall']) {
                for ($ix = 0; $ix < count($data['imgsmall']); $ix++) {
                    $chat = array();
                    $chat['delay'] = urlencode($data['wechat'][$ix]['time']);
                    $chat['id'] = array();
                    foreach ($data['wechat'][$ix]['ids'] as $chatv) {
                        $chat['id'][] = urlencode($chatv[0].'&'.$chatv[1]);//array(urlencode($chatv[0]), urlencode($chatv[1]));
                    }
                    $arrchat[] = $chat;
                }
            }
            $dt = array();
            for ($ix = 0; $ix < count($arris); $ix++) {
                $dt[] = array("imgName" => urlencode($arrib[$ix]), "delay" => $pictime, 'imgSmall' => urlencode($arris[$ix]), 'wechat' => $arrchat[$ix]);
            }
            $advurl = '';
            if (count($dt) == 0)
                $advurl = '';
            else
                $advurl = urldecode(json_encode($dt));
            $updateData = array('pyjAdvertisementURLs' => $advurl);//.'|xxx.png'
            $id = $this->where(array('placeID' => $data['confid']))->getField('placeID');
            if ($id)
                $this->where(array('placeID' => $data['confid']))->save($updateData);
            else {
                $updateData['placeID'] = $data['confid'];
                $this->data($updateData)->add();
            }
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateAdv] update failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $apiSer = new ApiService();
        // 调用服务端接口，刷新缓存
        $serRet = $apiSer->kaifangApiQuery('/console/?act=reload');
        if (ERRCODE_SUCCESS !== $serRet['code']) {
            $ret['code'] = $serRet['code'];
            $ret['msg'] = $serRet['msg'];
            return $ret;
        }

        return $ret;
    }

    /**
     * 修改登录广告
     */
    public function updateLogAdv($data) {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // 更新数据库
        try {
            $arris = array();
            if ($data['logadv']) {
                for ($ix = 0; $ix < count($data['logadv']); $ix++) {
                    $rt = $this->_updateImgToService($data['logadv'][$ix]['pic']);
                    if($rt['code'] != ERRCODE_SUCCESS){
                        return $rt;
                    }
                    $name = $rt['data'];
                    $arris[] = array('title'=>urlencode(html_entity_decode($data['logadv'][$ix]['title'])),'img'=>urlencode($name),
                        'wechat'=>urlencode($data['logadv'][$ix]['wx']));
                }
            }
            $switch = $data['switch'];
            $parentSwitch = $data['parentSwitch'];
            $popupAdEnable = $switch ? 1 : ($parentSwitch ? 0 : -1);
            $advurl = '';
            if (count($arris) == 0)
                $advurl = '';
            else
                $advurl = urldecode(json_encode($arris));
            $updateData = array('popupAdURLs' => $advurl, 'popupAdEnable'=>$popupAdEnable);
            $id = $this->where(array('placeID' => $data['confid']))->getField('placeID');
            if ($id)
                $this->where(array('placeID' => $data['confid']))->save($updateData);
            else {
                $updateData['placeID'] = $data['confid'];
                $this->data($updateData)->add();
            }
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateLogAdv] update failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $apiSer = new ApiService();
        // 调用服务端接口，刷新缓存
        $serRet = $apiSer->dsSvrApiGetQuery(C('G_USER.gameid'),'/console/?act=reload');
        if (ERRCODE_SUCCESS !== $serRet['code']) {
            $ret['code'] = $serRet['code'];
            $ret['msg'] = $serRet['msg'];
            return $ret;
        }

        return $ret;
    }

    /**
     *
     * 修改招募代理配置
     * @author liyao
     */
    public function updateCallAgent($data) {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // 更新数据库
        try {
            $agentdata = array();
            $agentdata['winTitle'] = urlencode(html_entity_decode($data['wxtitle']));
            $agentdata['wxtime'] = $data['wxtime'];
            $rt = $this->_updateImgToService($data['banner']);
            if($rt['code'] != ERRCODE_SUCCESS){
                return $rt;
            }
            $name = $rt['data'];
            $agentdata['banner'] = urlencode(self::ADV_PIC_PATH.$name);
            $agentdata['wxid'] = urlencode(implode('|', $data['wxid']));
            $updateData = array('recruitSetting' => urldecode(json_encode($agentdata)));
            $id = $this->where(array('placeID' => $data['confid']))->getField('placeID');
            if ($id) {
                $this->where(array('placeID' => $data['confid']))->save($updateData);
            } else {
                $updateData['placeID'] = $data['confid'];
                $this->data($updateData)->add();
            }
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateCallAgent] update failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $apiSer = new ApiService();
        // 调用服务端接口，刷新缓存
        $serRet = $apiSer->kaifangApiQuery('/console/?act=reload');
        if (ERRCODE_SUCCESS !== $serRet['code']) {
            $ret['code'] = $serRet['code'];
            $ret['msg'] = $serRet['msg'];
            return $ret;
        }

        return $ret;
    }

    /**
     *
     * 修改每日分享奖励配置
     * @author liyao
     */
    public function updateDailyReward($data) {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // 更新数据库
        try {
            $data['club_diamond'] = isset($data['club_diamond']) && !empty($data['club_diamond']) ? $data['club_diamond'] : 0;
            $data['club_yuanbao'] = isset($data['club_yuanbao']) && !empty($data['club_yuanbao']) ? $data['club_yuanbao'] : 0;
            $data['no_club_diamond'] = isset($data['no_club_diamond']) && !empty($data['no_club_diamond']) ? $data['no_club_diamond'] : 0;
            $data['no_club_yuanbao'] = isset($data['no_club_yuanbao']) && !empty($data['no_club_yuanbao']) ? $data['no_club_yuanbao'] : 0;
            if ($data['club_diamond'] == 0 && $data['club_yuanbao'] == 0 && $data['no_club_diamond'] == 0 && $data['no_club_yuanbao'] == 0) {
                $val = '';
            } else {
                // 格式化
                // 10008:1|10009:100OR10008:1|10009:200
                $val = '10008:' . $data['no_club_diamond'] . '|10009:' . $data['no_club_yuanbao'] . 'OR' . '10008:' . $data['club_diamond'] . '|10009:' . $data['club_yuanbao'];
            }
            $updateData = array('awardList' => $val);
            $id = $this->where(array('placeID' => $data['confid']))->getField('placeID');
            if ($id) {
                $this->where(array('placeID' => $data['confid']))->save($updateData);
            } else {
                $updateData['placeID'] = $data['confid'];
                // 历史原因添加popupAdURLs空字符串
                $updateData['popupAdURLs'] = '';
                $this->data($updateData)->add();
            }
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateDailyReward] update failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $apiSer = new ApiService();
        // 调用服务端接口，刷新缓存
        $gameId = C("G_USER.gameid");
        $serRet = $apiSer->dsSvrApiGetQuery($gameId, '/console/?act=reload');
        if (ERRCODE_SUCCESS !== $serRet['code']) {
            $ret['code'] = $serRet['code'];
            $ret['msg'] = $serRet['msg'];
            return $ret;
        }

        return $ret;
    }

    private function _updateImgToService($img){
        $apiSer = new ApiService();

        //为空，或者已经是服务器图片地址了，不处理
        if(empty($img) ){
            $result['code'] = ERRCODE_SUCCESS;
            $result['data'] = $img ;
            return $result;
        }

        $tempname = str_replace("/FileUpload/",'', $img);
        $imgSource = ROOT_PATH."FileUpload/".$tempname;

        //资源服务器地址或者已经是服务器图片相对地址，不处理重复上传
        if(preg_match("/^\/?".self::ADV_PIC_PATH."[0-9a-zA-Z]+\.(png|jpg|gif|jpeg)/", $img)
                || strpos($img, self::ADV_PIC_PATH) !== false){
            $result['code'] = ERRCODE_SUCCESS ;
            $result['data'] = substr($img, strpos($img, self::ADV_PIC_PATH)+strlen(self::ADV_PIC_PATH));
        }else{
            if (empty($img) || !file_exists($imgSource)) {
                $result['code'] = ERRCODE_DATA_ERR;
                $result['msg'] = '图片资源不存在，请重新上传';
                $result['data'] = $img;
                return $result;
            }
            //$picname = $imgname.substr($tempname, strrpos($tempname, '.'));
            $svrPath = self::ADV_PIC_PATH.$tempname;
            $serRet = $apiSer->resourceServerUploadImg($svrPath, $imgSource);
            if (ERRCODE_SUCCESS !== $serRet['code']) {
                $result['code'] = $serRet['code'];
                $result['msg'] = $serRet['msg'];
            }else{
                $result['code'] = ERRCODE_SUCCESS ;
                $result['data'] = $tempname;
            }
        }
        return $result ;
    }

    /**
     * 粘帖配置
     * @author liyao
     */
    public function pasteConfig($sourceid, $destid)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // 更新数据库
        try {
            $info = $this->where(array('placeID' => $sourceid))->find();
            if ($info) {
                $info['placeID'] = $destid;
                $id = $this->where(array('placeID' => $destid))->getField('placeID');
                if ($id) {
                    $this->where(array('placeID' => $destid))->save($info);
                } else {
                    $this->data($info)->add();
                }
            } else {
                $ret['code'] = ERRCODE_DB_UPDATE_ERR;
                $ret['msg'] = '未找到原始配置';
                return $ret;
            }
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[pasteConfig] update failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $apiSer = new ApiService();
        // 调用服务端接口，刷新缓存
        $serRet = $apiSer->kaifangApiQuery('/console/?act=reload');
        if (ERRCODE_SUCCESS !== $serRet['code']) {
            $ret['code'] = $serRet['code'];
            $ret['msg'] = $serRet['msg'];
            return $ret;
        }

        return $ret;
    }

    /**
     * 删除配置
     * @author liyao
     */
    public function deleteConfig($id)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // 更新数据库
        try {
            $this->where(array('placeID' => $id))->delete();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[deleteConfig] delete failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $apiSer = new ApiService();
        // 调用服务端接口，刷新缓存
        $serRet = $apiSer->kaifangApiQuery('/console/?act=reload');
        if (ERRCODE_SUCCESS !== $serRet['code']) {
            $ret['code'] = $serRet['code'];
            $ret['msg'] = $serRet['msg'];
            return $ret;
        }

        return $ret;
    }

    /**
     * 删除每日分享奖励配置
     * @author daniel
     */
    public function deleteDailyRewardConfig($placeId)
    {
        $ret = [
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        ];
        try {
            $this->where(['placeID' => $placeId])->save(['awardList' => NULL]);
            // 使用*查询单条记录
            $operateConfig = $this->field('*')->where(['placeID' => $placeId])->select();
        } catch (\Exception $e) {
            $sqlRet = [
                'code' => ERRCODE_DB_SELECT_ERR,
                'msg' => "数据库连接失败，错误信息：" . $e->getMessage(),
            ];
            return $sqlRet;
        }

        // 若该行数据所有配置项为空，则删除此行数据
        $notNUllField = 'placeID';
        $existConfig = 0;
        foreach ($operateConfig[0] as $key => $config) {
            if ($key !== $notNUllField && !empty($config)){
                $existConfig = 1;
            }
            break;
        }
        if ($existConfig == 0) {
            try{
                $this->where(['placeID' => $placeId])->delete();
            } catch (\Exception $e) {
                $deleteRet = [
                    'code' => ERRCODE_DB_DELETE_ERR,
                    'msg' => "数据删除失败" . $e->getMessage()
                ];
                return $deleteRet;
            }
        }

        $apiSer = new ApiService();
        // 调用服务端接口，刷新缓存
        $gameId = C("G_USER.gameid");
        $serRet = $apiSer->dsSvrApiGetQuery($gameId, '/console/?act=reload');
        if (ERRCODE_SUCCESS !== $serRet['code']) {
            $errRet = [
                'code' => $serRet['code'],
                'msg' => $serRet['msg']
            ];
            return $errRet;
        }
        return $ret;
    }
}
