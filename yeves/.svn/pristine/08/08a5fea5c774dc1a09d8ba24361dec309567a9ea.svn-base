<?php
/**
 * Created by PhpStorm.
 * User: brady
 * Date: 2018/2/1
 * Time: 17:43
 */

if (!function_exists("dump")) {
    function dump($arr)
    {
        echo "<pre>";
        print_r($arr);
        echo "</pre>";
    }
}

if (!function_exists('get_public_domain')) {

    function get_public_domain()
    {
        $host = filter_input(INPUT_SERVER, 'HTTP_HOST');
        $host = preg_replace('/:\d+/', "", $host);
        $arrHost = explode('.', $host);
        $arrHostCount = count($arrHost);
        return $arrHostCount > 1 ? $arrHost[$arrHostCount - 2] . '.' . $arrHost[$arrHostCount - 1] : '';
    }

}

if(!function_exists("curl_get")){
    function curl_get($url, array $params = array(), $timeout = 5)
    {
        // 1. 初始化
        $ch = curl_init();
        // 2. 设置选项，包括URL
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HEADER,0);
        // 3. 执行并获取HTML文档内容
        $output = curl_exec($ch);


        // 4. 释放curl句柄
        curl_close($ch);
        if($output === FALSE ){
            return array();
        } else {
            return json_decode($output,true);
        }

    }
}

if(!function_exists('tps_curl_post2')){
    function tps_curl_post2($url, $postData) {
        $dataFormat = '';
        foreach($postData as $k=>$v){
            $v = @iconv("UTF-8","GBK", $v);
            if($v==''){
                $v='default';
            }
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
        return json_decode($result);
    }
}

if(!function_exists('tps_curl_post3')){
    function tps_curl_post3($url, $postData=array()) {
        $postData = json_encode($postData);
        $curl = curl_init();  //初始化
        curl_setopt($curl,CURLOPT_URL,$url);  //设置url
        curl_setopt($curl,CURLOPT_HTTPAUTH,CURLAUTH_BASIC);  //设置http验证方法
        curl_setopt($curl, CURLOPT_TIMEOUT,3);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);  //设置curl_exec获取的信息的返回方式
        curl_setopt($curl,CURLOPT_POST,1);  //设置发送方式为post请求
        curl_setopt($curl,CURLOPT_POSTFIELDS,$postData);  //设置post的数据

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($postData))
        );

        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result,true);
    }
}

if(!function_exists("curl_post")){
    function curl_post($url, array $params = array(), $timeout)
    {
        $ch = curl_init();//初始化
        curl_setopt($ch, CURLOPT_URL, $url);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        return ($data);
    }
}
if(!function_exists("curl_post_json")){
    function curl_post_json($url,$data = array())
    {
        $data_string = json_encode($data);
        $token='TPS20171114COM$PS';
        $ch = curl_init();//初始化
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json; charset=utf-8",
                "Content-Length: " . strlen($data_string),
                "token: ".$token)
        );
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        $return_content = curl_exec($ch);;
        return json_decode($return_content,true);

    }
}

