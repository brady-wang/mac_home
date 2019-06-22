<?php
namespace Common\Service;

/**
 * 阿里云CDN接口服务
 * @author tangjie
 */
class AliAPIService
{
    private $AccessKeyId = ''; // aliyun 的 APPID
    private $AccessKeySecret = ''; // aliyun 的 Appsecret

    public function __construct($param)
    {
        $this->AccessKeyId = $param['AccessKeyId'] ? $param['AccessKeyId'] : C('ALIYUN_CDN_ACCESSKEYID');
        $this->AccessKeySecret = $param['AccessKeySecret'] ? $param['AccessKeySecret'] : C('ALIYUN_CDN_ACCESSSECRET');
    }

    /**
     * 刷新阿里云CDN缓存数据
     * @author tangjie
     * CND加速 回源地址：client-update-resource.stevengame.com
     * CND加速  地址  ： client-update.stevengame.com
     * API 账号密码：
     * cdn_api_user
     * AccessKeyID: LTA******hk1
     * AccessKeySecret:  dN8yXD************bL3pS3
     * https://help.aliyun.com/document_detail/27155.html?spm=5176.10695662.1996646101.searchclickresult.47c1a614STOmp2
     */
    public function refushAliYunCdnCache($fileUrl)
    {
        $action = 'RefreshObjectCaches';
        $querParam = array(
            'AccessKeyId' => $this->AccessKeyId,
            'Action' => $action,
            'ObjectPath' => $fileUrl,
            'ObjectType' => 'File'
        );

        $parameter = array();
        $parameter = $this->_setParameter($querParam);
        $url = $this->_getUrlToSign($parameter, $this->AccessKeySecret);

        // 请求接口
        $result = $this->curlHttpGet($url);
        return $result;
    }

    /**
     * 阿里云签名逻辑
     * 获取阿里云CDN 接口签名URL
     * @param array $parameter 签名的所有参数
     * @param string $secret 阿里云签名的私钥
     */
    private function _getUrlToSign($parameter, $access_key_secret)
    {
        ksort($parameter);
        foreach ($parameter as $key => $value) {
            $str[] = rawurlencode($key)."=".rawurlencode($value);
        }
        $ss = "";
        if (!empty($str)) {
            for ($i = 0; $i < count($str); $i++) {
                if (!isset($str[$i + 1])) {
                    $ss .= $str[$i];
                } else
                    $ss .= $str[$i]."&";
            }
        }
        $StringToSign = "GET"."&".rawurlencode("/")."&".rawurlencode($ss);

        $signature = base64_encode(hash_hmac("sha1", $StringToSign, $access_key_secret."&", true));

        $url = "https://cdn.aliyuncs.com/?".$ss."&Signature=".$signature;
        return $url;
    }

    /**
     *  阿里云签名逻辑
     *  处理需要请求接口的参数并且和私有参数合并
     *  @param array  $specialParameter 独立接口的私有参数
     */
    private function _setParameter($specialParameter)
    {
        // 时间戳必须要按照标准时区传递，本程序设置了东八区所以需要减掉8小时
        $time = date('Y-m-d H:i:s', time() - 8 * 3600);

        $var = strtr($time, ' ', 'T');
        $Timestamp = $var . 'Z';

        $signature_nonce = '';
        for ($i = 0; $i < 14; $i++) {
            $signature_nonce .= mt_rand(0, 9);
        }
        // 公共参数，每个接口都需要传递
        $publicParameter = array(
            'Format' => 'JSON',
            'Version' => '2014-11-11',
            'SignatureMethod' => 'HMAC-SHA1',
            'TimeStamp' => $Timestamp,
            'SignatureVersion' => '1.0',
            'SignatureNonce' => $signature_nonce,
        );
        // 合并参数
        $parameter = array_merge($publicParameter, (array) $specialParameter);
        return $parameter;
    }

    /**
     *  http 调用阿里云CDN服务器缓存
     *  @param string  $url 请求的URL地址
     */
    public function curlHttpGet($url, $SSL = false)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // 记录日志
        if( empty( $this->AccessKeyId ) || empty($this->AccessKeySecret)  ){
            set_exception(__FILE__, __LINE__, "[AliYunCdnAPI-curlHttpGet] 阿里云配置信息错误: AccessKeyId：【{$this->AccessKeyId}】，AccessKeySecret：【{$this->AccessKeySecret}】");
            $ret['code'] = ERRCODE_PARAM_INVALID;
            return $ret;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $SSL); // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $SSL);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_HEADER, 0); // 不要http header 加快效率
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $output = curl_exec($ch);
        curl_close($ch);

        $output = json_decode($output);

        if ($output->Code != '') {
            $ret['code'] = ERRCODE_PARAM_INVALID;
            $ret['msg'] = "刷新CDN缓存接口失败：" . $output->Message;

            set_exception(__FILE__, __LINE__, "[AliYunCdnAPI-curlHttpGet] {$url} connect failed: ".var_export($output, true));
        }

        return $ret;
    }
}
