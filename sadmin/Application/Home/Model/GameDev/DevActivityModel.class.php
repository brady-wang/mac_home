<?php
namespace Home\Model\GameDev;
use Common\Service\ApiService;
use Think\Model;

class DevActivityModel extends Model
{
    // 初始配置
    protected $connection = 'GAME_DEV_DB';
    protected $trueTableName = 's_activity';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取列表
     * @author liyao
     */
    public function getActivityList()
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try{
            $activityList = $this->order('id ASC')->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[getActivityList] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $activityList;
        return $ret;
    }
    
    /**
     * 更新配置
     * @author liyao
     */
    public function updateInfo($id, $data, $gameid='') {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $arr = array();
        try{
            if ($id > 0)
                $this->where('id=' . $id)->save($data);
            else {
                $insertid = $this->data($data)->add();
                $ret['data'] = $insertid;
            }
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateInfo] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $apiSer = new ApiService();
        // 调用服务端接口，刷新缓存
        $serRet = $apiSer->kaifangApiQuery('/console/?act=reload', $gameid);
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
    public function deleteInfo($id) {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $arr = array();
        try{
            $this->where('id=' . $id)->delete();
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
