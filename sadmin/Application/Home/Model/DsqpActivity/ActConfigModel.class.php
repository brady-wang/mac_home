<?php
namespace Home\Model\DsqpActivity;
use Common\Service\ApiService;
use Think\Model;

class ActConfigModel extends Model
{
    // 初始配置
    protected $connection = 'GAME_ACTIVITY_DB';
    protected $trueTableName = 'act_config';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取配置
     * @author liyao
     */
    public function getConfig($key)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try{
            $data = $this->field('sValue')->where(array('sKey'=>$key,'gameId'=>C('G_USER.gameid')))->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[getConfig] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $val = '';
        if ($data)
            $val = $data['sValue'];
        $ret['data'] = $val;
        return $ret;
    }

    /**
     * 更新配置
     * @author liyao
     */
    public function setConfig($key, $val, $desc = '') {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $arr = array();
        try{
            $ext = $this->field('sValue')->where(array('sKey'=>$key,'gameId'=>C('G_USER.gameid')))->find();
            if ($ext) {
                $this->where(array('sKey'=>$key,'gameId'=>C('G_USER.gameid')))->save(array('sValue'=>$val));
            } else {
                $this->data(array('sKey'=>$key,'sValue'=>$val,'gameId'=>C('G_USER.gameid'), 'sDesc'=>$desc))->add();
            }
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[setConfig] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $apiSer = new ApiService();
        // 调用服务端接口，刷新缓存
        $serRet = $apiSer->commonCleanCacheApi('/api/config?act=reload', 'activity');
        if (ERRCODE_SUCCESS !== $serRet['code']) {
            $ret['code'] = $serRet['code'];
            $ret['msg'] = $serRet['msg'];
            return $ret;
        }
        return $ret;
    }

}
