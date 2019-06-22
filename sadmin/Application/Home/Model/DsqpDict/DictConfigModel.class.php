<?php
namespace Home\Model\DsqpDict;
use Common\Service\ApiService;
use Think\Model;

class DictConfigModel extends Model
{
    // 初始配置
    public $yuanBaoDropRate = '2:50|3:75|4:100$4:50~100|8:50~100|16:100~200'; // 元宝掉率默认设置
    protected $connection = 'GAME_DICT_DB';
    protected $trueTableName = 'dict_config';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取配置
     * @param string $key 配置的键名
     * @param bool $isNull 是否判断配置存在 by tangjie
     * @author liyao
     */
    public function getConfig($key ,$isNull = false)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try{
            $data = $this->field('sValue')->where(array('sKey'=>$key, 'productId'=>C('G_USER.gameid')))->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[getConfig] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $val = $isNull ? null : '';
        if ($data)
            $val = $data['sValue'];
        $ret['data'] = $val;
        return $ret;
    }

    /**
     * 更新配置
     * @author liyao
     */
    public function setConfig($key, $val) {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try{
            $ext = $this->field('sValue')->where(array('sKey'=>$key, 'productId'=>C('G_USER.gameid')))->find();
            if ($ext) {
                $this->where(array('sKey'=>$key, 'productId'=>C('G_USER.gameid')))->save(array('sValue'=>$val, 'isValid'=>1));
            } else {
                $data = array('sKey'=>$key, 'productId'=>C('G_USER.gameid'),'sValue'=>$val);
                if ($key == 'coin_certificate') {
                    $data['sDesc'] = '生成兑换券';
                }
                $this->data($data)->add();
            }
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[setConfig] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $apiSer = new ApiService();
        // 调用服务端接口，刷新缓存
        $serRet = $apiSer->dsSvrApiGetQuery(C('G_USER.gameid'), '/console/?act=reload');
        if (ERRCODE_SUCCESS !== $serRet['code']) {
            $ret['code'] = $serRet['code'];
            $ret['msg'] = $serRet['msg'];
            return $ret;
        }
        return $ret;
    }


    /**
     * 通过接口修改配置
     * @author tangjie
     */
    public function setConfigByApi($key, $val) {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );



        $apiSer = new ApiService();
        // 调用服务端接口，刷新缓存
        $serRet = $apiSer->kaifangApiQuery('/api/sys/?act=setSConfig&sKey='.$key.'&sValue='.$val);
        if (ERRCODE_SUCCESS !== $serRet['code']) {
            $ret['code'] = $serRet['code'];
            $ret['msg'] = $serRet['msg'];
            return $ret;
        }
        return $ret;
    }

    /**
     * 判断该设置项是否启用
     * 兼容数据库字段isValid 此字段表示是否开启配置
     * @param string $key 配置的键名
     * @return array 0: 该条目不存在 1: 该条目存在且`isValid`为1且存在`sValue`配置 -1: 该条目存在且(`isValid`为-1或`sValue`为空)
     * @author daniel
     */

    public function getValid($key)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => 0,
        );

        if (empty($key) || !is_string($key)) {
            $ret['code'] = ERRCODE_PARAM_INVALID;
            $ret['msg'] = '参数错误';
            return $ret;
        }

        $where = [
            'productId' => C('G_USER.gameid'),
            'sKey' => $key
        ];
        try {
            $valid = $this->field('sValue,isValid')->where($where)->find();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[getConfig] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if (empty($valid)) {
            // 该条目不存在 返回0
            $ret['data'] = 0;
            return $ret;
        } elseif ((isset($valid['isValid']) && $valid['isValid'] == '1')
            && (isset($valid['sValue']) && !empty($valid['sValue']))) {
            // 该条目存在且`isValid`为1且存在`sValue`配置 返回1
            $ret['data'] = 1;
            return $ret;
        } elseif ((isset($valid['isValid']) && $valid['isValid'] == '-1')
            || (isset($valid['sValue']) && empty($valid['sValue']))) {
            // 该条目存在且(`isValid`为-1或`sValue`为空) 返回-1
            $ret['data'] = -1;
            return $ret;
        }
        return $ret;
    }

    /**
     * 更新开启关闭状态
     * @author daniel
     */
    public function updateValid($key, $valid)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => false,
        );

        if (!in_array($valid, [1, -1]) || !is_string($key)) {
            $ret['code'] = ERRCODE_PARAM_INVALID;
            $ret['msg'] = '参数错误';
            return $ret;
        }

        $where= [
            'productId' => C('G_USER.gameid'),
            'sKey' => $key
        ];
        try {
            $queryRet = $this->where($where)->save(['isValid' => $valid]);
        } catch (\Exception $e) {
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $apiSer = new ApiService();
        // 调用服务端接口，刷新缓存
        $serRet = $apiSer->dsActivityApiGetQuery(C('G_USER.gameid'), '/console/?act=reload');
        if (ERRCODE_SUCCESS !== $serRet['code']) {
            $ret['code'] = $serRet['code'];
            $ret['msg'] = $serRet['msg'];
            return $ret;
        }
        return $ret;
    }

}
