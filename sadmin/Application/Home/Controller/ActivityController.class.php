<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Controller;

use Common\Service\ValidatorService;
use Common\Service\ApiService;
use Home\Logic\ActivityLogic;
use Home\Model\Activity\ActivityListModel;
use Home\Model\Activity\BagListModel;


/**
 * Description of ActivityController
 *
 * @author SDF-KaiF
 */
class ActivityController extends BaseController{

    public function __construct()
    {
        parent::__construct();

        $this->assignBaseData();
    }

    /*****************活动配置***************************/

    /**
     * 获取活动列表
     * author rave.xiong
     */
    public function actList()
    {
        $params['start_time'] = I('request.stime');
        $params['end_time'] = I('request.etime');
        $params['name'] = I('request.name');

        //前端时间选项组建默认值
        $data['stime'] = $params['start_time'];
        $data['etime'] = $params['end_time'];
        $data['name'] = $params['name'];

        $actMod = new ActivityLogic();
        $data['actData'] = $actMod->getActList($params);
        $data['actConf'] = $actMod->getActConf();
        $data['gameData'] = $actMod->getGameList($params);

        $this->assign($data);
        $this->display();
    }

    /**
     * 活动配置
     * author rave.xiong
     */
    public function actSet()
    {
        // 校验输入
        $params = I('post.', '', 'trim');
        if (!isset($params['act_switch'])) {
            $this->ajaxReturn(return_format(ERRCODE_VALIDATE_FAILED, '开关参数不能为空'));
        }

        if (empty($params['act_list'])) {
            $this->ajaxReturn(return_format(ERRCODE_VALIDATE_FAILED, '活动地址不能为空'));
        }

        if (empty($params['act_request']['login']) || empty($params['act_request']['club'])) {
            $this->ajaxReturn(return_format(ERRCODE_VALIDATE_FAILED, '请求地址不能为空'));
        }

        // 更改后端配置数据库
        $actLogic = new ActivityLogic();
        $modRet = $actLogic->updateSrvConf($params);

        // 记录操作流水
        $record = [
            'operateUser' => C("G_USER.username"),
            'operateInfo' => 'updateActSet',
            'operateData' => $params,
        ];
        set_operation("修改活动总配置", $record);

        $this->ajaxReturn($modRet);
    }


    /**
     * 活动配置
     * author rave.xiong
     */
    public function actDetail()
    {
        $viewAssign = [];

        $actLgc = new ActivityLogic();
        $activityMod = new ActivityListModel();

        $actId = I('request.actId');
        $pid = cookie('sadmin_gid');
        $modRet = $activityMod->getActivityInfo($actId, $pid);
        $viewAssign['actConf'] = $modRet['data'];
        $viewAssign['gameData'] = $actLgc->getGameList();
        $this->assign($viewAssign);
        $this->display("Activity/". $actId. "/act_conf");
    }

    public function ajaxEdtActConf()
    {
        $this->checkIsAjax();

        $retData = [
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => [],
        ];

        // 操作权限校验
        if (!in_array(AUTH_OPER_ACT_AVTIVITY_OPER, C("G_USER.operate"))) {
            $retData['code'] = ERRCODE_OPER_UNAUTH;
            $retData['msg'] = '操作授权校验未通过';
            $this->ajaxReturn($retData);
        }

        $vldSer = new ValidatorService();
        $apiSer = new ApiService();
        $actMod = new ActivityListModel();

        // 校验输入
        $attr = I('post.', '', 'trim');
        $rules = array(
            array('act_id', 0, array(
                array('integer', null, '活动id参数错误'),
            )),
            array('pageLimit', 0, array(
                array('integer', null, '分页参数错误'),
            )),
            array('cashlimit', 0, array(
                array('integer', null, '现金奖池上限参数错误'),
            )),
            array('replacebid', 0, array(
                array('integer', null, '普通礼包ID参数错误'),
            )),
        );

        $confTimes = [
            'logPlayTime' => strtotime($attr['logtime']),
            'showCloseTime' => strtotime($attr['closetime']),
            'stime' => strtotime($attr['stime']),
            'etime' => strtotime($attr['etime']),
        ];
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        } elseif (array_search(min($confTimes), $confTimes) != 'logPlayTime') {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = '活动牌局统计开始时间最前';
            $this->ajaxReturn($retData);
        } elseif (array_search(max($confTimes), $confTimes) != 'showCloseTime') {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = '活动展示结束时间最后';
            $this->ajaxReturn($retData);
        } elseif ($confTimes['stime'] >= $confTimes['etime']) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = '活动任务开始时间不能大于等于结束时间';
            $this->ajaxReturn($retData);
        }

        $attr['pid'] = cookie('sadmin_gid');
        $param = [
            'pid' => cookie('sadmin_gid'),
            'act_id' => $attr['act_id'],
            'name' => $attr['act_name'],
            'start_time' => strtotime($attr['stime']),
            'end_time' => strtotime($attr['etime']),
            'act_conf' => json_encode([
                'pageLimit' => $attr['pageLimit'],
                'wxCode' => $attr['act_wxcode'],
                'gameName' => $attr['act_gamename'],
                'logPlayTime' => strtotime($attr['logtime']),
                'showCloseTime' => strtotime($attr['closetime']),
                'replaceBid' => $attr['replacebid'],
                'cashLimit' => $attr['cashlimit'],
                'EggTask' => [
                    ['eggType' => 1, 'eggId' => (int)$attr['egg_id1'], 'task' => (int)$attr['task1']],
//                    ['eggType' => 2, 'eggId' => (int)$attr['egg_id2'], 'task' => (int)$attr['task2']],
//                    ['eggType' => 3, 'eggId' => (int)$attr['egg_id3'], 'task' => (int)$attr['task3']],
//                    ['eggType' => 4, 'eggId' => (int)$attr['egg_id4'], 'task' => (int)$attr['task4']],
//                    ['eggType' => 5, 'eggId' => (int)$attr['egg_id5'], 'task' => (int)$attr['task5']],
                    ['eggType' => 6, 'eggId' => (int)$attr['egg_id6'], 'task' => (int)$attr['task6']],
//                    ['eggType' => 7, 'eggId' => (int)$attr['egg_id7'], 'task' => (int)$attr['task7']],
//                    ['eggType' => 8, 'eggId' => (int)$attr['egg_id8'], 'task' => (int)$attr['task8']],
                    ['eggType' => 9, 'eggId' => (int)$attr['egg_id9'], 'task' => (int)$attr['task9']],
//                    ['eggType' => 9, 'eggId' => (int)$attr['egg_id10'], 'task' => (int)$attr['task10']]
                ]
            ])
        ];

        $modRet = $actMod->updateActConf($param);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        $url = '/ApiSadmin/setActInfo';
        $confData = [
            'aid' => (int)$attr['act_id'],
            'pid' => (int)cookie('sadmin_gid'),
            'name' => $attr['act_name'],
            'stime' => $attr['stime'],
            'etime' => $attr['etime'],
            'showCloseTime' => $attr['closetime'],
            'logPlayTime' => $attr['logtime'],
            'reward_List_limit' => (int)$attr['pageLimit'],
            'tableLogLevel' => 8,
            'replaceBid' => (int)$attr['replacebid'],
            'cashLimit' => (int)$attr['cashlimit'],
            'task' => [
                (int)$attr['task1'] => 1,
//                (int)$attr['task2'] => 2,
//                (int)$attr['task3'] => 3,
//                (int)$attr['task4'] => 4,
//                (int)$attr['task5'] => 5,
                (int)$attr['task6'] => 6,
//                (int)$attr['task7'] => 7,
//                (int)$attr['task8'] => 8,
//                (int)$attr['task9'] => 9,
                (int)$attr['task9'] => 9
            ],
            'maxTask1Play' => (int)$attr['task8'],
            'wxCode' => $attr['act_wxcode'],
            'gameName' => $attr['act_gamename'],
            'bagList' => [
                1 => (int)$attr['egg_id1'],
//                2 => (int)$attr['egg_id2'],
//                3 => (int)$attr['egg_id3'],
//                4 => (int)$attr['egg_id4'],
//                5 => (int)$attr['egg_id5'],
                6 => (int)$attr['egg_id6'],
//                7 => (int)$attr['egg_id7'],
//                8 => (int)$attr['egg_id8'],
                9 => (int)$attr['egg_id9']
            ]
        ];

        $host1 = C('ACT_API1_CONFURL');
        $port1 = C('ACT_API1_CONFPORT');

        $host2 = C('ACT_API2_CONFURL');
        $port2 = C('ACT_API2_CONFPORT');

        $serRet = $apiSer->actSendConfFile($host1, $port1, $url, $confData);
        if (ERRCODE_SUCCESS !== $serRet['code']) {
            $retData['code'] = $serRet['code'];
            $retData['msg'] = $serRet['msg'];
            $this->ajaxReturn($retData);
        }

        $serRet = $apiSer->actSendConfFile($host2, $port2, $url, $confData);
        if (ERRCODE_SUCCESS !== $serRet['code']) {
            $retData['code'] = $serRet['code'];
            $retData['msg'] = $serRet['msg'];
            $this->ajaxReturn($retData);
        }

        // 记录操作流水
        $record = [
            'operateUser' => C("G_USER.username"),
            'operateInfo' => 'updateActConf',
            'operateData' => $confData,
        ];
        set_operation('修改活动(' .$attr['act_id']. ')配置', $record);
        $this->ajaxReturn($serRet);
    }

    /**
     * 活动数据分析
     * author rave.xiong
     */
    public function actAnalysis()
    {
        $url = '/ApiSadmin/getActInfo';
        $params['product_id'] = cookie('sadmin_gid');
        $params['act_id'] = I('request.actId');

        $apiSer = new ApiService();
        $data = $apiSer->actGetAnalysis($url, $params);
        $this->assign(['actData' => $data['data'], 'searchType' => 'all', 'actId' => $params['act_id']]);
        $this->display("Activity/". $params['act_id']. "/act_analysis");
    }

    /**
     * 活动数据分析
     * author rave.xiong
     */
    public function actAnalysisByUid()
    {
        $viewAssign = [];

        $url = '/ApiSadmin/getActInfoByUid';
        $params['product_id'] = cookie('sadmin_gid');
        $params['act_id'] = I('request.actId');
        $params['user_id'] = I('request.userId');
        $viewAssign['searchType'] = 'user';
        $viewAssign['actId'] = $params['act_id'];

        $viewAssign['query'] = json_encode(['userId' => $params['user_id']]);

        if (empty($params['user_id'])) {
            $viewAssign['errMsg'] = '请输入用户id';
        } else {
            $apiSer = new ApiService();
            $data = $apiSer->actGetAnalysis($url, $params);
            $viewAssign['actData'] = $data['data'];
        }

        $this->assign($viewAssign);
        $this->display("Activity/". $params['act_id']. "/act_analysis");
    }


    /**
     * 添加活动
     * author rave.xiong
     */
    public function actSave()
    {
        // 校验输入
        $params = I('post.', '', 'trim');
        $rules = [
            ['act_id', 0, [
                ['require', null, '未填写活动名称'],
                ['integer', null, '活动ID必须为数字'],
            ]],
            ['name', 0, [
                ['require', null, '未填写活动名称'],
                ['len_max', "32", '活动名称不能超过 32 个字符'],
            ]],
            ['pid', 0, [
                ['require', null, '至少要勾选一个访问权限'],
                ['array', null, '参数错误'],
            ]]
        ];

        $vldSer = new ValidatorService();
        $vRet = $vldSer->exce($params, $rules);
        if (true !== $vRet) {
            $ret = return_format(ERRCODE_VALIDATE_FAILED, $vRet);
            $this->ajaxReturn($ret);
        }

        //数据保存
        $actMod = new ActivityLogic();
        $rs = $actMod->saveActInfo($params);
        if ($rs) {
            $code = 0;
            $msg = '保存成功';
        } else {
            $code = ERRCODE_VALIDATE_FAILED;
            $msg = '保存失败';
        }

        $record = [
            'operateUser' => C("G_USER.username"),
            'operateInfo' => 'addActConf',
            'operateData' => $params,
        ];
        set_operation('新增活动配置', $record);

        $ret = return_format($code, $msg);
        $this->ajaxReturn($ret);
    }

    /**
     * 图片上传
     * author rave.xiong
     */
    public function ajaxShareUploadBgImg()
    {
        $this->ajaxReturn($this->_shareUploadBgImg());
    }

    /**
     * 活动设置测试参数
     * author rave.xiong
     */
    public function setTestParams()
    {
        $params = I('request.', '', 'trim');
        if ($params) {
            $apiSer = new ApiService();
            switch($params['setType']) {
                case 1:
                    $ret = $apiSer->actSendConfFile('/PullNewUserByoil/setUserInfo', ['type' => 1]);
                    break;
                case 2:
                    //$sendData['testConf'] = ['setData' => $params['playDate']];
                    //$ret = $apiSer->actSendConfFile('/ApiSadmin/setActConf', $sendData);
                    break;
                case 3:
                    $ret = $apiSer->actSendConfFile('/PullNewUserByoil/setUserInfo', ['type' => 2]);
                    break;
                case 4:
                    $ret = $apiSer->actSendConfFile('/PullNewUserByoil/setUserInfo', ['type' => 3]);
                    break;
            }

            if ($ret['code'] == ERRCODE_SUCCESS) {
                //$ret = return_format($code, $msg);
                //$this->ajaxReturn($ret);
            }
        }
        $this->display("Activity/". $params['actId']. "/act_test_conf");
    }

    /**
     * 分享内容进行缩略图上传，分享配置、房间配置等多处用到
     * @author Carter
     */
    private function _shareUploadBgImg()
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



    /*****************礼包配置***************************/
    public function bagList()
    {
        $data['name'] = I('request.name');
        $data['luck'] = I('request.luck');
        $data['optBy'] = I('request.optBy');
        $data['bagId'] = I('request.bagId');

        $actMod = new ActivityLogic();
        $data['bagData'] = $actMod->getBagList($data);

        $bagModel = new BagListModel();
        $data['luckMap'] = $bagModel->luckInfo;

        $this->assign($data);
        $this->display();
    }

    public function getBagInfoById()
    {
        $data['bagId'] = I('post.bagId', '', 'trim');

        $bagModel = new BagListModel();
        $data['luckMap'] = $bagModel->luckInfo;
        $data['bagInfo'] = $bagModel->where('id='. $data['bagId'])->find();
        if ($data['bagInfo']) {
            $data['bagInfo']['data'] = json_decode($data['bagInfo']['data']);
        }
        $this->ajaxReturn(return_format(0, $data));
    }

    public function delBagById()
    {
        $save['id'] = I('post.bagId', '', 'trim');
        $save['status'] = 1;

        $bagModel = new BagListModel();
        $rs = $bagModel->save($save);

        // 记录操作流水
        $record = [
            'operateUser' => C("G_USER.username"),
            'operateInfo' => 'delBagConf',
            'operateData' => $save['id'],
        ];
        set_operation('删除礼包(' .$save['id']. ')', $record);

        $this->ajaxReturn(return_format($rs ? ERRCODE_SUCCESS : ERRCODE_DB_DELETE_ERR));
    }

    public function bagSave()
    {
        // 校验输入
        $params = I('post.', '', 'trim');
        $rules = [
            ['name', 0, [
                ['require', null, '未填写礼包名称'],
            ]],
            ['luck', 0, [
                ['require', null, '未填写礼包类型']
            ]]
        ];

        $vldSer = new ValidatorService();
        $vRet = $vldSer->exce($params, $rules);
        if (true !== $vRet) {
            $ret = return_format(ERRCODE_VALIDATE_FAILED, [], $vRet);
            $this->ajaxReturn($ret);
        }

        if (empty($params['data'])) {
            $ret = return_format(ERRCODE_VALIDATE_FAILED, [], '礼包内容为空');
            $this->ajaxReturn($ret);
        }

        $request = ['name' => '礼包名称', 'type' => '礼包类型', 'val' => '数量', 'percent' => '概率'];
        foreach ($request as $key => $val) {
            foreach ($params['data'][$key] as $k => $v) {
                if ($v == '') {
                    $ret = return_format(ERRCODE_VALIDATE_FAILED, [], '子礼包'. ($k+1) .'：'. $val .'不能为空');
                    $this->ajaxReturn($ret);
                }
            }
        }

        if ($params['luck'] == 2) {
            $total = array_sum($params['data']['percent']);
            if ((strval($total) != '100')) {
                $ret = return_format(ERRCODE_VALIDATE_FAILED, [], '抽奖礼包概率不为100%');
                $this->ajaxReturn($ret);
            }
        }

        $actMod = new ActivityLogic();
        $ret = $actMod->saveBagInfo($params);
        if (!$ret) {
            $ret = return_format(ERRCODE_VALIDATE_FAILED, [], '保存失败！');
            $this->ajaxReturn($ret);
        }

        $apiSer = new ApiService();

        $host1 = C('ACT_API1_CONFURL');
        $port1 = C('ACT_API1_CONFPORT');

        $host2 = C('ACT_API2_CONFURL');
        $port2 = C('ACT_API2_CONFPORT');

        $rs = $apiSer->actSendConfFile($host1, $port1, '/ApiSadmin/setBagInfo', $ret);
        if (!$rs) {
            $ret = return_format(ERRCODE_VALIDATE_FAILED, [], '保存失败！');
            $this->ajaxReturn($ret);
        }

        $rs = $apiSer->actSendConfFile($host2, $port2, '/ApiSadmin/setBagInfo', $ret);
        if (!$rs) {
            $ret = return_format(ERRCODE_VALIDATE_FAILED, [], '保存失败！');
            $this->ajaxReturn($ret);
        }

        // 记录操作流水
        $record = [
            'operateUser' => C("G_USER.username"),
            'operateInfo' => !empty($params['id']) ? 'updateBagConf' : 'addBagConf',
            'operateData' => $params,
        ];
        set_operation(!empty($params['id']) ? '修改礼包(' .$params['id']. ')配置' : '新增礼包配置', $record);
        $this->ajaxReturn($rs);
    }

    /*****************实物奖品配置***************************/
    public function rewardList()
    {
        $params['startDate'] = I('request.stime');
        $params['endDate'] = I('request.etime');
        $params['actName'] = I('request.actName');

        //前端时间选项组建默认值
        $data['stime'] = $params['startDate'];
        $data['etime'] = $params['endDate'];
        $data['actName'] = $params['actName'];

        $actMod = new ActivityLogic();
        $data['rewardData'] = $actMod->getRewardList($params);

        $this->assign($data);
        $this->display();
    }


}
