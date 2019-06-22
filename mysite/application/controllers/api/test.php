<?php

/**
 * Created by PhpStorm.
 * User: brady
 * Date: 2018/10/18
 * Time: 12:29
 */
class test extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    //优惠券列表
    public function test_coupons_log_erp()
    {
        $postData = array(
            'sign' 	=> api_mobile_create_sign('1460101942856'),
            'lan_id' =>2,
            'uid'=>'1390003009',
            //'coupon_id' =>1,
            //'coupon_name'=>'test',
            'user_coupon_status' =>0,
            'suffix' =>138,
            'debug' =>"ticowong",
        );
        $postData['token'] = $this->get_token($postData);

        $this->__generalPost(base_url('/api/erp/coupons_log'),$postData);
    }

    private function __generalPost($url, $postData) {
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_FOLLOWLOCATION => 1,

        );

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);
        echo ($result);
        //print_r(json_decode((Remove_UTF8_BOM($result))));
    }

    private function __generalPost2($url, $postData) {

        $dataFormat = '';
        foreach($postData as $k=>$v){
            $v = iconv("UTF-8","GBK", $v);
            $dataFormat.='&'.$k.'='.urlencode($v);
        }

        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => substr($dataFormat,1),
        );

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);
        echo '<pre/>';
        var_dump(json_decode($result));
    }

    private function __generalGet($url, $data) {

        $urlAndData = $url.'?1=1';
        foreach($data as $k=>$v){
            $urlAndData.='&'.$k.'='.$v;
        }
        $cu = curl_init();
        curl_setopt($cu, CURLOPT_URL, $urlAndData);
        curl_setopt($cu, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($cu);
        curl_close($cu);
        echo '<pre/>';
        print_r(json_decode($result));
    }
}