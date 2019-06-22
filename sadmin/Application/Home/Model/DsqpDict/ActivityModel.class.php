<?php
namespace Home\Model\DsqpDict;
use Common\Service\ApiService;
use Think\Model;

class ActivityModel extends Model
{
    // 初始配置
    protected $connection = 'GAME_DICT_DB';
    protected $trueTableName = 'dict_activity';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取地区列表
     * @author liyao
     */
    public function queryInfoById($id)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try{
            $field = "*";
            $where = array('id'=>$id);
            $info = $this->field($field)->where($where)->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryInfoById] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $info;
        return $ret;
    }

    /**
     * 取得路径
     * @author liyao
     */
    public function updateInfo($id, $data, $gameid='')
    {
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
    public function deleteInfo($id)
    {
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
