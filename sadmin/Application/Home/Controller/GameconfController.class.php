<?php
namespace Home\Controller;

use Common\Service\ValidatorService;
use Home\Logic\GameconfLogic;
use Home\Logic\MallConfigLogic;
use Home\Model\GameAppSubversionModel;
use Home\Model\GameModel;
use Home\Model\GameAppVersionModel;
use Home\Model\GameBlackListModel;
use Home\Model\GameConfModel;
use Home\Model\GameLandpageModel;
use Home\Model\GameShareAppidModel;
use Home\Model\GameShareDomainModel;
use Home\Model\GameShareModel;
use Home\Model\GameUpdateWhiteListModel;
use Home\Model\GameWhiteListModel;
use Home\Model\DsqpDict\DictPlaceModel;
use Home\Model\DsqpDict\PlaceConfigModel;
use Home\Model\SysCacheModel;

class GameconfController extends BaseController
{
    const TYPE_PACK_HORIZONTAL = 1;
    const TYPE_PACK_VERTICAL = 2;
    const TYPE_PACK_HORI_VERT_MIX = 3;

    private $reward_id_type_map = array(10008 => '钻石');
    private $reward_reward_type_map = array(10008 => '钻石');

    private $package_version = array(
        4156 => self::TYPE_PACK_HORIZONTAL,
        5538 => self::TYPE_PACK_HORIZONTAL,
        5539 => self::TYPE_PACK_HORIZONTAL,
        5537 => self::TYPE_PACK_HORI_VERT_MIX,
        4444 => self::TYPE_PACK_VERTICAL,
        4514 => self::TYPE_PACK_VERTICAL,
        3422 => self::TYPE_PACK_HORI_VERT_MIX
    );

    public function __construct()
    {
        parent::__construct();
        $this->assignBaseData();
    }

    /**
     * 分享内容进行缩略图上传，分享配置、房间配置等多处用到
     * @author Carter
     */
    private function _shareUploadThumbImg()
    {
        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $uploadPath = ROOT_PATH."FileUpload/ShareImg/";
        if (!is_dir($uploadPath)) {
            if (!mkdir($uploadPath, 0777, true)) {
                $retData['code'] = ERRCODE_SYSTEM;
                $retData['msg'] = "mkdir {$uploadPath} failed";
                return $retData;
            }
        }

        // upload 类获取上传文件，最大仅允许32KB
        $config = array(
            'maxSize'    => 32 * 1024,
            'rootPath'   => $uploadPath,
            'exts'       => array('jpg', 'jpeg'),
            'subName'    => false,
            'saveName'   => array('uniqid')
        );
        $upload = new \Think\Upload($config);
        $uploadInfo = $upload->upload();
        if (false === $uploadInfo || empty($uploadInfo['thumb_image'])) {
            $retData['code'] = ERRCODE_UPLOAD_FAILED;
            $errMsg = $upload->getError();
            if ('上传文件大小不符！' == $errMsg) {
                $retData['msg'] = '图片大小不能超过32KB';
            } else if ('上传文件后缀不允许' == $errMsg) {
                $retData['msg'] = '仅支持JPG图片';
            }
            return $retData;
        }

        // 图片限制宽高不能超过300
        $sizeInfo = getimagesize($uploadPath.$uploadInfo['thumb_image']['savename']);
        if ($sizeInfo[0] > 300) {
            $retData['code'] = ERRCODE_UPLOAD_FAILED;
            $retData['msg'] = "缩略图宽度不能大于300像素";
            return $retData;
        }
        if ($sizeInfo[1] > 300) {
            $retData['code'] = ERRCODE_UPLOAD_FAILED;
            $retData['msg'] = "缩略图高度不能大于300像素";
            return $retData;
        }

        $retData['data'] = array(
            'imgUrl' => "/FileUpload/ShareImg/".$uploadInfo['thumb_image']['savename'],
            'saveName' => $uploadInfo['thumb_image']['savename'],
        );

        return $retData;
    }

    /**
     * 分享内容进行背景图上传，分享配置、房间配置等多处用到
     * @author Carter
     */
    private function _shareUploadBgImg()
    {
        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        // 上传路径，先存放于临时路劲，等待提交的时候再上传至资源服务器
        $uploadPath = ROOT_PATH."FileUpload/ShareImg/";
        if (!is_dir($uploadPath)) {
            if (!mkdir($uploadPath, 0777, true)) {
                $retData['code'] = ERRCODE_SYSTEM;
                $retData['msg'] = "mkdir {$uploadPath} failed";
                return $retData;
            }
        }

        // upload 类获取上传文件
        $config = array(
            'maxSize'    => C('IMG_MAX_UPLOAD_SIZE') * 1024 * 1024,
            'rootPath'   => $uploadPath,
            'exts'       => array('jpg', 'jpeg'),
            'subName'    => false,
            'saveName'   => array('uniqid')
        );
        $upload = new \Think\Upload($config);
        $uploadInfo = $upload->upload();
        if (false === $uploadInfo || empty($uploadInfo['bg_image'])) {
            $retData['code'] = ERRCODE_UPLOAD_FAILED;
            $errMsg = $upload->getError();
            if ('上传文件后缀不允许' == $errMsg) {
                $retData['msg'] = '仅支持JPG图片';
            }
            return $retData;
        }

        // 获取图片宽高
        $sizeInfo = getimagesize($uploadPath.$uploadInfo['bg_image']['savename']);
        if ($sizeInfo[0] < 150) {
            $retData['code'] = ERRCODE_UPLOAD_FAILED;
            $retData['msg'] = "背景图宽度不能小于150像素";
            return $retData;
        }
        if ($sizeInfo[1] < 150) {
            $retData['code'] = ERRCODE_UPLOAD_FAILED;
            $retData['msg'] = "背景图高度不能小于150像素";
            return $retData;
        }

        $retData['data'] = array(
            'imgUrl' => "/FileUpload/ShareImg/".$uploadInfo['bg_image']['savename'],
            'saveName' => $uploadInfo['bg_image']['savename'],
        );

        return $retData;
    }

    /************************************ 运营配置 ************************************/

    public function operate()
    {
        $viewAssign = array();
        // 页面 title
        $viewAssign['title'] = "游戏配置 | 运营配置";
        $third = I('get.third');
        if (empty($third)) {
            $third = 'opeconf';
        }
        switch ($third) {
            // 运营配置
            case 'opeconf':
                $gamelogic = new GameconfLogic();
                $ret = $gamelogic->getOperateList();
                if ($ret['code'] == ERRCODE_SUCCESS) {
                    $viewAssign['opeConf'] = $ret['data']['placeList'];
                    $viewAssign['confList'] = $ret['data']['confList'];
                } else {
                    $viewAssign['errMsg'] = $ret['msg'];
                }
                $gameid = C('G_USER.gameid');
                if (isset($this->package_version[$gameid])) {
                    $viewAssign['packVer'] = $this->package_version[$gameid];
                }
                $viewAssign['pasteFlag'] = in_array(AUTH_OPER_GF_OPECONF_PASTE, C("G_USER.operate")) ? true : false;
                $viewAssign['deleteFlag'] = in_array(AUTH_OPER_GF_OPECONF_DELETE, C("G_USER.operate")) ? true : false;
                $viewAssign['shareFlag'] = in_array(AUTH_OPER_GF_OPECONF_SHARE, C("G_USER.operate")) ? true : null;
                $viewAssign['shareFlag'] = null;
                $viewAssign['horseFlag'] = in_array(AUTH_OPER_GF_OPECONF_HORSE, C("G_USER.operate")) ? true : null;
                $viewAssign['advFlag'] = in_array(AUTH_OPER_GF_OPECONF_ADV, C("G_USER.operate")) ? true : null;
                $viewAssign['cmsFlag'] = in_array(AUTH_OPER_GF_OPECONF_CMS, C("G_USER.operate")) ? true : null;
                $viewAssign['callAgentFlag'] = in_array(AUTH_OPER_GF_OPECONF_CALLAGENT, C("G_USER.operate")) ? true : null;
                $viewAssign['dailyRewardFlag'] = in_array(AUTH_OPER_GF_OPECONF_DAILYREWARD, C("G_USER.operate")) ? true : null;
                // 取得每日奖励钻石数量
                if (isset(PlaceConfigModel::$DailyRewardDiamond[C('G_USER.gameid')])) {
                    $num = PlaceConfigModel::$DailyRewardDiamond[C('G_USER.gameid')];
                } else {
                    $num = 1;
                }
                $viewAssign['diamond_num'] = $num;
                $displayPage = "opeconf";
                break;
            default:
                // 未知三级目录
                redirect('/Auth/logout');
        }
        $viewAssign["stime"] = I('request.stime');
        $viewAssign["etime"] = I('request.etime');
        $viewAssign["operate_type"] = intval(I('request.type'));
        $this->assign($viewAssign);
        $this->display($displayPage);
    }

    /**
     * 招募代理配置
     */
    public function ajaxSubmitCallAgent()
    {
        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => ''
        );

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_OPECONF_CALLAGENT, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }
        $gamelogic = new GameconfLogic();
        $modRet = $gamelogic->saveCallAgent(I('POST.'));
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $retData['code'] = ERRCODE_DATA_ERR;
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        $title = "";
        $mod = new DictPlaceModel();
        $modRet = $mod->getPlacePath(I('POST.confid'));
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $retData['code'] = ERRCODE_DATA_ERR;
            $retData['msg'] = '未找到配置路径';
            $this->ajaxReturn($retData);
        }
        $title = $modRet['data'];
        $postdata = I("POST.");
        $postdata['subGamelogTitle'] = $title;
        $postdata['subGamelogDescrip'] = '修改招募代理配置';
        set_operation("修改招募代理配置", $postdata, AUTH_OPER_GF_OPECONF_CALLAGENT);
        $this->ajaxReturn($retData);
    }

    /**
     * 保存广告图片
     */
    public function ajaxSubmitAdvImg()
    {
        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => ''
        );

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_OPECONF_ADV, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }
        $vldSer = new ValidatorService();
        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('pictime', 0, array(
                array('require', null, '未填写轮播时间'),
                array('integer', null, '轮播时间必须为整数'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }
        $gamelogic = new GameconfLogic();
        $modRet = $gamelogic->saveAdvImg(I('POST.'));
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $retData['code'] = ERRCODE_DATA_ERR;
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        $title = "";
        $mod = new DictPlaceModel();
        $modRet = $mod->getPlacePath(I('POST.confid'));
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $retData['code'] = ERRCODE_DATA_ERR;
            $retData['msg'] = '未找到配置路径';
            $this->ajaxReturn($retData);
        }
        $title = $modRet['data'];
        $postdata = I("POST.");
        $postdata['subGamelogTitle'] = $title;
        $postdata['subGamelogDescrip'] = '修改了广告配置';
        set_operation("修改广告图片配置", $postdata, AUTH_OPER_GF_OPECONF_ADV);
        $this->ajaxReturn($retData);
    }

    /**
     * 保存跑马灯
     */
    public function ajaxSubmitHorse()
    {
        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => ''
        );

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_OPECONF_HORSE, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }
        $vldSer = new ValidatorService();
        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('horsetime', 0, array(
                array('integer', null, '延迟时间必须为整数'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }
        $gamelogic = new GameconfLogic();
        $modRet = $gamelogic->saveHorse(I('POST.'));
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $retData['code'] = ERRCODE_DATA_ERR;
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        $title = "";
        $mod = new DictPlaceModel();
        $modRet = $mod->getPlacePath(I('POST.confid'));
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $retData['code'] = ERRCODE_DATA_ERR;
            $retData['msg'] = '未找到配置路径';
            $this->ajaxReturn($retData);
        }
        $title = $modRet['data'];
        $postdata = I("POST.");
        $postdata['subGamelogTitle'] = $title;
        $postdata['subGamelogDescrip'] = '修改了跑马灯配置(时间：'.I('POST.horsetime').' | '.implode(' | ', I('POST.horse')).')';
        set_operation("修改了跑马灯设置", $postdata, AUTH_OPER_GF_OPECONF_HORSE);
        $this->ajaxReturn($retData);
    }

    /**
     * 保存客服配置
     */
    public function ajaxSubmitCms()
    {
        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => ''
        );

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_OPECONF_CMS, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }
        $vldSer = new ValidatorService();
        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('weixintime', 0, array(
                array('require', null, '未填写轮播时间'),
                array('integer', null, '时间必须为整数'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }
        $gamelogic = new GameconfLogic();
        $modRet = $gamelogic->saveCms(I('POST.'));
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $retData['code'] = ERRCODE_DATA_ERR;
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }
        $cont = $modRet['data'];

        $title = "";
        $mod = new DictPlaceModel();
        $modRet = $mod->getPlacePath(I('POST.confid'));
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $retData['code'] = ERRCODE_DATA_ERR;
            $retData['msg'] = '未找到配置路径';
            $this->ajaxReturn($retData);
        }
        $title = $modRet['data'];
        $postdata = I("POST.");
        $postdata['subGamelogTitle'] = $title;
        $postdata['subGamelogDescrip'] = '修改了客服界面配置(轮播时间：'.I('post.weixintime').'|公众号文字：'.
                implode(",",I('post.weixin')).'|代理招募：'.implode(",",I('post.proxy')).')';
        set_operation("修改了客服界面设置", $postdata, AUTH_OPER_GF_OPECONF_CMS);
        $this->ajaxReturn($retData);
    }

    /**
     * 黏贴操作
     */
    public function ajaxPasteOperate()
    {
        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => ''
        );

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_OPECONF_PASTE, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $sourceid = (int) I('post.sourceid');
        $destid = (int) I('post.destid');
        if ($sourceid < 1 || $destid < 1) {
            $retData['code'] = ERRCODE_PARAM_INVALID;
            $retData['msg'] = '参数传递错误';
            $this->ajaxReturn($retData);
        }
        $gamelogic = new GameconfLogic();
        $modRet = $gamelogic->pasteConfig(I('POST.'));
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $retData['code'] = ERRCODE_DATA_ERR;
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        $title = "";
        $mod = new DictPlaceModel();
        $modRet = $mod->getPlacePath(I('POST.destid'));
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $retData['code'] = ERRCODE_DATA_ERR;
            $retData['msg'] = '未找到配置路径';
            $this->ajaxReturn($retData);
        }
        $title = $modRet['data'];
        $modRet = $mod->getPlacePath(I('POST.sourceid'));
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $retData['code'] = ERRCODE_DATA_ERR;
            $retData['msg'] = '未找到配置路径';
            $this->ajaxReturn($retData);
        }
        $postdata = I("POST.");
        $postdata['subGamelogTitle'] = $title;
        $postdata['subGamelogDescrip'] = '拷贝配置“'.$modRet['data'].'”粘贴到“'.$title.'”';
        set_operation("粘帖运营配置提示", $postdata, AUTH_OPER_GF_OPECONF_PASTE);
        $this->ajaxReturn($retData);
    }

    /**
     * 删除操作
     */
    public function ajaxDelOperate()
    {
        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => ''
        );

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_OPECONF_DELETE, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $id = (int) I('post.id');
        if ($id < 1) {
            $retData['code'] = ERRCODE_PARAM_INVALID;
            $retData['msg'] = '参数传递错误';
            $this->ajaxReturn($retData);
        }
        $gamelogic = new GameconfLogic();
        $modRet = $gamelogic->deleteConfig(I('POST.'));
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $retData['code'] = ERRCODE_DATA_ERR;
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        $title = "";
        $mod = new DictPlaceModel();
        $modRet = $mod->getPlacePath(I('POST.id'));
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $retData['code'] = ERRCODE_DATA_ERR;
            $retData['msg'] = '未找到配置路径';
            $this->ajaxReturn($retData);
        }
        $title = $modRet['data'];
        $postdata = I("POST.");
        $postdata['subGamelogTitle'] = $title;
        $postdata['subGamelogDescrip'] = '删除了配置信息';
        set_operation("删除运营配置", $postdata, AUTH_OPER_GF_OPECONF_DELETE);
        $this->ajaxReturn($retData);
    }

    /**
     * 奖励配置
     * @author daniel
     */
    public function opDailyReward()
    {
        // 操作权限
        $operate = C("G_USER.operate");
        $viewAssign = [
            'deleteFlag' => in_array(AUTH_OPER_GF_DAILYREWARD_MGR, $operate) ? true : null,
            'dailyRewardEditFlag' => in_array(AUTH_OPER_GF_DAILYREWARD_MGR, $operate) ? true : null,
        ];

        // EKEY_DAILYREWARD 兼容
        // --------start------
        if (true !== is_function_enable("EKEY_DAILYREWARD")) {
            $viewAssign['errMsg'] = "本游戏未兼容此功能， 更新至最新版本后才支持配置开启此功能";
        }
        // --------end--------

        // 页面title
        $viewAssign['title'] = "游戏配置 | 奖励配置";
        $gameId = C('G_USER.gameid');
        // 当前游戏id
        $firstId = empty(I('get.firstId')) ? $gameId : I('get.firstId');
        $viewAssign['curFirstId'] = $firstId;
        // 当前地区id,未传则为游戏id
        $placeId = empty(I('get.placeId')) ? $gameId : I('get.placeId');
        $viewAssign['curPlaceId'] = $placeId;
        // 获取地区树
        $gameConfLogic = new GameconfLogic();
        $areaInfo = $gameConfLogic->getPlaceTreeByGameId($gameId, $placeId, true);
        if (ERRCODE_SUCCESS !== $areaInfo['code']) {
            $viewAssign['errMsg'] = '获取地区树失败';
        }
        $viewAssign['placeTree'] = $areaInfo['data']['tree'];
        $viewAssign['pageHead'] = $areaInfo['data']['pageHead'];
        // 获取状态map，用于区分哪些地区存在配置项，那些无配置项
        $areaOperateLists = $gameConfLogic->getDailyRewardOperateList(true);
        if (ERRCODE_SUCCESS !== $areaOperateLists['code']) {
            $viewAssign['errMsg'] = '获取地区状态映射失败';
        }
        if (!empty($areaOperateLists['data']['confList']) && is_array($areaOperateLists['data']['confList'])) {
            $confLists = [];
            foreach ($areaOperateLists['data']['confList'] as $key => $confList) {
                if(isset($confList['dailyreward'])) {
                    $confLists[$key] = $confList['dailyreward'];
                }
            }
        }
        $viewAssign['validPlaceInfo'] = $confLists;
        // 获取地址id列表，并去除空元素
        $viewAssign['validPlace'] = array_keys(array_filter($confLists));
        // 取得每日奖励钻石数量
        if (isset(PlaceConfigModel::$DailyRewardDiamond[C('G_USER.gameid')])) {
            $num = PlaceConfigModel::$DailyRewardDiamond[C('G_USER.gameid')];
        } else {
            $num = 1;
        }
        $viewAssign['diamondNum'] = $num;

        $this->assign($viewAssign);
        $this->display('opDailyReward');
    }

    /**
     * 删除奖励配置
     * @author daniel
     */
    public function ajaxDelDailyReward()
    {
        $this->checkIsAjax();
        $retData = [
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        ];

        // EKEY_DAILYREWARD 兼容
        // --------start-------
        $enable = is_function_enable("EKEY_DAILYREWARD");
        if (!$enable) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '当前游戏暂未开通此功能';
            $retData['error'] = 'edition';
            $this->ajaxReturn($retData);
        }
        // --------end---------

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_DAILYREWARD_MGR, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校检未通过';
            $this->ajaxReturn($retData);
        }
        // 参数校检
        $validSer = new ValidatorService();
        $attr = I('post.', '', 'trim');
        $rules = [
            ['place_id', 0, [
                ['require', null, 'id参数缺失'],
                ['integer', null, 'id参数错误'],
            ]],
        ];
        $checkRet = $validSer->exce($attr, $rules);
        if (true !== $checkRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $checkRet;
            $this->ajaxReturn($retData);
        }
        // 判断是否为省包
        $placeId = $attr['place_id'];
        $gameLogic = new GameConfLogic();
        $removeRet = $gameLogic->removeDailyRewardConf($placeId);
        if ($removeRet['code'] !== ERRCODE_SUCCESS) {
            $retData['code'] = $removeRet['code'];
            $retData['msg'] = $removeRet['msg'];
            $this->ajaxReturn($retData);
        }
        $this->ajaxReturn($retData);
    }

    /**
     * 保存奖励配置
     * @author daniel
     */
    public function ajaxSubmitDailyReward()
    {
        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => ''
        );

        // EKEY_DAILYREWARD 兼容
        // --------start-------
        $enable = is_function_enable("EKEY_DAILYREWARD");
        if (!$enable) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '当前游戏暂未开通此功能';
            $retData['error'] = 'edition';
            $this->ajaxReturn($retData);
        }
        // --------end----------

        $vldSer = new ValidatorService();
        $postdata = I("POST.");
        $rules = array(
            array('club_diamond', 0, array(
                array('max', 1000, '亲友圈钻石奖励不能超过上限1000'),
            )),
            array('club_yuanbao', 0, array(
                array('max', 1000, '亲友圈元宝奖励不能超过上限1000'),
            )),
            array('no_club_diamond', 0, array(
                array('max', 1000, '非亲友圈钻石奖励不能超过上限1000'),
            )),
            array('no_club_yuanbao', 0, array(
                array('max', 1000, '非亲友圈元宝奖励不能超过上限1000'),
            )),
            array('confid', 0, array(
                array('require', null, '配置地址id错误'),
            )),
        );
        $vRet = $vldSer->exce($postdata, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_DAILYREWARD_MGR, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }
        $gamelogic = new GameconfLogic();
        $modRet = $gamelogic->saveDailyReward(I('POST.'));
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $retData['code'] = ERRCODE_DATA_ERR;
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        $mod = new DictPlaceModel();
        $modRet = $mod->getPlacePath(I('POST.confid'));
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $retData['code'] = ERRCODE_DATA_ERR;
            $retData['msg'] = '未找到配置路径';
            $this->ajaxReturn($retData);
        }
        $title = $modRet['data'];
        $postdata = I("POST.");
        $postdata['subGamelogTitle'] = $title;
        $postdata['subGamelogDescrip'] = '修改每日分享奖励配置';
        set_operation("修改每日分享奖励配置", $postdata, AUTH_ACCESS_GF_DAILYREWARD);
        $this->ajaxReturn($retData);
    }

    /************************************ 运营配置 - 登录广告配置 ************************************/
    /**
     * 登录广告配置
     * @author Daniel
     */
    public function opLoginAd()
    {
        $gameConfLogic = new GameconfLogic();

        // 页面title
        $viewAssign['title'] = "游戏配置 | 登录广告配置";
        $gameId = C('G_USER.gameid');
        // 当前游戏id
        $firstId = empty(I('get.firstId')) ? $gameId : I('get.firstId');
        $viewAssign['curFirstId'] = $firstId;
        // 当前地区id,未传则为游戏id
        $placeId = empty(I('get.placeId')) ? $gameId : I('get.placeId');
        $viewAssign['curPlaceId'] = $placeId;

        // 获取游戏地址树
        $placeTree = $gameConfLogic->getPlaceTreeByGameId($gameId, $placeId);
        if (ERRCODE_SUCCESS !== $placeTree['code']) {
            $viewAssign['errMsg'] = '获取地区树失败';
        }
        $viewAssign['placeTree'] = $placeTree['data']['tree'];
        $viewAssign['pageHead'] = $placeTree['data']['pageHead'];

        // 获取地区配置状态
        $validPlaceRet = $gameConfLogic->getLoginAdOperateList();
        if (ERRCODE_SUCCESS !== $validPlaceRet['code']) {
            $viewAssign['errMsg'] = '获取地区状态映射失败';
        }
        $validPlaceList = $validPlaceRet['data']['confList'];
        $viewAssign['validPlace'] = $validPlaceList;

        // 获取当前配置项
        $curConf = [];
        if (isset($validPlaceList[$placeId])) {
            $curConf = $validPlaceList[$placeId];
        }
        $viewAssign['confSwitch'] = $curConf['logadvswitch'] > 0 ? 1 : 0;
        $viewAssign['parentSwitch'] = $curConf['logadvswitch'] == -1 ? 0 : 1;
        $viewAssign['topLevel'] = I('get.firstId') == I('get.placeId') ? 1 : 0;

        // 检测权限
        $viewAssign['logadvFlag'] = in_array(AUTH_OPER_GF_OPECONF_LOGADV, C("G_USER.operate")) ? true : null;

        $this->assign($viewAssign);
        $this->display('opLoginAd');
    }

    /**
     * 登录广告配置
     */
    public function ajaxSubmitLogAdv()
    {
        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => ''
        );

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_OPECONF_LOGADV, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $vldSer = new ValidatorService();
        $postdata = I("POST.");
        $rules = array(
            array('confid', 0, array(
                array('numeric', null, '数据类型错误'),
            )),
            array('switch', 0, array(
                array('numeric', null, '数据类型错误'),
            )),
            array('parentSwitch', 0, array(
                array('numeric', null, '数据类型错误'),
            )),
        );
        $vRet = $vldSer->exce($postdata, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $gamelogic = new GameconfLogic();
        $modRet = $gamelogic->saveLogAdv($postdata);
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $retData['code'] = ERRCODE_DATA_ERR;
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        $title = "";
        $mod = new DictPlaceModel();
        $modRet = $mod->getPlacePath(I('POST.confid'));
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $retData['code'] = ERRCODE_DATA_ERR;
            $retData['msg'] = '未找到配置路径';
            $this->ajaxReturn($retData);
        }
        $title = $modRet['data'];
        $postdata['subGamelogTitle'] = $title;
        $postdata['subGamelogDescrip'] = '修改了登录广告配置';
        set_operation("修改登录广告配置", $postdata, AUTH_OPER_GF_OPECONF_LOGADV);
        $this->ajaxReturn($retData);
    }

    /**
     * 广告图片上传
     */
    public function ajaxAdvUploadImages()
    {
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // 上传路径，先存放于临时路劲，等待广告创建的时候再上传至图片服务器
        $uploadPath = ROOT_PATH."FileUpload/";
        if (!is_dir($uploadPath)) {
            if (!mkdir($uploadPath, 0777, true)) {
                $retData['code'] = ERRCODE_SYSTEM;
                $retData['msg'] = "mkdir {$uploadPath} failed";
                $this->ajaxReturn($retData);
            }
        }
        // upload 类获取上传文件
        $config = array(
            'maxSize'    => C('IMG_MAX_UPLOAD_SIZE') * 1024 * 1024,
            'rootPath'   => $uploadPath,
            'exts'       => array('jpg', 'jpeg', 'png', 'gif'),
            'subName'    => false,
            'saveName'   => array('uniqid')
        );
        $upload = new \Think\Upload($config);
        $uploadInfo = $upload->upload();
        if (false === $uploadInfo || empty( $uploadInfo )  ) {
            $retData['code'] = ERRCODE_UPLOAD_FAILED;
            $retData['msg'] = $upload->getError();
            $this->ajaxReturn($retData);
        }

        $uploadData = array_shift($uploadInfo);
        // 获取图片宽高
        $sizeInfo = getimagesize($uploadPath.$uploadData['savename']);

        $retData['data'] = array(
            'imgUrl' => "/FileUpload/".$uploadData['savename'],
            'saveName' => $uploadData['savename'],
        );

        $this->ajaxReturn($retData);
    }

    /************************************ 运营配置 - 朋友圈-分享配置 ************************************/

    /**
     * 朋友圈-分享配置
     * @author Carter
     */
    public function opShareCont()
    {
        $viewAssign = array();

        $gcnfLgc = new GameconfLogic();
        $contMod = new GameShareModel();

        // 操作权限
        $oper = C('G_USER.operate');
        $viewAssign['editFlag'] = in_array(AUTH_OPER_GF_SHARECONT_EDIT, $oper) ? true : null;

        // 页面 title
        $viewAssign['title'] = "游戏配置 | 运营配置";

        $gameId = C('G_USER.gameid');

        // 朋友圈-分享配置所包括的分享功能
        $shareSource = array(
            $contMod::SOURCE_HALL_NOAWARD,
            $contMod::SOURCE_HALL_AWARD,
            $contMod::SOURCE_DIAMOND,
            $contMod::SOURCE_CLUB,
        );

        // 当前游戏id
        $firstId = I('get.firstId');
        if (empty($firstId)) {
            $firstId = $gameId;
        }
        $viewAssign['firstId'] = $firstId;

        // 不允许跨游戏修改
        if ($gameId != $firstId) {
            redirect('/Gameconf/opShareCont');
        }

        // 当前地区id，未传则为游戏id
        $placeId = I('get.placeId');
        if (empty($placeId)) {
            $placeId = $gameId;
        }
        $viewAssign['placeId'] = $placeId;

        // 通过游戏id获取地区树
        $lgcRet = $gcnfLgc->getPlaceTreeByGameId($gameId, $placeId, false);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $viewAssign['errMsg'] = '获取地区树失败';
        }
        $viewAssign['placeTree'] = $lgcRet['data']['tree'];
        $viewAssign['pageHead'] = $lgcRet['data']['pageHead'];

        // 获取状态map，用于区分哪些地区存在配置项，哪些无配置项
        $lgcRet = $gcnfLgc->getGConfShareContValidPlace($gameId, $shareSource);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $viewAssign['errMsg'] = '获取地区状态映射失败';
        }
        $viewAssign['validPlace'] = $lgcRet['data'];

        // 分享功能 map
        $viewAssign['sourceMap'] = $contMod->sourceMap;

        // 分享方式 map
        $viewAssign['shareTypeMap'] = $contMod->shareTypeMap;

        // 获取配置内容
        $attr = array(
            'place_id' => $placeId,
            'source' => $shareSource,
        );
        $field = 'id,source,share_type,title,desc,image,address,qrcode_x,qrcode_y';
        $modRet = $contMod->queryGameShareByAttr($attr, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign['conf'] = array_combine(
                array_column($modRet['data'], 'source'),
                $modRet['data']
            );
        }

        // 复制id
        $copyPlaceId = cookie('share_cont_copy_id');
        if (is_null($copyPlaceId) || $copyPlaceId == $placeId) {
            $viewAssign['copyPlaceId'] = 0;
        } else {
            $viewAssign['copyPlaceId'] = $copyPlaceId;
            $viewAssign['copyPlaceTitle'] = cookie('share_cont_copy_title');
        }

        // icon url 前缀
        $viewAssign['imgUrlPrefix'] = C('RESOURCE_SERVER_IPHOST').':'.C('RESOURCE_SERVER_PORT').'/';

        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * 分享内容进行缩略图上传
     * @author Carter
     */
    public function ajaxShareUploadThumbImg()
    {
        $this->checkIsAjax();
        $this->ajaxReturn($this->_shareUploadThumbImg());
    }

    /**
     * 分享内容进行背景图上传
     * @author Carter
     */
    public function ajaxShareUploadBgImg()
    {
        $this->checkIsAjax();
        $this->ajaxReturn($this->_shareUploadBgImg());
    }

    /**
     * 朋友圈-分享配置，设置一个复制cookie，用于页面切换时的粘贴功能
     * @author Carter
     */
    public function ajaxShareContSetCopyCookie()
    {
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // 正式服做负载均衡涉及session共享问题，这里采用cookie
        cookie('share_cont_copy_id', I('post.place_id'));
        cookie('share_cont_copy_title', I('post.page_head'));

        $this->ajaxReturn($retData);
    }

    /**
     * 粘贴朋友圈-分享配置
     * @author Carter
     */
    public function ajaxPasteShareCont()
    {
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_SHARECONT_EDIT, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $vldSer = new ValidatorService();
        $confLgc = new GameconfLogic();

        // 参数校验
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('copy_id', 0, array(
                array('require', null, '粘贴id缺失'),
                array('integer', null, '参数错误'),
            )),
            array('place_id', 0, array(
                array('require', null, '地区id缺失'),
                array('integer', null, '参数错误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $gameId = C('G_USER.gameid');

        $lgcRet = $confLgc->pasteGameShareConf($gameId, $attr['copy_id'], $attr['place_id']);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("朋友圈分享内容 {$attr['copy_id']}粘贴到 {$attr['place_id']}", $lgcRet['data'], AUTH_OPER_GF_SHARECONT_EDIT);

        // 修改内容，更新分享后台配置文件
        $lgcRet = $confLgc->refrashShareConfFile($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 销毁相关cookie
        cookie('share_cont_copy_id', null);
        cookie('share_cont_copy_title', null);

        $this->ajaxReturn($retData);
    }

    /**
     * 添加朋友圈-分享配置
     * @author Carter
     */
    public function ajaxAddShareCont()
    {
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_SHARECONT_EDIT, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $vldSer = new ValidatorService();
        $contMod = new GameShareModel();
        $confLgc = new GameconfLogic();

        // 朋友圈-分享配置所包括的分享功能
        $shareSource = array(
            $contMod::SOURCE_HALL_NOAWARD,
            $contMod::SOURCE_HALL_AWARD,
            $contMod::SOURCE_DIAMOND,
            $contMod::SOURCE_CLUB,
        );

        // 参数校验
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('first_id', 0, array(
                array('require', null, '游戏id缺失'),
                array('integer', null, '参数错误'),
            )),
            array('place_id', 0, array(
                array('require', null, '地区id缺失'),
                array('integer', null, '参数错误'),
            )),
            array('source', 0, array(
                array('require', null, '分享功能缺失'),
                array('in', implode(",", $shareSource), '参数错误'),
            )),
            array('share_type', 0, array(
                array('require', null, '分享方式缺失'),
                array('in', implode(",", array_keys($contMod->shareTypeMap)), '参数错误'),
            )),
            array('title', 1, array(
                array('len_max', "256", '标题不能超过 256 个字符'),
            )),
            array('desc', 1, array(
                array('len_max', "512", '描述不能超过 512 个字符'),
            )),
            array('cont', 1, array(
                array('len_max', "512", '分享内容不能超过 512 个字符'),
            )),
            array('address', 1, array(
                array('len_max', "512", '二维码地址不能超过 512 个字符'),
            )),
            array('qrcode_x', 1, array(
                array('numeric', null, '二维码x锚点必须为一个数字'),
                array('max', '1', '二维码x锚点不能大于1'),
                array('min', '0', '二维码x锚点不能小于0'),
            )),
            array('qrcode_y', 1, array(
                array('numeric', null, '二维码y锚点必须为一个数字'),
                array('max', '1', '二维码y锚点不能大于1'),
                array('min', '0', '二维码y锚点不能小于0'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        // 系统分享校验分享语句
        if ($contMod::SHARE_TYPE_SYS == $attr['share_type']) {
            if (!preg_match('/\[https?:\/\/.*\]/i', $attr['cont'])) {
                $retData['code'] = ERRCODE_VALIDATE_FAILED;
                $retData['msg'] = "分享语句必须包含URL地址";
                $this->ajaxReturn($retData);
            }
        }

        $gameId = C('G_USER.gameid');

        $lgcRet = $confLgc->addGameShareConf($gameId, $attr);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("添加分享内容 {$lgcRet['data']['id']}", $lgcRet['data'], AUTH_OPER_GF_SHARECONT_EDIT);

        // 修改内容，更新分享后台配置文件
        $lgcRet = $confLgc->refrashShareConfFile($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        $this->ajaxReturn($retData);
    }

    /**
     * 修改朋友圈-分享配置
     * @author Carter
     */
    public function ajaxEditShareCont()
    {
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_SHARECONT_EDIT, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $vldSer = new ValidatorService();
        $contMod = new GameShareModel();
        $confLgc = new GameconfLogic();

        // 朋友圈-分享配置所包括的分享功能
        $shareSource = array(
            $contMod::SOURCE_HALL_NOAWARD,
            $contMod::SOURCE_HALL_AWARD,
            $contMod::SOURCE_DIAMOND,
            $contMod::SOURCE_CLUB,
        );

        // 参数校验
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('id', 0, array(
                array('require', null, 'id参数缺失'),
                array('integer', null, 'id参数错误'),
            )),
            array('source', 0, array(
                array('require', null, '分享功能缺失'),
                array('in', implode(",", $shareSource), '参数错误'),
            )),
            array('share_type', 0, array(
                array('require', null, '分享方式缺失'),
                array('in', implode(",", array_keys($contMod->shareTypeMap)), '参数错误'),
            )),
            array('title', 1, array(
                array('len_max', "256", '标题不能超过 256 个字符'),
            )),
            array('desc', 1, array(
                array('len_max', "512", '描述不能超过 512 个字符'),
            )),
            array('cont', 1, array(
                array('len_max', "512", '分享内容不能超过 512 个字符'),
            )),
            array('address', 1, array(
                array('len_max', "512", '二维码地址不能超过 512 个字符'),
            )),
            array('qrcode_x', 1, array(
                array('numeric', null, '二维码x锚点必须为一个数字'),
                array('max', '1', '二维码x锚点不能大于1'),
                array('min', '0', '二维码x锚点不能小于0'),
            )),
            array('qrcode_y', 1, array(
                array('numeric', null, '二维码y锚点必须为一个数字'),
                array('max', '1', '二维码y锚点不能大于1'),
                array('min', '0', '二维码y锚点不能小于0'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        // 系统分享校验分享语句
        if ($contMod::SHARE_TYPE_SYS == $attr['share_type']) {
            if (!preg_match('/\[https?:\/\/.*\]/i', $attr['cont'])) {
                $retData['code'] = ERRCODE_VALIDATE_FAILED;
                $retData['msg'] = "分享语句必须包含URL地址";
                $this->ajaxReturn($retData);
            }
        }

        $gameId = C('G_USER.gameid');

        $lgcRet = $confLgc->editGameShareConf($gameId, $attr['id'], $attr);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("修改分享内容 id {$attr['id']}", $lgcRet['data'], AUTH_OPER_GF_SHARECONT_EDIT);

        // 修改内容，更新分享后台配置文件
        $lgcRet = $confLgc->refrashShareConfFile($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        $this->ajaxReturn($retData);
    }

    /**
     * 删除朋友圈-分享配置
     * @author Carter
     */
    public function ajaxDelShareCont()
    {
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_SHARECONT_EDIT, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $vldSer = new ValidatorService();
        $confLgc = new GameconfLogic();

        // 参数校验
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('place_id', 0, array(
                array('require', null, 'id参数缺失'),
                array('integer', null, 'id参数错误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $gameId = C('G_USER.gameid');

        $lgcRet = $confLgc->removeGameShareConf($gameId, $attr['place_id']);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("删除分享内容 id {$attr['place_id']}", $lgcRet['data'], AUTH_OPER_GF_SHARECONT_EDIT);

        // 修改内容，更新分享后台配置文件
        $lgcRet = $confLgc->refrashShareConfFile($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        $this->ajaxReturn($retData);
    }

    /************************************ 运营配置 - 好友/群-分享配置 ************************************/

    /**
     * 好友/群-分享配置
     * @author Carter
     */
    public function opFriendShare()
    {
        $viewAssign = array();

        $gcnfLgc = new GameconfLogic();
        $contMod = new GameShareModel();

        // 操作权限
        $oper = C('G_USER.operate');
        $viewAssign['editFlag'] = in_array(AUTH_OPER_GF_FRIENDSHARE_EDIT, $oper) ? true : null;

        // 页面 title
        $viewAssign['title'] = "运营配置 | 好友/群-分享配置";

        $gameId = C('G_USER.gameid');

        // 好友/群-分享配置所包括的分享功能
        $shareSource = array(
            $contMod::SOURCE_FRIEND_NOAWARD, // 大厅分享给好友（无奖励）
            $contMod::SOURCE_FRIEND_AWARD, // 领取钻石-分享给好友
            $contMod::SOURCE_FRIEND_CLUB, // 俱乐部分享给好友
        );

        // 当前游戏id
        $firstId = I('get.firstId');
        if (empty($firstId)) {
            $firstId = $gameId;
        }
        $viewAssign['firstId'] = $firstId;

        // 不允许跨游戏修改
        if ($gameId != $firstId) {
            redirect('/Gameconf/opFriendShare');
        }

        // 当前地区id，未传则为游戏id
        $placeId = I('get.placeId');
        if (empty($placeId)) {
            $placeId = $gameId;
        }
        $viewAssign['placeId'] = $placeId;

        // 通过游戏id获取地区树
        $lgcRet = $gcnfLgc->getPlaceTreeByGameId($gameId, $placeId, false);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $viewAssign['errMsg'] = '获取地区树失败';
        }
        $viewAssign['placeTree'] = $lgcRet['data']['tree'];
        $viewAssign['pageHead'] = $lgcRet['data']['pageHead'];

        // 获取状态map，用于区分哪些地区存在配置项，哪些无配置项
        $lgcRet = $gcnfLgc->getFriendShareContValidPlace($gameId, $shareSource);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $viewAssign['errMsg'] = '获取地区状态映射失败';
        }
        $viewAssign['validPlace'] = $lgcRet['data'];

        // 分享功能 map
        $viewAssign['sourceMap'] = $contMod->sourceMap;

        // 获取配置内容
        $attr = array(
            'place_id' => $placeId,
            'source' => $shareSource,
        );
        $field = 'id,source,title,desc';
        $modRet = $contMod->queryGameShareByAttr($attr, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign['conf'] = array_combine(
                array_column($modRet['data'], 'source'),
                $modRet['data']
            );
        }

        // 复制id
        $copyPlaceId = cookie('friend_share_copy_id');
        if (is_null($copyPlaceId) || $copyPlaceId == $placeId) {
            $viewAssign['copyPlaceId'] = 0;
        } else {
            $viewAssign['copyPlaceId'] = $copyPlaceId;
            $viewAssign['copyPlaceTitle'] = cookie('friend_share_copy_title');
        }

        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * 好友/群-分享配置，设置一个复制cookie，用于页面切换时的粘贴功能
     * @author Carter
     */
    public function ajaxFriendShareSetCopyCookie()
    {
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // 正式服做负载均衡涉及session共享问题，这里采用cookie
        cookie('friend_share_copy_id', I('post.place_id'));
        cookie('friend_share_copy_title', I('post.page_head'));

        $this->ajaxReturn($retData);
    }

    /**
     * 粘贴好友/群-分享配置
     * @author Carter
     */
    public function ajaxPasteFriendShare()
    {
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_FRIENDSHARE_EDIT, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $vldSer = new ValidatorService();
        $confLgc = new GameconfLogic();

        // 参数校验
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('copy_id', 0, array(
                array('require', null, '粘贴id缺失'),
                array('integer', null, '参数错误'),
            )),
            array('place_id', 0, array(
                array('require', null, '地区id缺失'),
                array('integer', null, '参数错误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $gameId = C('G_USER.gameid');

        $lgcRet = $confLgc->pasteFriendShareConf($gameId, $attr['copy_id'], $attr['place_id']);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("朋友圈分享内容 {$attr['copy_id']}粘贴到 {$attr['place_id']}", $lgcRet['data'], AUTH_OPER_GF_FRIENDSHARE_EDIT);

        // 修改内容，更新分享后台配置文件
        $lgcRet = $confLgc->refrashShareConfFile($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 销毁相关cookie
        cookie('friend_share_copy_id', null);
        cookie('friend_share_copy_title', null);

        $this->ajaxReturn($retData);
    }

    /**
     * 好友/群-分享配置添加记录
     * @author Carter
     */
    public function ajaxAddFriendShare()
    {
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_FRIENDSHARE_EDIT, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $vldSer = new ValidatorService();
        $contMod = new GameShareModel();
        $confLgc = new GameconfLogic();

        // 朋友圈-分享配置所包括的分享功能
        $shareSource = array(
            $contMod::SOURCE_FRIEND_NOAWARD,
            $contMod::SOURCE_FRIEND_AWARD,
            $contMod::SOURCE_FRIEND_CLUB,
        );

        // 参数校验
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('first_id', 0, array(
                array('require', null, '游戏id缺失'),
                array('integer', null, '参数错误'),
            )),
            array('place_id', 0, array(
                array('require', null, '地区id缺失'),
                array('integer', null, '参数错误'),
            )),
            array('source', 0, array(
                array('require', null, '分享功能缺失'),
                array('in', implode(",", $shareSource), '参数错误'),
            )),
            array('share_type', 0, array(
                array('exclude', null, '非法参数'),
            )),
            array('title', 0, array(
                array('len_max', "256", '标题不能超过 256 个字符'),
            )),
            array('desc', 0, array(
                array('len_max', "512", '描述不能超过 512 个字符'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $gameId = C('G_USER.gameid');

        $lgcRet = $confLgc->addFriendShareConf($gameId, $attr);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("好友/群添加分享内容 {$lgcRet['data']['id']}", $lgcRet['data'], AUTH_OPER_GF_FRIENDSHARE_EDIT);

        // 修改内容，更新分享后台配置文件
        $lgcRet = $confLgc->refrashShareConfFile($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        $this->ajaxReturn($retData);
    }

    /**
     * 好友/群-分享配置修改
     * @author Carter
     */
    public function ajaxEditFriendShare()
    {
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_FRIENDSHARE_EDIT, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $vldSer = new ValidatorService();
        $contMod = new GameShareModel();
        $confLgc = new GameconfLogic();

        // 朋友圈-分享配置所包括的分享功能
        $shareSource = array(
            $contMod::SOURCE_FRIEND_NOAWARD,
            $contMod::SOURCE_FRIEND_AWARD,
            $contMod::SOURCE_FRIEND_CLUB,
        );

        // 参数校验
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('id', 0, array(
                array('require', null, 'id参数缺失'),
                array('integer', null, 'id参数错误'),
            )),
            array('source', 0, array(
                array('require', null, '分享功能缺失'),
                array('in', implode(",", $shareSource), '参数错误'),
            )),
            array('share_type', 0, array(
                array('exclude', null, '非法参数'),
            )),
            array('title', 0, array(
                array('len_max', "256", '标题不能超过 256 个字符'),
            )),
            array('desc', 0, array(
                array('len_max', "512", '描述不能超过 512 个字符'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $gameId = C('G_USER.gameid');

        $lgcRet = $confLgc->editFriendShareConf($gameId, $attr['id'], $attr);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("好友/群修改分享配置 id {$attr['id']}", $lgcRet['data'], AUTH_OPER_GF_FRIENDSHARE_EDIT);

        // 修改内容，更新分享后台配置文件
        $lgcRet = $confLgc->refrashShareConfFile($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        $this->ajaxReturn($retData);
    }

    /**
     * 删除好友/群-分享配置
     * @author Carter
     */
    public function ajaxDelFriendShare()
    {
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_FRIENDSHARE_EDIT, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $vldSer = new ValidatorService();
        $confLgc = new GameconfLogic();

        // 参数校验
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('place_id', 0, array(
                array('require', null, 'id参数缺失'),
                array('integer', null, 'id参数错误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $gameId = C('G_USER.gameid');

        $lgcRet = $confLgc->removeFriendShareConf($gameId, $attr['place_id']);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("好友/群分享删除配置 id {$attr['place_id']}", $lgcRet['data'], AUTH_OPER_GF_FRIENDSHARE_EDIT);

        // 修改内容，更新分享后台配置文件
        $lgcRet = $confLgc->refrashShareConfFile($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        $this->ajaxReturn($retData);
    }

    /************************************ 运营配置 - 绑定手机奖励配置 ************************************/

    /**
     * 绑定手机奖励配置
     * @author daniel
     */
    public function opBindPhone()
    {
        $viewAssign['title'] = '运营配置|绑定手机奖励配置';
        $gameId = C('G_USER.gameid');
        $displayPage = 'opBindPhone';
        $gameConfLogic = new GameconfLogic();
        $bindPhoneRet = $gameConfLogic->getBindPhoneConf($gameId);
        if ($bindPhoneRet['code'] !== ERRCODE_SUCCESS) {
            $viewAssign['errMsg'] = $bindPhoneRet['msg'];
            $this->assign($viewAssign);
            $this->display($displayPage);
            return;
        }
        $viewAssign['option'] = $bindPhoneRet['data']['option'];
        $viewAssign['value'] = empty($bindPhoneRet['data']['value']) ? '' : $bindPhoneRet['data']['value'];
        $this->assign($viewAssign);
        $this->display($displayPage);
    }

    /**
     * 修改手机奖励配置
     * @author daniel
     */
    public function ajaxEditBindPhone()
    {
        $this->checkIsAjax();

        $validSer = new ValidatorService();

        // 参数校验
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('type', 1, array(
                array('integer', null, 'type参数错误'),
                array('in', '0,10008,10009', 'type为空'),
            )),
            array('num', 0, array(
                array('integer', null, 'num参数错误'),
                array('require', null, '数量不能为空'),
                array('min', 1, '数量不能为空'),
            ))
        );
        $vRet = $validSer->exce($attr, $rules);
        if (true !== $vRet || $attr['num'] === 0 || empty($attr['type']) !== empty($attr['num'])) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = '参数错误';
            $this->ajaxReturn($retData);
        }

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );
        $gameId = C('G_USER.gameid');
        $gameConfLogic = new GameconfLogic();
        $savePhoneRet = $gameConfLogic->saveBindPhoneConf($gameId, $attr);
        if ($savePhoneRet['code'] !== ERRCODE_SUCCESS) {
            $retData['code'] = $savePhoneRet['code'];
            $retData['msg'] = $savePhoneRet['msg'];
            $this->ajaxReturn($retData);
        }
        $this->ajaxReturn($retData);

    }

    /************************************ 活动配置 ************************************/

    /**
     * 限时免钻
     */
    public function ajaxSaveProDio()
    {
        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => ''
        );

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_PROFREEDIO_MGR, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }
        $vldSer = new ValidatorService();
        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('starttime', 0, array(
                array('require', null, '未填写开始时间'),
                array('date', null, '开始时间日期格式有误'),
            )),
            array('endtime', 0, array(
                array('require', null, '未填写结束时间'),
                array('date', null, '结束时间日期格式有误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }
        $gamelogic = new GameconfLogic();
        $modRet = $gamelogic->setFreeDiamod(I('POST.'));
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $retData['code'] = ERRCODE_DB_UPDATE_ERR;
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }
        $postdata = I("POST.");
        $postdata['subGamelogTitle'] = '限时免钻';
        $postdata['subGamelogDescrip'] = '修改了限时免钻配置信息(开始时间：'.I('POST.starttime').' |结束时间： '.I('POST.endtime').' | '.(I('POST.active') == 1?'开启':'结束').')';
        set_operation("修改限时免钻配置", $postdata, AUTH_OPER_GF_PROFREEDIO_MGR);
        $this->ajaxReturn($retData);
    }

    /**
     * 元宝掉落配置
     */
    public function ajaxSaveRedGoldConf()
    {
        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => ''
        );

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_PROREDPACK_DROPRATE, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }
        $vldSer = new ValidatorService();
        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('low4', 0, array(
                array('require', null, '未填写4局元宝掉落下限'),
                array('integer', null, '元宝数量必须是整数'),
                array('max', $_POST['high4'], '元宝掉落数量下限不能超过上限'),
                array('unequal', $_POST['high4'], '元宝掉落数量下限不能等于上限'),
            )),
            array('high4', 0, array(
                array('require', null, '未填写4局元宝掉落上限'),
                array('integer', null, '元宝数量必须是整数'),
            )),
            array('low8', 0, array(
                array('require', null, '未填写8局元宝掉落下限'),
                array('integer', null, '元宝数量必须是整数'),
                array('max', $_POST['high8'], '元宝掉落数量下限不能超过上限'),
                array('unequal', $_POST['high8'], '元宝掉落数量下限不能等于上限')
            )),
            array('high8', 0, array(
                array('require', null, '未填写8局元宝掉落上限'),
                array('integer', null, '元宝数量必须是整数'),
            )),
            array('low16', 0, array(
                array('require', null, '未填写16局元宝掉落下限'),
                array('integer', null, '元宝数量必须是整数'),
                array('max', $_POST['high16'], '元宝掉落数量下限不能超过上限'),
                array('unequal', $_POST['high16'], '元宝掉落数量下限不能等于上限')
            )),
            array('high16', 0, array(
                array('require', null, '未填写16局元宝掉落上限'),
                array('integer', null, '元宝数量必须是整数'),
            )),
            array('rate2', 0, array(
                array('require', null, '未填写2人局元宝掉落比率'),
                array('integer', null, '2人局掉落比率必须是整数'),
                array('between', "0,100", "比率必须在0到100之间"),
            )),
            array('rate3', 0, array(
                array('require', null, '未填写3人局元宝掉落比率'),
                array('integer', null, '3人局掉落比率必须是整数'),
                array('between', "0,100", "比率必须在0到100之间"),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }
        $gamelogic = new GameconfLogic();
        $modRet = $gamelogic->setRedpack(I('POST.'));
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $retData['code'] = ERRCODE_DB_UPDATE_ERR;
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }
        $postdata = I("POST.");
        $postdata['subGamelogTitle'] = '兑换红包';
        $postdata['subGamelogDescrip'] = '修改了元宝掉落配置信息(4局掉落上下限：'.I('post.low4').'~'.I('post.high4').
                ' | 8局掉落上下限：'.I('post.low8').'~'.I('post.high8').' | 16局掉落上下限：'.I('post.low16').'~'.I('post.high16').' | 2人比率：'.I('post.rate2').' | 3人比率：'.I('post.rate3').')';
        set_operation("修改元宝掉落配置", $postdata, AUTH_OPER_GF_PROREDPACK_DATE);
        $this->ajaxReturn($retData);
    }

    /**
     * 元宝掉落重置
     * @author daniel
     */
    public function ajaxResetRedGoldConf()
    {
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => ''
        );
        // 检查操作权限
        if (!in_array(AUTH_OPER_GF_PROREDPACK_DATE, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }
        $gameLogic = new GameconfLogic();
        $setRetConf = $gameLogic->resetRedConf();
        if ($setRetConf['code'] !== ERRCODE_SUCCESS) {
            $retData['code'] = ERRCODE_DB_UPDATE_ERR;
            $retData['msg'] = $setRetConf['msg'];
            $this->ajaxReturn($retData);
        }
        $this->ajaxReturn($retData);
    }

    /**
     * 活动日期配置
     */
    public function ajaxSaveRedDateConf()
    {
        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => ''
        );

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_PROREDPACK_DATE, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }
        $vldSer = new ValidatorService();
        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('redeemcodeday', 1, array(
                array('require', null, '未填写兑换码有效天数'),
                array('integer', null, '有效天数必须是整数'),
            )),
            array('redeemcodehour', 1, array(
                array('integer', null, '有效小时必须是整数'),
                array('between', "0,23", "小時必须在0到23之间"),
            )),
            array('redeemcodemin', 1, array(
                array('integer', null, '有效分钟必须是整数'),
                array('between', "0,59", "分鐘必须在0到59之间"),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }
        $gamelogic = new GameconfLogic();
        $modRet = $gamelogic->setRedConf(I('POST.'));
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $retData['code'] = ERRCODE_DB_UPDATE_ERR;
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }
        $postdata = I("POST.");
        $postdata['subGamelogTitle'] = '兑换红包';
        $postdata['subGamelogDescrip'] = '修改了兑换红包活动日期配置信息('.(I('post.active')==1?'开启':'结束').
                ' | '.I('post.redeemcodeday').'天 | '.I('post.redeemcodehour').'时 | '.I('post.redeemcodemin').'分)';
        set_operation("修改兑换红包活动日期配置", $postdata, AUTH_OPER_GF_PROREDPACK_DATE);
        $this->ajaxReturn($retData);
    }

    /**
     * 新手红包
     */
    public function ajaxSaveNewRedPack()
    {
        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => ''
        );

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_PRONEWREDPACK_MGR, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }
        $gamelogic = new GameconfLogic();
        $modRet = $gamelogic->setNewRedPack(I('POST.'));
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $retData['code'] = ERRCODE_DB_UPDATE_ERR;
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }
        $postdata = I("POST.");
        $postdata['subGamelogTitle'] = '新手红包';
        $postdata['subGamelogDescrip'] = '修改了新手红包配置信息(标题：'.I('post.title').'|完成局数：'.I('post.record')
                .'|对应公众号：'.I('post.code').'|限制地区文字：'.I('post.area').'|前端是否显示时间：'.(I('post.display')==1?'显示':'隐藏').
                '|开始时间：'.I('post.stime').'|结束时间：'.I('post.etime').'|活动是否开启：'.(I('post.active')==1?'开启':'关闭').')';
        set_operation("修改新手红包配置", $postdata, AUTH_OPER_GF_PRONEWREDPACK_MGR);
        $this->ajaxReturn($retData);
    }

    /**
     * 新年拉新活动
     */
    public function ajaxSaveActInviteConf()
    {
        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => ''
        );

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_ACTINVITEFRD_MGR, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }
        $logarr = array();
        $data = array();
        foreach (I('POST.conf') as $v) {
            $reward = array();
            $rewardlog = array();
            for ($ix = 1; $ix <=5; $ix++) {
                if ($v['type'.$ix] > 0 && intval($v['val'.$ix]) > 0) {
                    $reward[] = $v['type'.$ix].':'.intval($v['val'.$ix]);
                    $rewardlog[] = $this->reward_reward_type_map[$v['type'.$ix]].':'.intval($v['val'.$ix]).'个';
                }
            }
            if ($v['event_num'] > 0 && count($reward) > 0) {
                $rewardval = implode(",", $reward);
                $data[] = array('num'=>$v['event_num'], 'reward'=>$rewardval);
                $logarr[] = '人数：'.$v['event_num'].",".implode(',', $rewardlog);
            }
        }
        $dt = array();
        $dt['status'] = I('POST.status');
        $dt['starttime'] = I('POST.starttime');
        $dt['endtime'] = I('POST.endtime');
        $dt['reward'] = $data;
        $gamelogic = new GameconfLogic();
        $modRet = $gamelogic->setActInviteFriendConf($dt);
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $retData['code'] = ERRCODE_DB_UPDATE_ERR;
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }
        $postdata = I("POST.");
        $postdata['subGamelogTitle'] = '新年拉新的邀请好友奖励';
        $postdata['subGamelogDescrip'] = '修改了新年拉新的邀请好友奖励配置信息('.implode(' | ',$logarr).')';
        set_operation("修改新年拉新的邀请好友奖励配置", $postdata, AUTH_OPER_GF_ACTINVITEFRD_MGR);
        $this->ajaxReturn($retData);
    }

    /**
     * 活动配置页面
     */
    public function project()
    {
        $viewAssign = array();
        // 页面 title
        $viewAssign['title'] = "游戏配置 | 活动配置";
        $third = I('get.third');
        if (empty($third)) {
            $third = 'proredpack';
        }
        switch ($third) {
            case 'proredpack':
                $displayPage = "proredpack";

                // 检查操作权限
                $viewAssign['dateFlag'] = in_array(AUTH_OPER_GF_PROREDPACK_DATE, C("G_USER.operate")) ? true : null;
                $viewAssign['droprateFlag'] = in_array(AUTH_OPER_GF_PROREDPACK_DROPRATE, C("G_USER.operate")) ? true : null;

                $gameLogic = new GameconfLogic();
                // 判断元宝掉落是否配置
                $YuanBaoStatus = $gameLogic->getValid('coin_certificate');
                if ($YuanBaoStatus['code'] !== ERRCODE_SUCCESS) {
                    $viewAssign['errMsg'] = $YuanBaoStatus['msg'];
                    break;
                }
                $YuanBaoStatusConf = $YuanBaoStatus['data'];
                if ($YuanBaoStatusConf === -1 || $YuanBaoStatusConf === 0) {
                    // 元宝掉落时间关闭
                    $viewAssign['active0'] = 'checked';
                    $viewAssign['tab_date_conf'] = 'active';
                    $viewAssign['tab_gold_drop'] = '';
                    $viewAssign['page_date_conf'] = 'active in';
                    $viewAssign['page_gold_drop'] = '';
                    break;
                }
                $gameConfRet = $gameLogic->getRedpack();
                if ($gameConfRet['code'] !== ERRCODE_SUCCESS) {
                    $viewAssign['errMsg'] = $gameConfRet['msg'];
                    break;
                }
                $goldConfList = $gameConfRet['data']['list'];
                $goldConf = $goldConfList['gold'];
                /**
                 * 元宝掉落默认配置如下:
                 *   2:50|3:75|4:100$4:50~100|8:50~100|16:100~200|32:100~200
                 *   - $为多人局元宝比率配置和元宝掉落数量上下限配置的分隔符
                 *   - 2人局获得元宝比率: 50%
                 *   - 3人局获得元宝比率: 75%
                 *   - 4人局获得元宝比率: 100%
                 *     - 4人局完成4轮牌局元宝掉落下限50 上线100
                 *     - 4人局完成8轮牌局元宝掉落下限50 上线100
                 *     - 4人局完成16轮牌局元宝掉落下限100 上线200
                 */
                if (!stripos($goldConf, '$')) {
                    $viewAssign['errMsg'] = '数据库元宝掉落配置错误1';
                }
                $configs = explode('$', $goldConf);
                $getYuanBaoDropRate = explode('|', $configs[0]);
                if (count($getYuanBaoDropRate) < 3) {
                    $viewAssign['errMsg'] = '数据库元宝掉落配置错误2';
                    break;
                }
                // 多人局元宝比率配置数组
                // [2]=> string(2) "50" [3]=> string(2) "75" [4]=> string(3) "100"
                $getYuanBaoDropRateConf = [];
                foreach ($getYuanBaoDropRate as $rate) {
                    $rateMap = explode(':', $rate);
                    $getYuanBaoDropRateConf[$rateMap[0]] = $rateMap[1];
                }

                $getYuanBaoDropLimit = explode('|', $configs[1]);
                if (count($getYuanBaoDropLimit) < 3) {
                    $viewAssign['errMsg'] = '数据库元宝掉落配置错误3';
                }
                // 元宝掉落数量上下限配置数组
                // [4]=> array(2) { [0]=> string(2) "50" [1]=> string(3) "100" } [8]....
                $getYuanBaoDropLimitConf = [];
                foreach ($getYuanBaoDropLimit as $limit) {
                    $limitMap = explode(':', $limit);
                    $getYuanBaoDropLimitConf[$limitMap[0]] = explode('~', $limitMap[1]);
                }

                // 默认显示活动日期配置
                $viewAssign['tab_date_conf'] = 'active';
                $viewAssign['tab_gold_drop'] = '';
                $viewAssign['page_date_conf'] = 'active in';
                $viewAssign['page_gold_drop'] = '';

                // 优惠券兑换
                $viewAssign['redeemcodeday'] = $goldConfList['redeemcodeday'];
                $viewAssign['redeemcodehour'] = $goldConfList['redeemcodehour'];
                $viewAssign['redeemcodemin'] = $goldConfList['redeemcodemin'];

                // 二人局获得元宝比率
                $viewAssign['gold_rate_2'] = $getYuanBaoDropRateConf[2];
                // 三人局获得元宝比率
                $viewAssign['gold_rate_3'] = $getYuanBaoDropRateConf[3];

                // 游戏局数
                $gameRoundNum = [4, 8, 16];
                // 4人牌局完成n局游戏掉落上下限
                foreach ($gameRoundNum as $round) {
                    $keyLow = 'gold_low_' . $round;
                    $keyHigh = 'gold_high_' . $round;
                    $viewAssign[$keyLow] = $getYuanBaoDropLimitConf[$round][0];
                    $viewAssign[$keyHigh] = $getYuanBaoDropLimitConf[$round][1];
                }

                // 牌局人数
                $gameUserNum = [2, 3, 4];
                // 多人牌局完成n局游戏掉落上下限
                foreach ($gameUserNum as $user) {
                    foreach ($gameRoundNum as $round) {
                        $keyLow = 'gold_low_' . $user . '_' . $round;
                        $keyHigh = 'gold_high_' . $user . '_' . $round;
                        $viewAssign[$keyLow] = $getYuanBaoDropLimitConf[$round][0] * ($getYuanBaoDropRateConf[$user] / 100);
                        $viewAssign[$keyHigh] = $getYuanBaoDropLimitConf[$round][1] * ($getYuanBaoDropRateConf[$user] / 100);
                    }
                }

                // 元宝掉落时间开启
                $viewAssign['active1'] = 'checked';

                break;
            case 'profreedio':
                $displayPage = "profreedio";
                $gamelogic = new GameconfLogic();
                $modRet = $gamelogic->getFreeDiamod();
                if ($modRet['code'] != ERRCODE_SUCCESS) {
                    $viewAssign['errMsg'] = '无法获取配置信息';
                } else {
                    $data = $modRet['data']['list'];
                    $viewAssign['starttime'] = $data['starttime'];
                    $viewAssign['endtime'] = $data['endtime'];
                    if ($data['activity'] == 1)
                        $viewAssign['active1'] = 'checked';
                    else
                        $viewAssign['active0'] = 'checked';
                    $viewAssign['profreeFlag'] = in_array(AUTH_OPER_GF_PROFREEDIO_MGR, C("G_USER.operate")) ? true : null;
                }
                break;
            case 'pronewredpack':
                $displayPage = "pronewredpack";
                $gamelogic = new GameconfLogic();
                $modRet = $gamelogic->getNewRedPack();
                if ($modRet['code'] != ERRCODE_SUCCESS) {
                    $viewAssign['errMsg'] = '无法获取配置信息';
                } else {
                    $data = $modRet['data'];
                    $viewAssign['title'] = $data['title'];
                    $viewAssign['finishrecord'] = $data['count'];
                    $viewAssign['publiccode'] = $data['code'];
                    $viewAssign['limitarea'] = $data['area'];
                    $viewAssign['starttime'] = $data['starttime'];
                    $viewAssign['endtime'] = $data['endtime'];
                    $viewAssign['active'] = $data['activity'];
                    if ($data['activity'] == 1)
                        $viewAssign['active1'] = 'checked';
                    else
                        $viewAssign['active0'] = 'checked';
                    if ($data['display'] == 1)
                        $viewAssign['displaytime1'] = 'checked';
                    else
                        $viewAssign['displaytime0'] = 'checked';
                    $viewAssign['pronewredpackFlag'] = in_array(AUTH_OPER_GF_PRONEWREDPACK_MGR, C("G_USER.operate")) ? true : null;
                }
                break;
            case 'proactinvitefrd':
                $displayPage = "proactinvitefrd";
                $viewAssign['inviteRewardTypeMap'] = $this->reward_reward_type_map;
                $gamelogic = new GameconfLogic();
                $modRet = $gamelogic->getActInviteFriend();
                if ($modRet['code'] != ERRCODE_SUCCESS) {
                    $viewAssign['errMsg'] = '无法获取配置信息';
                } else {
                    $data = $modRet['data']['list'];
                    if ($data['status'] == 1)
                        $viewAssign['active1'] = 'checked';
                    else
                        $viewAssign['active0'] = 'checked';
                    $viewAssign['starttime'] = $data['starttime'];
                    $viewAssign['endtime'] = $data['endtime'];
                    $viewAssign['activing'] = $data['activing'];
                    $viewAssign['list'] = $data['list'];
                    $viewAssign['invitefrdFlag'] = in_array(AUTH_OPER_GF_ACTINVITEFRD_MGR, C("G_USER.operate")) ? true : null;
                }
                break;
            default:
                // 未知三级目录
                redirect('/Auth/logout');
        }
        $viewAssign["stime"] = I('request.stime');
        $viewAssign["etime"] = I('request.etime');
        $viewAssign["operate_type"] = intval(I('request.type'));
        $this->assign($viewAssign);
        $this->display($displayPage);
    }

    public function mall()
    {
        $viewAssign = array();
        // 页面 title
        $viewAssign['title'] = "游戏配置 | 商城配置";
        $third = I('get.third');
        if (empty($third)) {
            $third = 'mallconf';
        }
        $viewAssign['tabthird'] = I('get.third');

        switch ($third) {
            case 'mallconf':
                $goodsLogic = new MallConfigLogic ();

                $goodsWhere = array(
                    'gameId' => C('G_USER.gameid') ,
                    'status' => 1
                );

                $goodsList = $goodsLogic -> getMallGoodsListLogic($goodsWhere);

                if($goodsList['code'] != ERRCODE_SUCCESS ){ //异常服务
                    $viewAssign['errMsg']  = $goodsList['msg'];
                }  else { //查询数据成功
                    $insertId = $goodsLogic ->getMallInsertIdLogic();

                    $result = $goodsList['data'] ;
                    unset($goodsList);
                    $viewAssign['insertId']  = $insertId;
                    $viewAssign['list']  = $result['data'];
                    $viewAssign['pagehtml']  = $result['pagehtml'];
                }

                $displayPage = "mallconf";
                break;
            case 'malllist':
                $goodsLogic = new MallConfigLogic ();

                $goodsWhere = array(
                    'gameId' => C('G_USER.gameid') ,
                    'status' => 0
                );

                $goodsList = $goodsLogic -> getMallGoodsListLogic($goodsWhere);

                if($goodsList['code'] != ERRCODE_SUCCESS ){ //异常服务
                    $viewAssign['errMsg']  = $goodsList['msg'];
                }  else { //查询数据成功
                    $insertId = $goodsLogic ->getMallInsertIdLogic();

                    $result = $goodsList['data'] ;
                    unset($goodsList);
                    $viewAssign['insertId']  = $insertId;
                    $viewAssign['list']  = $result['data'];
                    $viewAssign['pagehtml']  = $result['pagehtml'];
                }

                $displayPage = "mallconf";
                break;
            case 'editGoods':

                $postdata = I('post.', '', 'trim');
                $goodsId = I('post.id');
                $action = I('post.action') ? I('post.action') : 'editGoods' ;

                $goodsLogic = new MallConfigLogic ();
                if($action == 'editGoods'){
                    $retData = $goodsLogic -> saveGoodsInfoLogic($postdata,$goodsId,$action );
                    if ($retData['ret_type'] == 1) {
                        $postdata['subGamelogTitle'] = "新增商品[".I('post.name')."]";
                        $postdata['subGamelogMallId'] = $retData['data'];
                        $postdata['subGamelogMallType'] = $retData['ret_param1'];
                        set_operation("商城配置", $postdata, AUTH_OPER_GF_MALLCONF_MGR);
                    } else if ($retData['ret_type'] == 2) {
                        $postdata['subGamelogTitle'] = "修改商品[".I('post.name')."]";
                        $postdata['subGamelogMallId'] = $goodsId;
                        $postdata['subGamelogMallType'] = $retData['ret_param1'];
                        set_operation("商城配置", $postdata, AUTH_OPER_GF_MALLCONF_MGR);
                    }
                }else{
                    $retData = $goodsLogic -> updateGoodsInfoLogic($goodsId,$action );
                    if ($retData['ret_type'] == 1) {
                        $postdata['subGamelogTitle'] = "下架商品[".$retData['ret_param2']."]";
                        $postdata['subGamelogMallId'] = $goodsId;
                        $postdata['subGamelogMallType'] = $retData['ret_param1'];
                        set_operation("商城配置", $postdata, AUTH_OPER_GF_MALLCONF_MGR);
                    } else if ($retData['ret_type'] == 2) {
                        $postdata['subGamelogTitle'] = "删除商品[".$retData['ret_param2']."]";
                        $postdata['subGamelogMallId'] = $goodsId;
                        $postdata['subGamelogMallType'] = $retData['ret_param1'];
                        set_operation("商城配置", $postdata, AUTH_OPER_GF_MALLCONF_MGR);
                    }
                }

                $this->ajaxReturn($postdata);
                exit;

                break;
            case 'addGoods':

                $postdata = I('post.', '', 'trim');

                $goodsLogic = new MallConfigLogic ();
                $retData = $goodsLogic -> saveGoodsInfoLogic($postdata,0,'addGoods');

                if ($retData['ret_type'] == 1) {
                    $postdata['subGamelogTitle'] = "新增商品[".I('post.name')."]";
                    $postdata['subGamelogMallId'] = $retData['data'];
                    $postdata['subGamelogMallType'] = $retData['ret_param1'];
                    set_operation("商城配置", $postdata, AUTH_OPER_GF_MALLCONF_MGR);
                } else if ($retData['ret_type'] == 2) {
                    $postdata['subGamelogTitle'] = "修改商品[".I('post.name')."]";
                    $postdata['subGamelogMallId'] = 0;
                    $postdata['subGamelogMallType'] = $retData['ret_param1'];
                    set_operation("商城配置", $postdata, AUTH_OPER_GF_MALLCONF_MGR);
                }

                $this->ajaxReturn($postdata);
                exit;

                break;
            // 操作记录
            case 'malllog':
                $goodsLogic = new MallConfigLogic ();

                $goodsWhere = array(
                    'gameId' => C('G_USER.gameid') ,
                );
                //查询兑换日志逻辑
                $goodsList = $goodsLogic -> getMallActListLogic($goodsWhere);

                if($goodsList['code'] != ERRCODE_SUCCESS ){ //异常服务
                    $viewAssign['errMsg']  = $goodsList['msg'];

                }  else { //查询数据成功
                    $result = $goodsList['data'] ;
                    unset($goodsList);

                    $viewAssign['list']  = $result['data'];
                    $viewAssign['pagehtml']  = $result['pagehtml'];
                }


                $displayPage = "malllog";
                break;
            default:
                // 未知三级目录
                redirect('/Auth/logout');
        }
        $viewAssign["stime"] = I('request.stime');
        $viewAssign["etime"] = I('request.etime');
        $this->assign($viewAssign);
        $this->display($displayPage);
    }

    /**
     * 商品信息上传图片
     */
    public function mallGetGoodsInfo()
    {
        $this->checkIsAjax();
        $id = (int) I('post.id');

        $goodsLogic = new MallConfigLogic ();
        $result = $goodsLogic -> getMallGoodsInfoLogic($id);

        if($result['code'] == ERRCODE_SUCCESS){
            $result['code'] = 0 ;
        }

        $this->ajaxReturn($result);
    }

    /**
     * 商品信息上传图片
     */
    public function mallUploadImages()
    {
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // 上传路径，先存放于临时路劲，等待广告创建的时候再上传至图片服务器
        $uploadPath = ROOT_PATH."FileUpload/";
        if (!is_dir($uploadPath)) {
            if (!mkdir($uploadPath, 0777, true)) {
                $retData['code'] = ERRCODE_SYSTEM;
                $retData['msg'] = "mkdir {$uploadPath} failed";
                $this->ajaxReturn($retData);
            }
        }
        // upload 类获取上传文件
        $config = array(
            'maxSize'    => C('IMG_MAX_UPLOAD_SIZE') * 1024 * 1024,
            'rootPath'   => $uploadPath,
            'exts'       => array('jpg', 'jpeg', 'png'),
            'subName'    => false,
            'saveName'   => array('uniqid')
        );
        $upload = new \Think\Upload($config);
        $uploadInfo = $upload->upload();
        if (false === $uploadInfo || empty( $uploadInfo )  ) {
            $retData['code'] = ERRCODE_UPLOAD_FAILED;
            $retData['msg'] = $upload->getError();
            $this->ajaxReturn($retData);
        }

        $uploadData = array_shift($uploadInfo);
        // 获取图片宽高
        $sizeInfo = getimagesize($uploadPath.$uploadData['savename']);

        $retData['data'] = array(
            'imgUrl' => "/FileUpload/".$uploadData['savename'],
            'saveName' => $uploadData['savename'],
        );

        $this->ajaxReturn($retData);
    }

    /************************************ 好友配置 ************************************/

    /**
     * 邀请人奖励配置
     */
    public function ajaxsaveInviteId()
    {
        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => ''
        );

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_FRDINVITE_INVITEID, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }
        $vldSer = new ValidatorService();
        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('number', 0, array(
                array('require', null, '未填写物品数量'),
                array('integer', null, '物品数量必须为整数'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }
        $gamelogic = new GameconfLogic();
        $modRet = $gamelogic->setInviteFriendId(I('POST.'));
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $retData['code'] = ERRCODE_DB_UPDATE_ERR;
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }
        $postdata = I("POST.");
        $postdata['subGamelogTitle'] = '填写邀请人ID奖励';
        $postdata['subGamelogDescrip'] = '修改了填写邀请人ID奖励配置信息(类型：'.$this->reward_id_type_map[I('POST.type')].' | 数量：'.I('POST.number').')';
        set_operation("修改填写邀请人ID奖励配置", $postdata, AUTH_OPER_GF_FRDINVITE_INVITEID);
        $this->ajaxReturn($retData);
    }

    /**
     * 邀请好友奖励
     */
    public function ajaxsaveInviteConf()
    {
        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => ''
        );

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_FRDINVITE_INVITEFRD, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }
        $logarr = array();
        $data = array();
        foreach (I('POST.conf') as $v) {
            $reward = array();
            $rewardlog = array();
            for ($ix = 1; $ix <=5; $ix++) {
                if ($v['type'.$ix] > 0 && intval($v['val'.$ix]) > 0) {
                    $reward[] = $v['type'.$ix].':'.intval($v['val'.$ix]);
                    $rewardlog[] = $this->reward_reward_type_map[$v['type'.$ix]].':'.intval($v['val'.$ix]).'个';
                }
            }
            if ($v['event_num'] > 0 && count($reward) > 0) {
                $rewardval = implode(",", $reward);
                $data[] = array('num'=>$v['event_num'], 'reward'=>$rewardval);
                $logarr[] = '人数：'.$v['event_num'].",".implode(',', $rewardlog);
            }
        }
        $gamelogic = new GameconfLogic();
        $modRet = $gamelogic->setInviteFriendConf($data);
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $retData['code'] = ERRCODE_DB_UPDATE_ERR;
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }
        $postdata = I("POST.");
        $postdata['subGamelogTitle'] = '邀请好友奖励';
        $postdata['subGamelogDescrip'] = '修改了邀请好友奖励配置信息('.implode(' | ',$logarr).')';
        set_operation("修改邀请好友奖励配置", $postdata, AUTH_OPER_GF_FRDINVITE_INVITEFRD);
        $this->ajaxReturn($retData);
    }

    /**
     * 邀请好友配置页面
     */
    public function friend()
    {
        $viewAssign = array();
        // 页面 title
        $viewAssign['title'] = "游戏配置 | 好友配置";
        $third = I('get.third');
        if (empty($third)) {
            $third = 'frdinvite';
        }
        switch ($third) {
            case 'frdinvite':
                $displayPage = "frdinvite";
                $viewAssign['inviteIdTypeMap'] = $this->reward_id_type_map;
                $viewAssign['inviteRewardTypeMap'] = $this->reward_reward_type_map;
                $gamelogic = new GameconfLogic();
                $modRet = $gamelogic->getInviteFriend();
                if ($modRet['code'] != ERRCODE_SUCCESS) {
                    $viewAssign['errMsg'] = '无法获取配置信息';
                } else {
                    $data = $modRet['data']['list'];
                    $viewAssign['invite_type'] = $data['type'];
                    $viewAssign['invite_number'] = $data['number'];
                    $viewAssign['actInvite'] = $data['act'];
                    $viewAssign['list'] = $data['list'];
                    $viewAssign['inviteidFlag'] = in_array(AUTH_OPER_GF_FRDINVITE_INVITEID, C("G_USER.operate")) ? true : null;
                    $viewAssign['invitefrdFlag'] = in_array(AUTH_OPER_GF_FRDINVITE_INVITEFRD, C("G_USER.operate")) ? true : null;
                }
                break;
            default:
                // 未知三级目录
                redirect('/Auth/logout');
        }
        $viewAssign["stime"] = I('request.stime');
        $viewAssign["etime"] = I('request.etime');
        $viewAssign["operate_type"] = intval(I('request.type'));
        $this->assign($viewAssign);
        $this->display($displayPage);
    }

    /************************************ 游戏配置 ************************************/

    public function game()
    {
        $viewAssign = array();

        // 页面 title
        $viewAssign['title'] = "游戏配置 | 游戏配置";

        $third = I('get.third');
        if (empty($third)) {
            $third = 'gamefeed';
        }
        switch ($third) {
            // 房费配置
            case 'gamefeed':
                $gameConfLogic = new GameconfLogic();
                //根据APP产品ID来查询游戏地区ID
                $gameList = $gameConfLogic->getAppGameListLogic();

                if ($gameList ['code'] != ERRCODE_SUCCESS) {
                    $viewAssign['msg'] = $gameList['msg'];
                } else {
                    $templist = $gameList['data'];
                    $viewAssign['gamelist'] = $templist ; //游戏列表展示

                    $firstData = array_shift($templist);
                    $firstPlaceId = $firstData['placeID']; //第一条数据的地区ID
                    $firstGameId = $firstData['gameId']; //第一条数据的玩法ID
                }

                $placeId = (int) I('get.placeid'); //地区id
                if($placeId < 1){
                    $placeId = $firstPlaceId ;
                }
                $gid = (int) I('get.gid'); //玩法ID
                if ($gid < 1) {
                    $gid = $firstGameId ;
                }

                $viewAssign['placeid'] = $placeId ;//玩法ID

                //根据地区ID来查询游戏配置
                $gameRoomSetting = $gameConfLogic -> getGameFeedSettingLogic($placeId,$gid);

                if($gameRoomSetting['code'] != ERRCODE_SUCCESS){ //异常处理
                    $viewAssign['msg'] = $gameRoomSetting['msg'];
                }else{
                    $roomSetting = $gameRoomSetting['data'] ;
                }

                $viewAssign['setting'] = $roomSetting ; //游戏设置
                $viewAssign['palceId'] = $placeId ; //地区ID
                $viewAssign['gid'] = $gid ; //玩法ID
                $displayPage = "gamefeed";
                break;

            // 不要在third里面加ajax操作，另起一个action并加入ajax判断
            case 'savegamefeed': //保存数据
                $retData = array(
                    'code' => ERRCODE_SUCCESS,
                    'msg' => "",
                );

                $gameConfLogic = new GameconfLogic();
                $result = $gameConfLogic -> saveGameRoomSettingLogic();

                $postdata = I("POST.");
                $postdata['subGamelogTitle'] = "房费修改操作";
                $postdata['subGamelogDescrip'] = "修改房费配置为：{".$result['ret_param1']."}";
                $postdata['subGamelogPlay'] = $result['ret_param2'];
                set_operation("房费配置", $postdata, AUTH_OPER_GF_GAMEFEED_MGR);
                if($result['code'] != ERRCODE_SUCCESS ){
                    $retData['code'] = ERRCODE_PARAM_NULL;
                    $retData['msg'] = "参数为空或异常，请联系管理员";

                }else{
                    $retData['data'] = $result['data'];
                }

                $this->ajaxReturn($result);
                break;

            // 不要在third里面加ajax操作，另起一个action并加入ajax判断
            case 'addgamefeed':
                $gameConfLogic = new GameconfLogic();
                $result = $gameConfLogic -> addGameRoomSettingLogic();

                $postdata = I("POST.");
                $postdata['subGamelogTitle'] = "房费新增项操作";
                $postdata['subGamelogDescrip'] = "修改房费配置为：{".$result['ret_param1']."}";
                $postdata['subGamelogPlay'] = $result['ret_param2'];
                set_operation("房费配置", $postdata, AUTH_OPER_GF_GAMEFEED_MGR);
                $this->ajaxReturn($result);
                break;

            // 角色配置
            case 'gameuser':
                $gameConfLogic = new GameconfLogic();
                $result = $gameConfLogic -> getGameUserConfigLogic();
                if($result ['code'] != ERRCODE_SUCCESS ){
                    $viewAssign['errMsg'] = $result['msg'];
                }else{
                    $viewAssign['config'] = $result['data'];
                }
                $displayPage = "gameuser";
                break;

            // 不要在third里面加ajax操作，另起一个action并加入ajax判断
            case 'savegameuser'://保存数据
                $gameConfLogic = new GameconfLogic();
                $result = $gameConfLogic -> setGameUserConfigLogic();

                $postdata = I("POST.");
                $postdata['subGamelogTitle'] = "角色配置操作";
                $postdata['subGamelogDescrip'] = "修改角色配置：钻石（{".$result['ret_param1']."}），元宝（{".$result['ret_param2']."}）";
                set_operation("角色配置", $postdata, AUTH_OPER_GF_GAMEUSER_MGR);
                $this->ajaxReturn($result);
                exit;
                break;

            default:
                // 未知三级目录
                redirect('/Auth/logout');
        }
        $viewAssign["stime"] = I('request.stime');
        $viewAssign["etime"] = I('request.etime');
        $viewAssign["operate_type"] = intval(I('request.type'));
        $this->assign($viewAssign);
        $this->display($displayPage);
    }

    /**
     * 房间配置
     * @author Carter
     */
    public function confRoom()
    {
        $viewAssign = array();

        $gcnfLgc = new GameconfLogic();

        // 操作权限
        $oper = C('G_USER.operate');
        $viewAssign['mgrFlag'] = in_array(AUTH_OPER_GF_GAMEROOM_MGR, $oper) ? true : null;

        // 页面 title
        $viewAssign['title'] = "游戏配置 | 房间配置";

        $gameId = C('G_USER.gameid');

        // 通过游戏id获取玩法列表
        $lgcRet = $gcnfLgc->getDictPlayMapByGameId($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $viewAssign['errMsg'] = '获取玩法列表失败';
        }
        $viewAssign['playMap'] = $playMap = $lgcRet['data'];

        // 当前玩法id
        $playId = I('get.playId');
        if (empty($playId) || !isset($playMap[$playId])) {
            $playId = key($playMap);
        }
        $viewAssign['playId'] = $playId;
        $viewAssign['placeId'] = $placeId = $playMap[$playId]['placeId'];

        // 获取当前玩法房间信息
        $lgcRet = $gcnfLgc->getPlayConfInfoById($gameId, $placeId, $playId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $viewAssign['errMsg'] = '获取玩法配置信息失败';
        }
        $viewAssign['gameInfo'] = $lgcRet['data']['game'];
        $viewAssign['shareInfo'] = $lgcRet['data']['share'];

        // icon url 前缀
        $viewAssign['imgUrlPrefix'] = C('RESOURCE_SERVER_IPHOST').':'.C('RESOURCE_SERVER_PORT').'/';

        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * 房间配置进行缩略图上传
     * @author Carter
     */
    public function ajaxRoomUploadThumbImg()
    {
        $this->checkIsAjax();
        $this->ajaxReturn($this->_shareUploadThumbImg());
    }

    /**
     * 保存房间配置
     * @author Carter
     */
    public function ajaxSaveRoomConf()
    {
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_GAMEROOM_MGR, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $vldSer = new ValidatorService();
        $gcnfLgc = new GameconfLogic();

        // 参数校验
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('place_id', 0, array(
                array('require', null, 'id参数缺失'),
                array('integer', null, 'id参数错误'),
            )),
            array('play_id', 0, array(
                array('require', null, 'id参数缺失'),
                array('integer', null, 'id参数错误'),
            )),
            array('init_score', 0, array(
                array('require', null, '初始分数参数缺失'),
                array('integer', null, '初始分数必须为整型值'),
                array('min', '0', '初始分数不能小于0'),
            )),
            array('expired_time', 0, array(
                array('require', null, '解散时间参数缺失'),
                array('integer', null, '解散时间必须为整型值'),
                array('min', '0', '解散时间不能小于0'),
            )),
            array('share_type', 0, array(
                array('exclude', null, '非法参数'),
            )),
            array('title', 1, array(
                array('len_max', "256", '标题不能超过 256 个字符'),
            )),
            array('desc', 1, array(
                array('len_max', "512", '描述不能超过 512 个字符'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $gameId = C('G_USER.gameid');

        $lgcRet = $gcnfLgc->saveGameRoomConf($gameId, $attr);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("修改房间{$attr['play_id']}配置", $lgcRet['data'], AUTH_OPER_GF_GAMEROOM_MGR);

        $this->ajaxReturn($retData);
    }

    /************************************ 产品配置 ************************************/

    /**
     * 维护控制
     * @author Carter
     */
    public function gameAppConf()
    {
        $viewAssign = array();

        $cnfMod = new GameConfModel();

        // EKEY_APPCONF 兼容
        // ----- start -----
        if (true !== is_function_enable("EKEY_APPCONF")) {
            $viewAssign['errMsg'] = '本游戏未发布v2.6.8.0版本，发版后才支持配置维护控制功能';
        };
        // ----- end -----

        // 页面 title
        $viewAssign['title'] = "维护控制 | 产品配置";

        $gameId = C('G_USER.gameid');

        // debug
        $confLgc = new GameconfLogic();
        $lgcRet = $confLgc->refrashGameAppConfFile($gameId);

        $field = 'game_status,upgrade_time,upgrade_dismiss_time,upgrade_notify_rule,upgrade_msg,upgrade_notify_status,';
        $field .= 'upgrade_notify_start_time,upgrade_notify_end_time,upgrade_notify_title,upgrade_notify_content';
        $modRet = $cnfMod->queryGameConfByGameId($gameId, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            // 需要进行数据格式转化
            $info = $modRet['data'];
            if ($info) {
                // 系统维护时间
                if ($info['upgrade_time'] > 0) {
                    $info['upgrade_time'] = date('Y-m-d H:i:s', $info['upgrade_time']);
                } else {
                    $info['upgrade_time'] = '';
                }
                // 提醒时间点
                $info['upgrade_notify_rule'] = unserialize($info['upgrade_notify_rule']);
                // 开始展示时间
                $info['upgrade_notify_launch'] = ($info['upgrade_notify_end_time'] - $info['upgrade_notify_start_time']) / 60;
            }
            $viewAssign['gameId'] = $gameId;
            $viewAssign['conf'] = $info;
        }

        // 相关 map
        $viewAssign['gameStatusMap'] = $cnfMod->gameStatusMap;
        $viewAssign['notifyStatusMap'] = $cnfMod->notifyStatusMap;

        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * 维护控制配置添加
     * @author Carter
     */
    public function ajaxGameAppSaveConf()
    {
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $cnfMod = new GameConfModel();
        $confLgc = new GameconfLogic();

        // 参数校验
        $attr = I('post.', '', 'trim');
        $rules = array(
            // 操作类型
            array('action_type', 0, array(
                array('require', null, '类型缺失'),
                array('in', 'add,edt', '参数错误'),
            )),
            // 产品ID
            array('game_id', 0, array(
                array('require', null, '产品ID参数缺失'),
                array('integer', null, '产品ID参数错误'),
            )),
            // 游戏状态
            array('game_status', 0, array(
                array('require', null, '游戏状态缺失'),
                array('in', implode(",", array_keys($cnfMod->gameStatusMap)), '参数错误'),
            )),
            // 维护公告面板
            array('upgrade_notify_status', 0, array(
                array('require_if', 'game_status,'.$cnfMod::GAME_STATUS_UPDATE, '维护公告面板参数缺失'),
                array('require_if', 'game_status,'.$cnfMod::GAME_STATUS_READING, '维护公告面板参数缺失'),
                array('in', implode(",", array_keys($cnfMod->notifyStatusMap)), '参数错误'),
            )),
            // 开始展示时间
            array('upgrade_notify_launch', 0, array(
                array('require_if', 'upgrade_notify_status,'.$cnfMod::NOTIFY_STATUS_OPEN, '请填写开始展示时间'),
            )),
            array('upgrade_notify_launch', 1, array(
                array('integer', null, '开始展示时间只能填写整数值'),
            )),
            // 公告标题
            array('upgrade_notify_title', 0, array(
                array('require_if', 'upgrade_notify_status,'.$cnfMod::NOTIFY_STATUS_OPEN, '请填写公告标题'),
                array('len_max', "255", '公告标题不能超过 255 个字符'),
            )),
            // 公告内容
            array('upgrade_notify_content', 0, array(
                array('require_if', 'upgrade_notify_status,'.$cnfMod::NOTIFY_STATUS_OPEN, '请填写公告内容'),
                array('len_max', "512", '公告标题不能超过 512 个字符'),
            )),
            // 非法参数
            array('id', 0, array(
                array('exclude', null, "非法参数：id"),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        // 当游戏状态不为正常运行时，才进行维护公告相关的校验
        if ($cnfMod::GAME_STATUS_ONLINE != $attr['game_status']) {
            $rules = array(
                // 系统维护时间
                array('upgrade_time', 0, array(
                    array('require', null, '请填写系统维护时间'),
                    array('date', null, "系统维护时间格式有误"),
                    array('date_after', date('Y-m-d H:i:s'), "系统维护时间不能早于当前时间"),
                )),
                // 维护提示
                array('upgrade_msg', 0, array(
                    array('require', null, '维护提示不能为空'),
                    array('len_max', "255", '维护提示不能超过 255 个字符'),
                )),
            );
            $vRet = $vldSer->exce($attr, $rules);
            if (true !== $vRet) {
                $retData['code'] = ERRCODE_VALIDATE_FAILED;
                $retData['msg'] = $vRet;
                $this->ajaxReturn($retData);
            }
        }

        // 维护提示必须有且仅有一个%s
        if ('' !== $attr['upgrade_msg']) {
            $firstPos = strpos($attr['upgrade_msg'], '%s');
            if (false === $firstPos || $firstPos !== strrpos($attr['upgrade_msg'], '%s')) {
                $retData['code'] = ERRCODE_VALIDATE_FAILED;
                $retData['msg'] = '维护提示必须有且仅有一个%s';
                $this->ajaxReturn($retData);
            }
        }

        $gameId = C('G_USER.gameid');
        if ($gameId != $attr['game_id']) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = '产品ID与当前游戏不一致，不能修改';
            $this->ajaxReturn($retData);
        }

        // 添加
        if ('add' == $attr['action_type']) {
            $lgcRet = $confLgc->addGameAppConf($attr);
            if (ERRCODE_SUCCESS !== $lgcRet['code']) {
                $retData['code'] = $lgcRet['code'];
                $retData['msg'] = $lgcRet['msg'];
                $this->ajaxReturn($retData);
            }

            // 记录操作流水
            set_operation("添加维护控制配置 {$lgcRet['data']['id']}", $lgcRet['data']);
        }
        // 修改
        else {
            $lgcRet = $confLgc->editGameAppConf($attr);
            if (ERRCODE_SUCCESS !== $lgcRet['code']) {
                $retData['code'] = $lgcRet['code'];
                $retData['msg'] = $lgcRet['msg'];
                $this->ajaxReturn($retData);
            }

            // 记录操作流水
            set_operation("修改维护控制配置 {$lgcRet['data']['id']}", $lgcRet['data']);
        }

        $this->ajaxReturn($retData);
    }

    /**
     * 白名单设置
     * @author Carter
     */
    public function gameAppWhiteList()
    {
        $viewAssign = array();

        $whiteMod = new GameWhiteListModel();

        // EKEY_APPCONF 兼容
        // ----- start -----
        if (true !== is_function_enable("EKEY_APPCONF")) {
            $viewAssign['errMsg'] = '本游戏未发布v2.6.8.0版本，发版后才支持配置白名单功能';
        };
        // ----- end -----

        // 操作权限
        $oper = C('G_USER.operate');
        $viewAssign['mgrFlag'] = in_array(AUTH_OPER_GF_GAPPWHITELIST_MGR, $oper) ? true : null;

        // 页面 title
        $viewAssign['title'] = "白名单设置 | 产品配置";

        $viewAssign['typeMap'] = $whiteMod->whiteTypeMap;

        // 参数校验
        $attr = I('get.', '', 'trim');
        $viewAssign['query'] = json_encode($attr);

        $gameId = C('G_USER.gameid');

        $modRet = $whiteMod->queryGameWhiteUserByAttr($gameId, $attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign['list'] = $modRet['data']['list'];
            $viewAssign['pagination'] = $modRet['data']['pagination'];
        }

        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * 白名单设置（热更新）
     * @author neo
     */
    public function gameAppWhiteListHotUpdate()
    {
        // 操作权限
        $oper = C('G_USER.operate');
        $viewAssign['mgrFlag'] = in_array(AUTH_OPER_GF_GAPPWHITELISTHP_MGR, $oper) ? true : null;

        // EKEY_APPCONF 兼容
        // ----- start -----
        if (true !== is_function_enable("EKEY_APPCONF")) {
            $viewAssign['errMsg'] = '本游戏未发布v2.6.8.0版本，发版后才支持配置白名单功能';
        };
        // ----- end -----

        $whiteMod = new GameUpdateWhiteListModel();
        $syscacheMod = new SysCacheModel();
        $subVersionMod = new GameAppSubversionModel();

        $gameId = C('G_USER.gameid');

        // 页面 title
        $viewAssign['title'] = "白名单设置（热更新） | 产品配置";

        $viewAssign['typeMap'] = $whiteMod->whiteTypeMap;

        // 子玩法 map
        $viewAssign['playMap'] = get_game_play_map($gameId);

        // 参数校验
        $attr = I('get.', '', 'trim');
        $viewAssign['query'] = json_encode($attr);

        $gameId = C('G_USER.gameid');
        $modRet = $whiteMod->queryGameUpdateWhiteUserByAttr($gameId, $attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign['list'] = $modRet['data']['list'];
            $viewAssign['pagination'] = $modRet['data']['pagination'];
        }

        $modRet = $subVersionMod->queryGameAllSubversion($gameId, 'id, play_id, version');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign['versionList'] = array_column($modRet['data'], 'version', 'play_id');
        }

        $modRet = $syscacheMod->querySysCacheByKey($gameId, 'sadmin_update_whitelist_status');
        $viewAssign['whiteListStatus'] = ERRCODE_SUCCESS == $modRet['code'] || !empty($modRet['data']) ? $modRet['data']['cache_sting'] : 'Off';
        $modRet = $syscacheMod->querySysCacheByKey($gameId, 'sadmin_update_whitelist_version');
        $viewAssign['whiteListVersion'] = ERRCODE_SUCCESS == $modRet['code'] || !empty($modRet['data']) ? $modRet['data']['cache_sting'] : '';

        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * 更改热更新开关状态
     * @author Neo
     */
    public function ajaxChangeUpdateWhiteConf()
    {
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_GAPPWHITELISTHP_MGR, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $vldSer = new ValidatorService();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            // 类型
            array('white_status', 0, array(
                array('require', null, '类型参数错误'),
                array('in', 'On,Off', '类型参数错误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $gameId = C('G_USER.gameid');
        $syscacheMod = new SysCacheModel();

        // 设置cachekey
        $cacheKey = 'sadmin_update_whitelist_status';
        $remark = '热更新白名单开启状态';
        $modRet = $syscacheMod->exceSetSysCache($gameId, $cacheKey, $attr['white_status'], $remark);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        set_operation("设置热更白名单状态 {$modRet['data']['id']}", $modRet['data'], AUTH_OPER_GF_GAPPWHITELISTHP_MGR);

        // 设置cachekey
        $cacheKey = 'sadmin_update_whitelist_version';
        $remark = '热更新白名单开启版本';
        $modRet = $syscacheMod->exceSetSysCache($gameId, $cacheKey, $attr['white_version'], $remark);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        set_operation("设置热更白名单对应版本 {$modRet['data']['id']}", $modRet['data'], AUTH_OPER_GF_GAPPWHITELISTHP_MGR);

        // 需要更新白名单文件（旧版白名单文件）
        // EKEY_APPCONF 兼容
        // ----- start -----
        $confLgc = new GameconfLogic();
        $lgcRet = $confLgc->refrashGameAppConfFileOld($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }
        // ----- end -----

        // 添加白名单，需要更新白名单文件
        $lgcRet = $confLgc->refrashGameAppConfFile($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        $this->ajaxReturn($ret);
    }

    /**
     * 新增白名单用户
     * @author Carter
     */
    public function ajaxAddWhiteList()
    {
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_GAPPWHITELIST_MGR, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $vldSer = new ValidatorService();
        $whiteMod = new GameWhiteListModel();
        $confLgc = new GameconfLogic();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            // 类型
            array('white_type', 0, array(
                array('require', null, '类型参数错误'),
                array('in', implode(",", array_keys($whiteMod->whiteTypeMap)), '类型参数错误'),
            )),
            // 白名单
            array('white_val', 0, array(
                array('require', null, '请填写白名单'),
                array('len_max', "32", '白名单长度不能超过 32 个字符'),
            )),
            // 备注
            array('remark', 1, array(
                array('len_max', "64", '备注长度不能超过 64 个字符'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $gameId = C('G_USER.gameid');

        // 插入数据
        $modRet = $whiteMod->insertGameWhiteList($gameId, $attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        set_operation("添加白名单 {$modRet['data']['id']}", $modRet['data'], AUTH_OPER_GF_GAPPWHITELIST_MGR);

        // 需要更新白名单文件（旧版白名单文件）
        // EKEY_APPCONF 兼容
        // ----- start -----
        $lgcRet = $confLgc->refrashGameAppConfFileOld($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }
        // ----- end -----

        // 添加白名单，需要更新白名单文件
        $lgcRet = $confLgc->refrashGameAppConfFile($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        $this->ajaxReturn($ret);
    }

    /**
     * 新增白名单用户(热更新)
     * @author Neo
     */
    public function ajaxAddUpdateWhiteList()
    {
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_GAPPWHITELISTHP_MGR, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $vldSer = new ValidatorService();
        $whiteMod = new GameUpdateWhiteListModel();
        $confLgc = new GameconfLogic();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            // 类型
            array('white_type', 0, array(
                array('require', null, '类型参数错误'),
                array('in', implode(",", array_keys($whiteMod->whiteTypeMap)), '类型参数错误'),
            )),
            // 白名单
            array('white_val', 0, array(
                array('require', null, '请填写白名单'),
                array('len_max', "32", '白名单长度不能超过 32 个字符'),
            )),
            // 备注
            array('remark', 1, array(
                array('len_max', "64", '备注长度不能超过 64 个字符'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $gameId = C('G_USER.gameid');

        // 插入数据
        $modRet = $whiteMod->insertGameUpdateWhiteList($gameId, $attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        set_operation("添加白名单 {$modRet['data']['id']}", $modRet['data'], AUTH_OPER_GF_GAPPWHITELISTHP_MGR);

        // 需要更新白名单文件（旧版白名单文件）
        // EKEY_APPCONF 兼容
        // ----- start -----
        $lgcRet = $confLgc->refrashGameAppConfFileOld($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }
        // ----- end -----

        // 添加白名单，需要更新白名单文件
        $lgcRet = $confLgc->refrashGameAppConfFile($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        $this->ajaxReturn($ret);
    }

    /**
     * 删除白名单用户
     * @author Carter
     */
    public function ajaxDelWhiteList()
    {
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_GAPPWHITELIST_MGR, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $vldSer = new ValidatorService();
        $whiteMod = new GameWhiteListModel();
        $confLgc = new GameconfLogic();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            // id
            array('id', 0, array(
                array('require', null, 'id参数错误'),
                array('integer', null, 'id参数错误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $gameId = C('G_USER.gameid');

        // 插入数据
        $modRet = $whiteMod->deleteGameWhiteList($attr['id']);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        set_operation("删除白名单 {$attr['id']}", array(), AUTH_OPER_GF_GAPPWHITELIST_MGR);

        // 需要更新白名单文件（旧版白名单文件）
        // EKEY_APPCONF 兼容
        // ----- start -----
        $lgcRet = $confLgc->refrashGameAppConfFileOld($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }
        // ----- end -----

        // 删除白名单，需要更新白名单文件
        $lgcRet = $confLgc->refrashGameAppConfFile($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        $this->ajaxReturn($ret);
    }

    /**
     * 删除白名单用户(热更新)
     * @author Neo
     */
    public function ajaxDelUpdateWhiteList()
    {
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_GAPPWHITELISTHP_MGR, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $vldSer = new ValidatorService();
        $whiteMod = new GameUpdateWhiteListModel();
        $confLgc = new GameconfLogic();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            // id
            array('id', 0, array(
                array('require', null, 'id参数错误'),
                array('integer', null, 'id参数错误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $gameId = C('G_USER.gameid');

        // 插入数据
        $modRet = $whiteMod->deleteGameUpdateWhiteList($attr['id']);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        set_operation("删除白名单 {$attr['id']}", array(), AUTH_OPER_GF_GAPPWHITELISTHP_MGR);

        // 需要更新白名单文件（旧版白名单文件）
        // EKEY_APPCONF 兼容
        // ----- start -----
        $lgcRet = $confLgc->refrashGameAppConfFileOld($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }
        // ----- end -----

        // 删除白名单，需要更新白名单文件
        $lgcRet = $confLgc->refrashGameAppConfFile($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        $this->ajaxReturn($retData);
    }

    /**
     * 指定子游戏热更版本
     * @author Neo
     */
    public function ajaxUpdatePlayVersion()
    {
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_GAPPWHITELISTHP_MGR, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $vldSer = new ValidatorService();
        $versionMod = new GameAppSubversionModel();
        $confLgc = new GameconfLogic();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            // id
            array('play_id', 0, array(
                array('require', null, 'id参数错误'),
                array('integer', null, 'id参数错误'),
            )),
        );

        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $gameId = C('G_USER.gameid');

        // 插入数据
        $modRet = $versionMod->updatePlayVersionByAttr($gameId, $attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        set_operation("指定子游戏热更版本 游戏id{$gameId}, 子游戏{$attr['play_id']}", array(), AUTH_OPER_GF_GAPPWHITELISTHP_MGR);

        // 需要更新白名单文件（旧版白名单文件）
        // EKEY_APPCONF 兼容
        // ----- start -----
        $lgcRet = $confLgc->refrashGameAppConfFileOld($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }
        // ----- end -----

        // 添加白名单，需要更新白名单文件
        $lgcRet = $confLgc->refrashGameAppConfFile($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        $this->ajaxReturn($retData);
    }

    /**
     * 黑名单设置
     * @author Carter
     */
    public function gameAppBlackList()
    {
        $viewAssign = array();

        $blackMod = new GameBlackListModel();

        // EKEY_APPCONF 兼容
        // ----- start -----
        if (true !== is_function_enable("EKEY_APPCONF")) {
            $viewAssign['errMsg'] = '本游戏未发布v2.6.8.0版本，发版后才支持配置黑名单功能';
        };
        // ----- end -----

        // 操作权限
        $oper = C('G_USER.operate');
        $viewAssign['mgrFlag'] = in_array(AUTH_OPER_GF_GAPPBLACKLIST_MGR, $oper) ? true : null;

        // 页面 title
        $viewAssign['title'] = "黑名单设置 | 产品配置";

        $viewAssign['typeMap'] = $blackMod->blackTypeMap;

        // 参数校验
        $attr = I('get.', '', 'trim');
        $viewAssign['query'] = json_encode($attr);

        $gameId = C('G_USER.gameid');

        $modRet = $blackMod->queryGameBlackUserByAttr($gameId, $attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign['list'] = $modRet['data']['list'];
            $viewAssign['pagination'] = $modRet['data']['pagination'];
        }

        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * 新增黑名单用户
     * @author Carter
     */
    public function ajaxAddBlackList()
    {
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_GAPPBLACKLIST_MGR, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $vldSer = new ValidatorService();
        $blackMod = new GameBlackListModel();
        $confLgc = new GameconfLogic();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            // 类型
            array('black_type', 0, array(
                array('require', null, '类型参数错误'),
                array('in', implode(",", array_keys($blackMod->blackTypeMap)), '类型参数错误'),
            )),
            // 白名单
            array('black_val', 0, array(
                array('require', null, '请填写白名单'),
                array('len_max', "32", '白名单长度不能超过 32 个字符'),
            )),
            // 备注
            array('remark', 1, array(
                array('len_max', "64", '备注长度不能超过 64 个字符'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $gameId = C('G_USER.gameid');

        // 插入数据
        $modRet = $blackMod->insertGameBlackList($gameId, $attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        set_operation("添加黑名单 {$modRet['data']['id']}", $modRet['data'], AUTH_OPER_GF_GAPPBLACKLIST_MGR);

        // 需要更新白名单文件（旧版白名单文件）
        // EKEY_APPCONF 兼容
        // ----- start -----
        $lgcRet = $confLgc->refrashGameAppConfFileOld($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }
        // ----- end -----

        // 添加黑名单，需要更新白名单文件
        $lgcRet = $confLgc->refrashGameAppConfFile($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        $this->ajaxReturn($ret);
    }

    /**
     * 删除黑名单用户
     * @author Carter
     */
    public function ajaxDelBlackList()
    {
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_GAPPBLACKLIST_MGR, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $vldSer = new ValidatorService();
        $blackMod = new GameBlackListModel();
        $confLgc = new GameconfLogic();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            // id
            array('id', 0, array(
                array('require', null, 'id参数错误'),
                array('integer', null, 'id参数错误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $gameId = C('G_USER.gameid');

        // 插入数据
        $modRet = $blackMod->deleteGameBlackList($attr['id']);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        set_operation("删除黑名单 {$attr['id']}", array(), AUTH_OPER_GF_GAPPBLACKLIST_MGR);

        // 需要更新白名单文件（旧版白名单文件）
        // EKEY_APPCONF 兼容
        // ----- start -----
        $lgcRet = $confLgc->refrashGameAppConfFileOld($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }
        // ----- end -----

        // 删除白名单，需要更新白名单文件
        $lgcRet = $confLgc->refrashGameAppConfFile($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        $this->ajaxReturn($ret);
    }

    /**
     * 版本管理
     * @author Carter
     */
    public function gameAppVersion()
    {
        $viewAssign = array();

        $confLgc = new GameconfLogic();
        $verMod = new GameAppVersionModel();

        // EKEY_UPDATE_VERSION 兼容
        // ----- start -----
        if (true !== is_function_enable("EKEY_UPDATE_VERSION")) {
            $viewAssign['errMsg'] = '本游戏前端未修改白名单文件新路径格式，更新格式后才支持版本管理功能';
        };
        // ----- end -----

        // 操作权限
        $oper = C('G_USER.operate');
        $viewAssign['mgrFlag'] = in_array(AUTH_OPER_GF_GAPPVERSION_MGR, $oper) ? 1 : 0;
        $viewAssign['pubFlag'] = in_array(AUTH_OPER_GF_GAPPVERSION_PUBLISH, $oper) ? 1 : 0;

        // 页面 title
        $viewAssign['title'] = "版本管理 | 产品配置";

        // 更新方式 map
        $viewAssign['modeMap'] = $verMod->updateModeMap;

        // 状态 map
        $viewAssign['statusMap'] = $verMod->statusMap;

        // 参数
        $attr = I('get.', '', 'trim');
        $viewAssign['query'] = json_encode($attr);

        $gameId = C('G_USER.gameid');

        $lgcRet = $confLgc->getGAppVersionChannelList($gameId, $attr);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $viewAssign['errMsg'] = $lgcRet['msg'];
        } else {
            $viewAssign['list'] = $lgcRet['data'];
        }

        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * 版本管理上传资源文件
     * @author Carter
     */
    public function ajaxVersionUploadResource()
    {
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        // 上传路径，先存放于临时路劲，等待提交的时候再上传至资源服务器
        $uploadPath = ROOT_PATH."FileUpload/VersionResource/";
        if (!is_dir($uploadPath)) {
            if (!mkdir($uploadPath, 0777, true)) {
                $retData['code'] = ERRCODE_SYSTEM;
                $retData['msg'] = "mkdir {$uploadPath} failed";
                $this->ajaxReturn($retData);
            }
        }

        // upload 类获取上传文件
        $config = array(
            'rootPath'   => $uploadPath,
            'maxSize'    => 32 * 1024 * 1024,
            'exts'       => array('zip'),
            'mimes'      => array('application/zip'),
            'subName'    => false,
            'saveName'   => array('uniqid')
        );
        $upload = new \Think\Upload($config);
        $uploadInfo = $upload->upload();
        if (false === $uploadInfo || empty($uploadInfo['resource_file'])) {
            $retData['code'] = ERRCODE_UPLOAD_FAILED;
            $errMsg = $upload->getError();
            if ('上传文件后缀不允许' == $errMsg) {
                $retData['msg'] = '仅支持ZIP文件';
            } else {
                $retData['msg'] = $errMsg;
            }
            $this->ajaxReturn($retData);
        }

        $retData['data'] = array(
            'fileName' => $uploadInfo['resource_file']['name'],
            'saveName' => $uploadInfo['resource_file']['savename'],
        );

        $this->ajaxReturn($retData);
    }

    /**
     * 每个渠道的版本列表通过ajax动态获取
     * @author Carter
     */
    public function ajaxGetVersionList()
    {
        $this->checkIsAjax();

        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $verMod = new GameAppVersionModel();

        $attr = I('post.', '', 'trim');

        // 获取版本分页列表
        $modRet = $verMod->queryGameAppVersionList($attr['channel_code'], $attr['page']);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }
        $ret['data']['list'] = $modRet['data']['list'];
        $ret['data']['totalPage'] = $modRet['data']['totalPage'];

        $this->ajaxReturn($ret);
    }

    /**
     * 获取版本详情信息
     * @author Carter
     */
    public function iframeGetVersionInfo($id)
    {
        $viewAssign = array();

        $verMod = new GameAppVersionModel();

        $viewAssign['modeMap'] = $verMod->updateModeMap;
        $viewAssign['statusMap'] = $verMod->statusMap;

        // 获取版本分页列表
        $modRet = $verMod->queryGameAppVersionById($id, '*');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign['info'] = $modRet['data'];
        }

        layout(false);
        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * 添加新版本提交
     * @author Carter
     */
    public function ajaxAddVersion()
    {
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_GAPPVERSION_MGR, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $vldSer = new ValidatorService();
        $verMod = new GameAppVersionModel();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            // 渠道号
            array('channel_code', 0, array(
                array('require', null, '渠道号参数缺失'),
                array('integer', null, '渠道号参数错误'),
            )),
            // 游戏版本号
            array('update_version', 0, array(
                array('require', null, '请填写游戏版本号'),
                array('len_max', "15", '游戏版本号总长度不能超过 15 个字符'),
            )),
            // 更新方式
            array('update_mode', 0, array(
                array('require', null, '更新方式参数缺失'),
                array('in', implode(',', array_keys($verMod->updateModeMap)), '更新方式参数错误'),
            )),
            // 强更地址
            array('update_url', 0, array(
                array('require_if', 'update_mode,'.$verMod::UPDATE_MODE_REPLACE, '请填写强更地址'),
            )),
            array('update_url', 1, array(
                array('url', null, '强更地址不是一个合规的URL地址'),
                array('len_max', "255", '强更地址长度不能超过 255 个字符'),
            )),
            // 热更资源
            array('file_savename', 0, array(
                array('require_if', 'update_mode,'.$verMod::UPDATE_MODE_PACKS, '请上传热更资源文件'),
            )),
            // 备注
            array('remark', 1, array(
                array('len_max', "255", '备注长度不能超过 255 个字符'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $gameId = C('G_USER.gameid');

        // 插入数据
        $modRet = $verMod->insertGameAppVersion($gameId, $attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        set_operation("提交新版本 {$modRet['data']['id']}", $modRet['data'], AUTH_OPER_GF_GAPPVERSION_MGR);

        $this->ajaxReturn($ret);
    }

    /**
     * 修改版本信息
     * @author Carter
     */
    public function ajaxEdtVersion()
    {
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_GAPPVERSION_MGR, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $vldSer = new ValidatorService();
        $verMod = new GameAppVersionModel();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            // id
            array('id', 0, array(
                array('require', null, 'id参数缺失'),
                array('integer', null, 'id参数错误'),
            )),
            // 强更地址
            array('update_url', 1, array(
                array('url', null, '强更地址不是一个合规的URL地址'),
                array('len_max', "255", '强更地址长度不能超过 255 个字符'),
            )),
            // 备注
            array('remark', 1, array(
                array('len_max', "255", '备注长度不能超过 255 个字符'),
            )),
            // 非法参数
            array('game_id', 0, array(
                array('exclude', null, '存在非法参数缺失'),
            )),
            array('update_version', 0, array(
                array('exclude', null, '存在非法参数缺失'),
            )),
            array('latest_flag', 0, array(
                array('exclude', null, '存在非法参数缺失'),
            )),
            array('channel_code', 0, array(
                array('exclude', null, '存在非法参数缺失'),
            )),
            array('update_mode', 0, array(
                array('exclude', null, '存在非法参数缺失'),
            )),
            array('status', 0, array(
                array('exclude', null, '存在非法参数缺失'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        // 修改数据
        $modRet = $verMod->updateGameAppVersion($attr['id'], $attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        set_operation("修改版本 {$attr['id']}", $modRet['data'], AUTH_OPER_GF_GAPPVERSION_MGR);

        $this->ajaxReturn($ret);
    }

    /**
     * 发布版本
     * @author Carter
     */
    public function ajaxPublishVersion()
    {
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_GAPPVERSION_PUBLISH, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $vldSer = new ValidatorService();
        $verMod = new GameAppVersionModel();
        $confLgc = new GameconfLogic();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            // id
            array('id', 0, array(
                array('require', null, 'id参数缺失'),
                array('integer', null, 'id参数错误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $gameId = C('G_USER.gameid');

        // 进行发版操作
        $modRet = $verMod->exceGameAppVersionPublish($gameId, $attr['id']);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 需要更新白名单文件（旧版白名单文件）
        // EKEY_APPCONF 兼容
        // ----- start -----
        $lgcRet = $confLgc->refrashGameAppConfFileOld($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }
        // ----- end -----

        // 发布版本，需要更新白名单文件
        $lgcRet = $confLgc->refrashGameAppConfFile($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        set_operation("发布版本 {$attr['id']}", $modRet['data'], AUTH_OPER_GF_GAPPVERSION_PUBLISH);

        $this->ajaxReturn($ret);
    }

    /**
     * 取消版本发布
     * @author Carter
     */
    public function ajaxCancelVersion()
    {
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_GAPPVERSION_MGR, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $vldSer = new ValidatorService();
        $verMod = new GameAppVersionModel();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            // id
            array('id', 0, array(
                array('require', null, 'id参数缺失'),
                array('integer', null, 'id参数错误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $gameId = C('G_USER.gameid');

        // 进行发版操作
        $modRet = $verMod->exceGameAppVersionCancel($gameId, $attr['id']);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        set_operation("删除版本 {$attr['id']}", $modRet['data'], AUTH_OPER_GF_GAPPVERSION_MGR);

        $this->ajaxReturn($ret);
    }

    /**
     * 落地页配置
     */
    public function gameAppLandpage()
    {
        $viewAssign = array();

        // 页面 title
        $viewAssign['title'] = "游戏配置 | 产品配置";

        $gameConfLogic = new GameconfLogic();

        $gameId = C('G_USER.gameid');
        $placeId = I('get.placeId', 0, 'intval');
        if (empty($placeId)) {
            $placeId = $gameId;
        }
        $viewAssign['placeId'] = $placeId;

        $viewAssign['image_path'] = '';
        $modRet = $gameConfLogic->getLandPageConfig($gameId, $placeId);
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $viewAssign['errMsg'] = '无法获取配置信息';
        } else {
            $data = $modRet['data'];
            if (count($data) > 0) {
                $viewAssign['landpage_title'] = $data['title'];
                $viewAssign['image_path'] = C('RESOURCE_SERVER_IPHOST').':'.C('RESOURCE_SERVER_PORT').'/'.GameLandpageModel::PIC_PATH.C('G_USER.gameid').'/'.$data['image_name'];
                $viewAssign['and_downlink'] = $data['android_downlink'];
                $viewAssign['ios_downlink'] = $data['ios_downlink'];
            }
            $viewAssign['anddownFlag'] = in_array(AUTH_OPER_GF_GAPP_ANDROID_DOWNLINK, C("G_USER.operate")) ? true : null;
            $viewAssign['iosdownFlag'] = in_array(AUTH_OPER_GF_GAPP_IOS_DOWNLINK, C("G_USER.operate")) ? true : null;
        }

        // 通过游戏id获取地区树，不考虑合服包
        $areaList = $gameConfLogic->getPlaceTreeByGameId($gameId, $placeId, false);
        if (ERRCODE_SUCCESS !== $areaList['code']) {
            $viewAssign['errMsg'] = '获取地区树失败';
        }
        $viewAssign['placeTree'] = json_encode($areaList['data']['tree']);
        $viewAssign['pageHead'] = $areaList['data']['pageHead'];

        // 获取状态map，用于区分哪些地区存在配置项，哪些无配置项
        $validPlace = $gameConfLogic->getLandPageValidPlace($gameId);
        if (ERRCODE_SUCCESS !== $validPlace['code']) {
            $viewAssign['errMsg'] = '获取地区状态映射失败';
        }
        $viewAssign['validPlace'] = json_encode($validPlace['data']);
        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * 广告图片上传
     */
    public function ajaxLandpageUploadImages()
    {
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // 上传路径，先存放于临时路劲，等待广告创建的时候再上传至图片服务器
        $uploadPath = ROOT_PATH.GameLandpageModel::TEMP_PIC_PATH."/";
        if (!is_dir($uploadPath)) {
            if (!mkdir($uploadPath, 0777, true)) {
                $retData['code'] = ERRCODE_SYSTEM;
                $retData['msg'] = "mkdir {$uploadPath} failed";
                $this->ajaxReturn($retData);
            }
        }
        // upload 类获取上传文件
        $config = array(
            'maxSize'    => C('IMG_MAX_UPLOAD_SIZE') * 1024 * 1024,
            'rootPath'   => $uploadPath,
            'exts'       => array('jpg', 'jpeg', 'png', 'gif'),
            'subName'    => false,
            'saveName'   => array('uniqid')
        );
        $upload = new \Think\Upload($config);
        $uploadInfo = $upload->upload();
        if (false === $uploadInfo || empty( $uploadInfo )  ) {
            $retData['code'] = ERRCODE_UPLOAD_FAILED;
            $retData['msg'] = $upload->getError();
            $this->ajaxReturn($retData);
        }

        $uploadData = array_shift($uploadInfo);
        // 获取图片宽高
        $sizeInfo = getimagesize($uploadPath.$uploadData['savename']);

        $retData['data'] = array(
            'imgUrl' => "/".GameLandpageModel::TEMP_PIC_PATH."/".$uploadData['savename'],
            'saveName' => $uploadData['savename'],
        );

        $this->ajaxReturn($retData);
    }

    /**
     * 保存落地页配置
     */
    public function ajaxSaveLandpage()
    {
        $this->checkIsAjax();
        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => ''
        );

        $model = new GameLandpageModel();
        $modRet = $model->saveGameData(I('POST.'));
        if ($modRet['code'] != ERRCODE_SUCCESS) {
            $retData['code'] = ERRCODE_DATA_ERR;
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }
        set_operation("修改落地页配置", $modRet['data']);
        $this->ajaxReturn($retData);
    }

    /**
     * AppID配置
     * @author Carter
     */
    public function gameAppAppid()
    {
        $viewAssign = array();

        $gameMod = new GameModel();
        $appidMod = new GameShareAppidModel();

        // 操作权限
        $oper = C('G_USER.operate');
        $viewAssign['addFlag'] = in_array(AUTH_OPER_GF_GAPPAPPID_ADD, $oper) ? true : null;
        $viewAssign['edtFlag'] = in_array(AUTH_OPER_GF_GAPPAPPID_EDT, $oper) ? true : null;
        $viewAssign['delFlag'] = in_array(AUTH_OPER_GF_GAPPAPPID_DEL, $oper) ? true : null;

        // 页面 title
        $viewAssign['title'] = "游戏配置 | 产品配置";

        // 是否被封杀 map
        $viewAssign['isBlockadeMap'] = $appidMod->isBlockadeMap;

        // 状态 map
        $viewAssign['statusMap'] = $appidMod->statusMap;

        // 参数校验
        $attr = I('get.', '', 'trim');
        $viewAssign['query'] = json_encode($attr);

        // 省包与市包游戏 map
        $localMap = $gameMod->localMap;
        $gameId = C('G_USER.gameid');
        $regionMap = $this->get('regionMap');
        $gameArr = array($gameId);
        $gameMap = array($gameId => $regionMap[$gameId]);
        if (isset($localMap[$gameId])) {
            foreach ($localMap[$gameId] as $k => $v) {
                $gameMap[$k] = $v['gName'];
                $gameArr[] = $k;
            }
        }
        $viewAssign['gameMap'] = $gameMap;

        $attr['game_id'] = $gameArr;

        $field = 'id,game_id,appid,app_name,share_count,is_blockade,status';
        $modRet = $appidMod->queryGameShareAppidListByAttr($attr, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $list = array();
            foreach ($modRet['data'] as $v) {
                $list[$v['game_id']][] = $v;
            }
            $viewAssign['list'] = $list;
        }

        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * 添加AppID配置
     * @author Carter
     */
    public function ajaxConfAddAppid()
    {
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_GAPPAPPID_ADD, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $gameMod = new GameModel();
        $appidMod = new GameShareAppidModel();
        $confLgc = new GameconfLogic();

        $localMap = $gameMod->localMap;
        $gameId = C('G_USER.gameid');
        $gameArr = array($gameId);
        if (isset($localMap[$gameId])) {
            foreach ($localMap[$gameId] as $k => $v) {
                $gameArr[] = $k;
            }
        }

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            // gameId
            array('game_id', 0, array(
                array('require', null, 'gameId参数缺失'),
                array('in', implode(',', $gameArr), 'gameId参数无效'),
            )),
            // appID
            array('appid', 0, array(
                array('require', null, 'appID参数缺失'),
                array('len_max', "64", 'appID长度不能超过 64 个字符'),
            )),
            // app名称
            array('app_name', 0, array(
                array('require', null, '请填写app名称'),
                array('len_max', "64", 'app名称长度不能超过 64 个字符'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $modRet = $appidMod->insertShareAppidConf($attr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("添加AppID配置 {$modRet['data']['id']}", $modRet['data'], AUTH_OPER_GF_GAPPAPPID_ADD);

        // 修改内容，更新分享后台配置文件
        $lgcRet = $confLgc->refrashShareConfFile($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        $this->ajaxReturn($retData);
    }

    /**
     * 修改AppID配置
     * @author Carter
     */
    public function ajaxConfEditAppid()
    {
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_GAPPAPPID_EDT, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $gameMod = new GameModel();
        $appidMod = new GameShareAppidModel();
        $confLgc = new GameconfLogic();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            // id
            array('id', 0, array(
                array('require', null, 'id参数缺失'),
                array('integer', null, 'id参数错误'),
            )),
            // 状态
            array('status', 0, array(
                array('require', null, '状态参数缺失'),
                array('in', implode(",", array_keys($appidMod->statusMap)), '状态参数错误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        // 省包下允许修改其附属热更包的appid
        $localMap = $gameMod->localMap;
        $gameId = C('G_USER.gameid');
        $gameArr = array($gameId);
        if (isset($localMap[$gameId])) {
            foreach ($localMap[$gameId] as $k => $v) {
                $gameArr[] = $k;
            }
        }

        // 同时还要传入省包和热更包的game id
        $modRet = $appidMod->updateShareAppidConf($attr, $gameArr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("修改AppID配置 {$attr['id']}", $attr, AUTH_OPER_GF_GAPPAPPID_EDT);

        // 修改内容，更新分享后台配置文件
        $lgcRet = $confLgc->refrashShareConfFile(C('G_USER.gameid'));
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        $this->ajaxReturn($retData);
    }

    /**
     * 删除AppID配置
     * @author Carter
     */
    public function ajaxConfDeleteAppid()
    {
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_GAPPAPPID_DEL, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $gameMod = new GameModel();
        $appidMod = new GameShareAppidModel();
        $confLgc = new GameconfLogic();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            // id
            array('id', 0, array(
                array('require', null, 'id参数缺失'),
                array('integer', null, 'id参数错误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        // 省包下允许删除其附属热更包的appid
        $localMap = $gameMod->localMap;
        $gameId = C('G_USER.gameid');
        $gameArr = array($gameId);
        if (isset($localMap[$gameId])) {
            foreach ($localMap[$gameId] as $k => $v) {
                $gameArr[] = $k;
            }
        }

        // 同时还要传入省包和热更包的game id
        $modRet = $appidMod->deleteShareAppidConf($attr['id'], $gameArr);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("删除AppID配置 {$attr['id']}", array(), AUTH_OPER_GF_GAPPAPPID_DEL);

        // 修改内容，更新分享后台配置文件
        $lgcRet = $confLgc->refrashShareConfFile(C('G_USER.gameid'));
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        $this->ajaxReturn($retData);
    }

    /**
     * 域名配置
     * @author Carter
     */
    public function gameAppDomain()
    {
        $viewAssign = array();

        $domainMod = new GameShareDomainModel();

        // 操作权限
        $oper = C('G_USER.operate');
        $viewAssign['addFlag'] = in_array(AUTH_OPER_GF_GAPPDOMAIN_ADD, $oper) ? true : null;
        $viewAssign['edtFlag'] = in_array(AUTH_OPER_GF_GAPPDOMAIN_EDT, $oper) ? true : null;
        $viewAssign['delFlag'] = in_array(AUTH_OPER_GF_GAPPDOMAIN_DEL, $oper) ? true : null;

        // 页面 title
        $viewAssign['title'] = "游戏配置 | 产品配置";

        // 是否被封杀 map
        $viewAssign['isBlockadeMap'] = $domainMod->isBlockadeMap;

        // 状态 map
        $viewAssign['statusMap'] = $domainMod->statusMap;

        // 参数校验
        $attr = I('get.', '', 'trim');
        $attr['game_id'] = C('G_USER.gameid');
        $viewAssign['query'] = json_encode($attr);

        $field = 'id,link,share_count,is_blockade,status';
        $modRet = $domainMod->queryGameShareDomainListByAttr($attr, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign['list'] = $modRet['data'];
        }

        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * 添加域名配置
     * @author Carter
     */
    public function ajaxConfAddDomain()
    {
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_GAPPDOMAIN_ADD, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $confLgc = new GameconfLogic();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            // 链接地址
            array('link', 0, array(
                array('require', null, '链接地址参数缺失'),
                array('len_max', "256", '链接地址长度不能超过 256 个字符'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $gameId = C('G_USER.gameid');

        $lgcRet = $confLgc->addGameAppDomainConf($gameId, $attr);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("添加域名配置 {$lgcRet['data']['insertData']['id']}", $lgcRet['data'], AUTH_OPER_GF_GAPPDOMAIN_ADD);

        // 修改内容，更新分享后台配置文件
        $lgcRet = $confLgc->refrashShareConfFile($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        $this->ajaxReturn($retData);
    }

    /**
     * 修改域名配置
     * @author Carter
     */
    public function ajaxConfEditDomain()
    {
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_GAPPDOMAIN_EDT, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $domainMod = new GameShareDomainModel();
        $confLgc = new GameconfLogic();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            // id
            array('id', 0, array(
                array('require', null, 'id参数缺失'),
                array('integer', null, 'id参数错误'),
            )),
            // 状态
            array('status', 0, array(
                array('require', null, '状态参数缺失'),
                array('in', implode(",", array_keys($domainMod->statusMap)), '状态参数错误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $gameId = C('G_USER.gameid');

        $lgcRet = $confLgc->editShareDomainConf($gameId, $attr['id'], $attr);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("修改域名配置 {$attr['id']}", $attr, AUTH_OPER_GF_GAPPDOMAIN_EDT);

        // 修改内容，更新分享后台配置文件
        $lgcRet = $confLgc->refrashShareConfFile($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        $this->ajaxReturn($retData);
    }

    /**
     * 删除域名配置
     * @author Carter
     */
    public function ajaxConfDeleteDomain()
    {
        $this->checkIsAjax();

        // 操作权限校验
        if (!in_array(AUTH_OPER_GF_GAPPDOMAIN_DEL, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $confLgc = new GameconfLogic();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            // id
            array('id', 0, array(
                array('require', null, 'id参数缺失'),
                array('integer', null, 'id参数错误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        $gameId = C('G_USER.gameid');

        $lgcRet = $confLgc->removeShareDomainConf($gameId, $attr['id']);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("删除域名配置 {$attr['id']}", $lgcRet['data'], AUTH_OPER_GF_GAPPDOMAIN_DEL);

        // 修改内容，更新分享后台配置文件
        $lgcRet = $confLgc->refrashShareConfFile($gameId);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        $this->ajaxReturn($retData);
    }

    /**
     * 人工排查
     * @author Carter
     */
    public function gameAppManual()
    {
        $viewAssign = array();

        $appidMod = new GameShareAppidModel();
        $domainMod = new GameShareDomainModel();

        // 页面 title
        $viewAssign['title'] = "游戏配置 | 产品配置";

        $gameId = C('G_USER.gameid');

        // 有效AppID列表
        $attr = array(
            'game_id' => $gameId,
            'status' => $appidMod::STATUS_NORMAL,
        );
        $modRet = $appidMod->queryGameShareAppidListByAttr($attr, 'id,appid');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign['appidList'] = $modRet['data'];
        }

        // 有效域名列表
        $attr = array(
            'game_id' => $gameId,
            'status' => $domainMod::STATUS_NORMAL,
        );
        $modRet = $domainMod->queryGameShareDomainListByAttr($attr, 'id,link');
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $viewAssign['errMsg'] = $modRet['msg'];
        } else {
            $viewAssign['domainList'] = $modRet['data'];
        }

        $conf = array(
            'conf_id' => 0,
            'appid' => '',
            'link_id' => 0,
            'link' => '',
        );
        $filename = ROOT_PATH."FileUpload/shareConf/shareConfForManual.json";
        if (is_file($filename)) {
            $fileConf = json_decode(file_get_contents($filename), true);
            if ($fileConf && isset($fileConf)) {
                $conf = $fileConf;
            }
        }
        $viewAssign['conf'] = $conf;

        // icon url 前缀
        $viewAssign['imgUrlPrefix'] = C('RESOURCE_SERVER_IPHOST').':'.C('RESOURCE_SERVER_PORT').'/';

        $this->assign($viewAssign);
        $this->display();
    }

    /**
     * 更新人工配置文件
     * @author Carter
     */
    public function ajaxUpdateManualShareConf()
    {
        $this->checkIsAjax();

        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $confLgc = new GameconfLogic();

        // 参数校验
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('type', 0, array(
                array('require', null, '类型缺失'),
                array('in', '1,2', '类型参数错误'),
            )),
            array('id', 0, array(
                array('require', null, 'id缺失'),
                array('integer', null, 'id参数错误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        // 更新分享后台配置文件
        $lgcRet = $confLgc->refrashManualShareConfFile($attr);
        if (ERRCODE_SUCCESS !== $lgcRet['code']) {
            $retData['code'] = $lgcRet['code'];
            $retData['msg'] = $lgcRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        set_operation("更新人工排查配置内容 ", $attr);

        $this->ajaxReturn($retData);
    }
}
