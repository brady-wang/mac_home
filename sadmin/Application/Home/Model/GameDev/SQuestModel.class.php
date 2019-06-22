<?php
namespace Home\Model\GameDev;

use Think\Model;

class SQuestModel extends Model
{
    // 初始配置
    protected $connection = 'GAME_DEV_DB';
    protected $trueTableName = 's_quest';

    // 手机绑定奖励选项
    protected $bindPhoneOption = array(
        10008 => '钻石',
        10009 => '元宝'
    );

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取绑定手机奖励配置
     * @author daniel
     */
    public function getBindOption()
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => '',
            'data' => array(),
        );

        try{
            $configRet = $this->field('awardList')->where(array('Id'=>10029))->find();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[getConfig] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        if (empty($configRet['awardList'])) {
            $ret['data']['option'] = '';
            $ret['data']['value'] = 0;
            return $ret;
        }
        $config = explode(':', $configRet['awardList']);
        $ret['data']['option'] = $config[0];
        $ret['data']['value'] = $config[1];
        return $ret;
    }

    public function saveBindOption($data)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => '',
            'data' => array(),
        );

        $saveData = empty($data['type']) ? '' : implode(':', $data);
        try{
            $this->where(array('Id'=>10029))->save(array('awardList' => $saveData));
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[getConfig] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        return $ret;

    }
}