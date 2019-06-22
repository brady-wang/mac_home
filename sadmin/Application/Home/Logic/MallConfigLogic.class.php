<?php

namespace Home\Logic;

use Common\Service\DbLoadConfigService; //加载外库服务
use Home\Model\DsqpActivity\MallModel; //商城表服务
use Home\Model\DsqpActivity\RewardsModel; //商城表红包规则
use Home\Model\DsqpActivity\UMallLogModel; //商城表服务
use Home\Model\GameDev\UUserInfoModel; //游戏主库
use Common\Service\ValidatorService;

/**
 * 商城商品列表逻辑
 *
 * @author tangjie
 */
class MallConfigLogic {

    private $mall_type = array(0=>'红包', 1=>'普通商品', 2=>'虚拟商品');
    /*
     * 获取商量列表逻辑
     */
    public function getMallGoodsListLogic($goodsWhere = array()) {
        $result = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => ''
        );


        //连接外库。
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_ACTIVITY_DB', 0)) {

            try {
                $goodsModel = new MallModel();
            } catch (\Exception $e) { //初始化数据库model失败
                $result['msg'] = "数据库连接失败，错误信息：" . $e->getMessage();
                $result['code'] = ERRCODE_DB_DATA_EMPTY;
                return $result;
            }
        } else {

            $result['msg'] = "数据库配置加载失败，请确认数据库配置信息";
            $result['code'] = ERRCODE_DB_DATA_EMPTY;
            return $result;
        }




        $goodsList = $goodsModel->getGoodsListByPage($goodsWhere);


        return $goodsList;
    }
    /**
    * 商城表主键ID逻辑
    *
    * @author tangjie
    */
    public function getMallInsertIdLogic() {
        $result = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => ''
        );


        //连接外库。
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_ACTIVITY_DB', 0)) {

            try {
                $goodsModel = new MallModel();
            } catch (\Exception $e) { //初始化数据库model失败
                $result['msg'] = "数据库连接失败，错误信息：" . $e->getMessage();
                $result['code'] = ERRCODE_DB_DATA_EMPTY;
                return $result;
            }
        } else {

            $result['msg'] = "数据库配置加载失败，请确认数据库配置信息";
            $result['code'] = ERRCODE_DB_DATA_EMPTY;
            return $result;
        }

        $insertid= $goodsModel->getGoodsInsertId();


        return $insertid;
    }


    /**
    * 商城表主键ID逻辑
    *
    * @author tangjie
    */
    public function saveGoodsInfoLogic($post,$goodsId = 0 ) {
        $result = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => ''
        );

        if(empty($post)){
            $post = I('post.', '', 'trim');
        }
        $type = (int) I('post.type') ;
        $priceType = (int) I('post.priceType') ;
        $postPrice = (int) I('post.price') ;
        $price = $priceType.":". $postPrice;


        $data = array(
            'gameId' => C('G_USER.gameid') ,
            'name' => $post['name'],
            'image' => $post['image'],
            'isHot' => (int)  $post['isHot'],
            'status' => 1, //默认上架
            'type' => 1, //类型（1 钻石区 2 兑换券区）
//            'item' => '',
            'price' => $price ,
            'ui' => 1,
            'total' => -1 ,
            'limitType' => 0,
            'limitNum' => 0,
        );


        //连接外库。
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_ACTIVITY_DB', 0)) {

            try {
                $goodsModel = new MallModel();
                $rewardModel = new RewardsModel();
            } catch (\Exception $e) { //初始化数据库model失败
                $result['msg'] = "数据库连接失败，错误信息：" . $e->getMessage();
                $result['code'] = ERRCODE_DB_DATA_EMPTY;
                return $result;
            }
        } else {

            $result['msg'] = "数据库配置加载失败，请确认数据库配置信息";
            $result['code'] = ERRCODE_DB_DATA_EMPTY;
            return $result;
        }


        if($goodsId <= 0){

            //新增红包逻辑
            if($type == 0 ){
                $proba = I('post.probadata') ;
                $rewards = array(
                    'name' => $data['name'],
                    'reward' => ''
                );
                foreach ($proba as   $item){
                    $rewardsdata[] = array( (int) $item['percentVlue'],  (int) $item['downVlue'], (int) $item['upVlue']);
                }
                $rewards['reward'] = json_encode($rewardsdata);

                $rewardId = $rewardModel->addGoodsRewardsData($rewards);

            }

            $data['item'] = $rewardId['data'].":1";

            $result = $goodsModel->addGoodsData($data);


            $result['ret_type'] = 1;
            $result['ret_param1'] = $this->mall_type[$type];
        }else{

            //新增红包逻辑
            if($type == 0 ){
                $rewardId = (int)I('post.rewardid') ;
                $proba = I('post.probadata') ;
                $rewards = array(
                    'name' => $data['name'],
                    'reward' => ''
                );
                foreach ($proba as  $item){
                    $rewardsdata[] = array( (int) $item['percentVlue'],  (int) $item['downVlue'], (int) $item['upVlue']);
                }
                $rewards['reward'] = json_encode($rewardsdata);

                $rewardStatus= $rewardModel->updateGoodsRewardsData( $rewardId, $rewards);

            }

            $result = $goodsModel->updateGoodsData($goodsId ,$data );

            $result['ret_type'] = 2;
            $result['ret_param1'] = $this->mall_type[$type];

        }

        return $result;
    }

    /**
    * 获取单个商品信息
    *
    * @author tangjie
    */
    public function getMallGoodsInfoLogic($id) {
        $result = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => ''
        );
        if($id < 1 ){
                $result['msg'] = "ID参数错误";
                $result['code'] = ERRCODE_PARAM_INVALID;
                return $result;
        }

        //连接外库。
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_ACTIVITY_DB', 0)) {

            try {
                $goodsModel = new MallModel();
                $rewardModel = new RewardsModel();
            } catch (\Exception $e) { //初始化数据库model失败
                $result['msg'] = "数据库连接失败，错误信息：" . $e->getMessage();
                $result['code'] = ERRCODE_DB_DATA_EMPTY;
                return $result;
            }
        } else {

            $result['msg'] = "数据库配置加载失败，请确认数据库配置信息";
            $result['code'] = ERRCODE_DB_DATA_EMPTY;
            return $result;
        }

        $goodsInfo = $goodsModel->getGoodsInfoById($id);
        if(empty($goodsInfo)){
            $result['code'] = ERRCODE_PARAM_INVALID ;
            $result['msg'] = "ID参数错误";
        }else{
            $rewardData = explode(":", $goodsInfo['item']);

            list($rewardId,$rewardNumber) = (array) $rewardData ;

            $rewards = $rewardModel -> getRewardsInfoById($rewardId);
            $goodsInfo['rewardId']  = $rewardId ;
            $goodsInfo['rewardData'] = json_decode($rewards['reward']);
            $goodsInfo['rewardNumber'] = $rewardNumber;

            $priceData =  explode(":", $goodsInfo['price']);
            list($priceType,$price) = (array) $priceData ;
            $goodsInfo['priceType'] = $priceType;
            $goodsInfo['price'] = $price;

            $result['data'] = $goodsInfo ;
        }

        return $result;
    }

    /**
     * 查询商城兑换逻辑
     * @author tangjie
     */
    public function getMallActListLogic($goodsWhere)
    {
        $result = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => ''
        );

        // 连接外库
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_ACTIVITY_LOG_DB', 0)) {
            try {
                $actLogModel = new UMallLogModel();
            } catch (\Exception $e) {
                $result['msg'] = "数据库连接失败，错误信息：" . $e->getMessage();
                $result['code'] = ERRCODE_DB_DATA_EMPTY;
                return $result;
            }
        } else {
            $result['msg'] = "数据库配置加载失败，请确认数据库配置信息";
            $result['code'] = ERRCODE_DB_DATA_EMPTY;
            return $result;
        }

        // 处理用户信息
        $logList = $actLogModel->getLogListByPage($goodsWhere);
        $dataList =& $logList['data']['data'];
        $userIds = array_column($dataList,'userId');
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_DEV_DB', 0)) {
            try {
                $userModel = new UUserInfoModel();
                $userList = $userModel->queryDevUserListByAttr(array('userId' => $userIds));
                foreach($userList['data'] as $userinfo ){
                    $userdata[$userinfo['userId']] = $userinfo['nickName'];
                }
            } catch (\Exception $e) {
                $result['msg'] = "查询用户列表失败，错误信息：" . $e->getMessage();
                $result['code'] = ERRCODE_DB_DATA_EMPTY;
                return $result;
            }
        } else {
            $result['msg'] = "数据库配置加载失败，请确认数据库配置信息";
            $result['code'] = ERRCODE_DB_DATA_EMPTY;
            return $result;
        }

        foreach($dataList as $key => $item){
            $dataList[$key]['username'] = $userdata[$item['userId']] ? $userdata[$item['userId']] : '-';
        }

        return $logList;
    }

    /**
    * 更新商城表逻辑
    *
    * @author tangjie
    */
    public function updateGoodsInfoLogic($goodsId = 0 ,$action ='') {
        $result = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => ''
        );

        $post = I('post.', '', 'trim');

        //连接外库。
        $confSer = new DbLoadConfigService();
        if (true === $confSer->load(C('G_USER.gameid'), 'GAME_ACTIVITY_DB', 0)) {

            try {
                $goodsModel = new MallModel();
            } catch (\Exception $e) { //初始化数据库model失败
                $result['msg'] = "数据库连接失败，错误信息：" . $e->getMessage();
                $result['code'] = ERRCODE_DB_DATA_EMPTY;
                return $result;
            }
        } else {

            $result['msg'] = "数据库配置加载失败，请确认数据库配置信息";
            $result['code'] = ERRCODE_DB_DATA_EMPTY;
            return $result;
        }

        if($goodsId <= 0){
            $result['msg'] = "参数错误";
            $result['code'] = ERRCODE_DB_DATA_EMPTY;
            return $result;

        }else{
            $action = $post['action'];
            $goodsInfo = $goodsModel->getGoodsInfoById($goodsId); //查询商品信息
            if($action == 'offGoods'){
                $data = array(
                    'status' => 0
                );
                $result = $goodsModel->updateGoodsData($goodsId ,$data );

                $result['ret_type'] = 1;
                $result['ret_param1'] = $this->mall_type[0];
                $result['ret_param2'] = $goodsInfo['name'];
            }else if($action == 'deleteGoods'){
                $result = $goodsModel->delGoodsData($goodsId );

                $result['ret_type'] = 2;
                $result['ret_param1'] = $this->mall_type[0];
                $result['ret_param2'] = $goodsInfo['name'];
            }
        }

        return $result;
    }
}
