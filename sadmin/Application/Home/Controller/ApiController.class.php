<?php
namespace Home\Controller;

use Common\Service\ValidatorService;
use Home\Model\GameShareAppidModel;
use Home\Model\GameShareDomainModel;

class ApiController extends BaseController
{
    public function _initialize()
    {
    }

    /**
     * 查看数据字典
     */
    public function shareFeeback()
    {
        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $vldSer = new ValidatorService();
        $appidMod = new GameShareAppidModel();
        $domainMod = new GameShareDomainModel();

        // 参数校验
        $attr = I('get.', '', 'trim');
        $rules = array(
            array('conf_id', 0, array(
                array('require', null, '配置id参数缺失'),
                array('integer', null, '配置id参数错误'),
            )),
            array('link_id', 0, array(
                array('require', null, '域名id参数缺失'),
                array('integer', null, '域名id参数错误'),
            )),
            array('share_result', 0, array(
                array('require', null, '分享结果参数缺失'),
                array('in', '1,9', '分享结果参数错误'),
            )),
        );
        $vRet = $vldSer->exce($attr, $rules);
        if (true !== $vRet) {
            $retData['code'] = ERRCODE_VALIDATE_FAILED;
            $retData['msg'] = $vRet;
            $this->ajaxReturn($retData);
        }

        if (APP_DEBUG) {
            $logFilename = RUNTIME_PATH.'Logs/apiLogForshareFeeback.log';
            $logStr = "--------------------------------------------------\n";
            $logStr .= "query time: ".date('Y-m-d H:i:s')."\n";
            $logStr .= "request: ".var_export($attr, true)."\n\n\n";
            file_put_contents($logFilename, $logStr, FILE_APPEND);
        }

        if (1 != $attr['share_result']) {
            $this->ajaxReturn($retData);
        }

        $modRet = $appidMod->updateShareAppidCountInc($attr['conf_id']);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        $modRet = $domainMod->updateShareDomainCountInc($attr['link_id']);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $retData['code'] = $modRet['code'];
            $retData['msg'] = $modRet['msg'];
            $this->ajaxReturn($retData);
        }

        $this->ajaxReturn($retData);
    }
}
