<?php
namespace Home\Model\DsqpDict;

use Common\Service\ApiService;
use Think\Model;

class TaskModel extends Model
{
    // 初始配置
    protected $connection = 'GAME_DICT_DB';
    protected $trueTableName = 'dict_task';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取奖励列表
     * @author liyao
     */
    public function queryRewardById($id)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try{
            $field = "id,eventNum,reward";
            $where = array('activityId'=>$id, 'status'=>1, 'eventId'=>1);
            $info = $this->field($field)->where($where)->order('ui ASC')->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryRewardById] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $info;
        return $ret;
    }

    /**
     * 添加配置信息
     * @author liyao
     */
    public function addRewardInfo($data) {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $updateData = array(
            'eventId'=>1,
            'status'=>1,
            'activityId'=>$data['activityId'],
            'eventNum'=>$data['num'],
            'reward'=>$data['reward'],
            'ui'=>$data['no'],
            'name'=>'',
            'createTime'=>date("Y-m-d H:i:s"),
            'updateTime'=>date("Y-m-d H:i:s"),
        );
        $arr = array();
        try{
            $this->data($updateData)->add();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[addRewardInfo] ".$e->getMessage());
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
    public function deleteInfo($id)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $arr = array();
        try{
            $this->where('activityId=' . $id)->delete();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[deleteInfo] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_DELETE_ERR;
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
}
