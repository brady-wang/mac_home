<?php
namespace Home\Model\DsqpActivity;

use Think\Model;
use Common\Service\ApiService;

class RewardsModel extends Model
{
    // 初始配置
    protected $connection = 'GAME_ACTIVITY_DB';
    protected $trueTableName = 'act_rewards';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 查询红包商品概率信息
     * @param int $id 查询ID
     * @return array|boole 返回单条数据或false
     * @author tangjie <tj_youxiang_2008@163.com>
     */
    public function getRewardsInfoById($id)
    {
        if($id < 1 ){

            $result =  array(
                'code' => ERRCODE_PARAM_INVALID  ,
                'data' => $info ,
                'msg' => '数据为空或id参数为空'
            );
            return $result ;

        }

        $where = array(
            'id' => (int)$id
        );
        $data = $this->where(  $where)->find();

        return $data;
    }

    /**
     * 删除商品信息
     * @param int $id 查询ID
     * @return array|boole 返回单条数据或false
     * @author tangjie <tj_youxiang_2008@163.com>
     */
    public function delGoodsData($id)
    {
        if($id < 1 ){
            $result =  array(
                'code' => ERRCODE_PARAM_INVALID  ,
                'data' => $info ,
                'msg' => 'id参数为空'
            );
            return $result ;
        }
        $where = array(
            'id' => (int)$id
        );
        $data = $this->where(  $where)->delete();

        $this->updateApiCache(); //重载配置
        return $data;
    }

    /**
     * 更新数据
     * @param int $date 默认显示数据
     * @return array|boole 返回单条数据或false
     * @author tangjie <tj_youxiang_2008@163.com>
     */
    public function updateGoodsRewardsData($id, $data)
    {
        if( empty( $data) || empty($id)){
            $result =  array(
                'code' => ERRCODE_PARAM_INVALID  ,
                'data' => $info ,
                'msg' => '数据为空或id参数为空'
            );
            return $result ;
        }

        $data['id'] = $id;

        $where= array(
            'id' => (int) $id
        );

        $info = $this->where($where)->save($data);
        if($info){
            $result =  array(
                'code' => ERRCODE_SUCCESS  ,
                'data' => $info ,
                'msg' => '修改成功'
            );
            $this->updateApiCache(); //重载配置
        }else{
            $result =  array(
                'code' => ERRCODE_DB_UPDATE_ERR  ,
                'data' => '' ,
                'msg' => '修改失败'
            );
        }
        return $result;
    }

    /**
     * 更新数据
     * @param int $date 默认显示数据
     * @return array|boole 返回单条数据或false
     * @author tangjie <tj_youxiang_2008@163.com>
     */
    public function addGoodsRewardsData($data)
    {
        if( empty( $data) ){
            $result =  array(
                'code' => ERRCODE_PARAM_INVALID  ,
                'data' => array(),
                'msg' => '数据为空或id参数为空'
            );
            return $result ;
        }

        $maxId = $this->max('id');
        $data['id'] = $maxId +1 ;

        $info = $this->add($data);

        if($info){
            $result =  array(
                    'code' => ERRCODE_SUCCESS  ,
                    'data' => $info ,
                    'msg' => '添加成功'
                );
            $this->updateApiCache(); //重载配置
        }else{
            $result =  array(
                'code' => ERRCODE_DB_UPDATE_ERR  ,
                'data' => '' ,
                'msg' => '添加失败'
            );
        }

        return $result;
    }

    //更新服务器缓存
    private function updateApiCache()
    {
        $api = new ApiService();
        return $api->commonCleanCacheApi('/api/config?act=reload','activity');  //重载兑换商城的Cache
    }
}
