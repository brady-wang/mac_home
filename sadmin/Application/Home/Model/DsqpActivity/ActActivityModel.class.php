<?php
namespace Home\Model\DsqpActivity;
use Common\Service\ApiService;
use Think\Model;

class ActActivityModel extends Model
{
    // 初始配置
    protected $connection = 'GAME_ACTIVITY_DB';
    protected $trueTableName = 'act_activity';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取配置
     * @author liyao
     */
    public function getData($type)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try{
            $data = $this->field('*')->where(array('type'=>$type,'gameId'=>C('G_USER.gameid')))->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[getData] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $data;
        return $ret;
    }
    
    /**
     * 更新配置
     * @author liyao
     */
    public function updateRedpackInfo($data) {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );
        $content = array();
        if (isset($data['title'])) {
            $content['title'] = urlencode(html_entity_decode($data['title']));
        }
        $content['money'] = 5;
        if (isset($data['record'])) {
            $content['playCount'] = urlencode($data['record']);
        }
        if (isset($data['code'])) {
            $content['wechat'] = urlencode(html_entity_decode($data['code']));
        }
        if (isset($data['area'])) {
            $content['province'] = urlencode(html_entity_decode($data['area']));
        }
        if (isset($data['display'])) {
            $content['displayActivityTime'] = urlencode($data['display']);
        }
        $updatedata = array();
        $updatedata['type'] = 700;
        $updatedata['gameId'] = C('G_USER.gameid');
        if (isset($data['stime'])) {
            $updatedata['startTime'] = $data['stime'];
        }
        if (isset($data['etime'])) {
            $updatedata['vanishTime'] = $updatedata['endTime'] = $data['etime'];
        }
        if (isset($data['active'])) {
            $updatedata['status'] = $data['active'];
        }
        $updatedata['content'] = urldecode(json_encode($content));

        $arr = array();
        try{
            $ext = $this->field('id')->where(array('type'=>700,'gameId'=>C('G_USER.gameid')))->find();
            if ($ext) {
                $this->where(array('id'=>$ext['id']))->save($updatedata);
            } else {
                $this->data($updatedata)->add();
            }
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateRedpackInfo] ".$e->getMessage());
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
