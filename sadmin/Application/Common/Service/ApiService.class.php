<?php

namespace Common\Service;

use Home\Model\GameModel;

/**
 * 本服务集成了所有对外接口调用方法
 */
class ApiService
{
    /**
     * 大圣游戏服接口调用（POST），仅接收 json 格式字符串 response
     * @author Carter
     */
    public function dsSvrApiPostQuery($gameId, $uri, $request)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $gmMod = new GameModel();

        $field = 'game_status,api_ip,api_port';
        $modRet = $gmMod->queryGameInfoById($gameId, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $gmInfo = $modRet['data'];

        if ($gmMod::GAME_STATUS_ON != $gmInfo['game_status']) {
            $ret['code'] = ERRCODE_DATA_ERR;
            $ret['msg'] = "游戏 {$gameId} 已下线";
            return $ret;
        }

        // 地址端口
        if (empty($gmInfo['api_ip']) || empty($gmInfo['api_port'])) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = "未配置游戏服地址或端口";
            return $ret;
        }
        $iphost = $gmInfo['api_ip'];
        $port = $gmInfo['api_port'];

        $ch = curl_init();

        // URL
        curl_setopt($ch, CURLOPT_URL, $iphost.$uri);

        // 端口
        curl_setopt($ch, CURLOPT_PORT, $port);

        // POST 请求，application/x-www-form-urlencoded 头请求接口
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

        // 忽略 SSL 证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        // 返回重定向信息
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        // 重定向时自动设置头信息
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);

        // 请求的参数
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($request));

        // 以字符串返回
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // 连接等待不超过 10 秒
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        $output = curl_exec($ch);
        if (curl_errno($ch)) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = curl_error($ch);
            curl_close($ch);
            return $ret;
        }
        curl_close($ch);

        $apiRet = json_decode($output, true);
        if (is_null($apiRet)) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = "JSON解码失败：{$output}";
            return $ret;
        }

        // API 流水功能上线前，暂时记录到日志文件里面
        if (APP_STATUS != 'production') {
            \Think\Log::write("url {$uri}, request ".var_export($request, true).", response ".var_export($apiRet, true));
        }

        $ret['data'] = $apiRet;
        return $ret;
    }

    /**
     * 大圣游戏服接口调用（GET），仅接收 json 格式字符串 response (后续调用 dashengSvrApiGetQuery接口 需要传递url和param )
     * @author Carter
     */
    public function dsSvrApiGetQuery($gameId, $uri)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $gmMod = new GameModel();

        $field = 'game_status,api_ip,api_port';
        $modRet = $gmMod->queryGameInfoById($gameId, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $gmInfo = $modRet['data'];

        if ($gmMod::GAME_STATUS_ON != $gmInfo['game_status']) {
            $ret['code'] = ERRCODE_DATA_ERR;
            $ret['msg'] = "游戏 {$gameId} 已下线";
            return $ret;
        }

        // 地址端口
        if (empty($gmInfo['api_ip']) || empty($gmInfo['api_port'])) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = "未配置游戏服地址或端口";
            return $ret;
        }
        $iphost = $gmInfo['api_ip'];
        $port = $gmInfo['api_port'];

        $ch = curl_init();

        // URL
        curl_setopt($ch, CURLOPT_URL, $iphost.$uri);

        // 端口
        curl_setopt($ch, CURLOPT_PORT, $port);

        // 使用 GET 方法进行 HTTP 请求
        curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "GET");

        // 将 curl_exec() 获取的信息以字符串返回，而不是直接输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // 连接等待不超过 10 秒
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        $output = curl_exec($ch);
        if (curl_errno($ch)) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = curl_error($ch);
            curl_close($ch);
            return $ret;
        }
        curl_close($ch);

        $apiRet = json_decode($output, true);
        if (is_null($apiRet)) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = "JSON解码失败：{$output}";
            return $ret;
        }

        // API 流水功能上线前，暂时记录到日志文件里面
        if (APP_STATUS != 'production') {
            \Think\Log::write("url {$uri}, response {$output}");
        }

        $ret['data'] = $output;
        return $ret;
    }


    /**
     * 大圣游戏服接口调用 新（GET），仅接收 json 格式字符串 response
     * @author Carter
     * @param $gameId 游戏ID
     * @param $url url地址
     * @param 请求参数
     */
    public function dashengSvrApiGetQuery($gameId, $url, $param = [])
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        //url处理 拼接为get类型
        $fullUrl = !empty($param) ? $url . "?" . http_build_query($param) : $url;

        $gmMod = new GameModel();
        $field = 'game_status,api_ip,api_port';
        $modRet = $gmMod->queryGameInfoById($gameId, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $gmInfo = $modRet['data'];

        if ($gmMod::GAME_STATUS_ON != $gmInfo['game_status']) {
            $ret['code'] = ERRCODE_DATA_ERR;
            $ret['msg'] = "游戏 {$gameId} 已下线";
            return $ret;
        }

        // 地址端口
        if (empty($gmInfo['api_ip']) || empty($gmInfo['api_port'])) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = "未配置游戏服地址或端口";
            return $ret;
        }
        $iphost = $gmInfo['api_ip'];
        $port = $gmInfo['api_port'];

        $ch = curl_init();

        // URL
        curl_setopt($ch, CURLOPT_URL, $iphost . $fullUrl);

        // 端口
        curl_setopt($ch, CURLOPT_PORT, $port);

        // 使用 GET 方法进行 HTTP 请求
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

        // 将 curl_exec() 获取的信息以字符串返回，而不是直接输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // 连接等待不超过 10 秒
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        $output = curl_exec($ch);
        if (curl_errno($ch)) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = curl_error($ch);
            curl_close($ch);
            return $ret;
        }
        curl_close($ch);

        $apiRet = json_decode($output, true);

        // api接口流水
        set_apilog('game', $url, $param, is_null($apiRet) ? $output : $apiRet);

        if (is_null($apiRet)) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = "JSON解码失败：{$output}";
            return $ret;
        }

        $ret['data'] = $output;
        return $ret;
    }


    /**
     * 大圣活动服接口调用（GET）
     * @author Carter
     */
    public function dsActivityApiGetQuery($gameId, $uri)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $gmMod = new GameModel();

        $field = 'game_status,activity_api,activity_api_port';
        $modRet = $gmMod->queryGameInfoById($gameId, $field);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        $gmInfo = $modRet['data'];

        if ($gmMod::GAME_STATUS_ON != $gmInfo['game_status']) {
            $ret['code'] = ERRCODE_DATA_ERR;
            $ret['msg'] = "游戏 {$gameId} 已下线";
            return $ret;
        }

        // 地址端口
        if (empty($gmInfo['activity_api']) || empty($gmInfo['activity_api_port'])) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = "未配置游戏服地址或端口";
            return $ret;
        }
        $iphost = $gmInfo['activity_api'];
        $port = $gmInfo['activity_api_port'];

        $ch = curl_init();

        // URL
        curl_setopt($ch, CURLOPT_URL, $iphost.$uri);

        // 端口
        curl_setopt($ch, CURLOPT_PORT, $port);

        // 使用 GET 方法进行 HTTP 请求
        curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "GET");

        // 将 curl_exec() 获取的信息以字符串返回，而不是直接输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // 连接等待不超过 10 秒
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        $output = curl_exec($ch);
        if (curl_errno($ch)) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = curl_error($ch);
            curl_close($ch);
            return $ret;
        }
        curl_close($ch);

        // API 流水功能上线前，暂时记录到日志文件里面
        if (APP_STATUS != 'production') {
            \Think\Log::write("url {$uri}, response {$output}");
        }

        $ret['data'] = $output;
        return $ret;
    }

    /**
     * 【待删】需要改用 dsSvrApiGetQuery ，切换完毕后删除本接口
     *
     * 开房服务端接口调用
     */
    public function kaifangApiQuery($uri, $gameid = '')
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );
        if (empty($gameid)) {
            $gameid = C("G_USER.gameid");
        }

        $mod = new GameModel();
        $modRet = $mod->queryGameInfoById($gameid);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        if (empty($modRet['data']['api_ip']) || empty($modRet['data']['api_port'])) {
            set_exception(__FILE__, __LINE__, "[kaifangApiQuery] game id {$gameid}");
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = "游戏配置未配置游戏服地址或端口";
            return $ret;
        }
        $iphost = $modRet['data']['api_ip'];
        $port = $modRet['data']['api_port'];

        $ch = curl_init();

        // URL
        curl_setopt($ch, CURLOPT_URL, $iphost.$uri);

        // 端口
        curl_setopt($ch, CURLOPT_PORT, $port);

        // 使用 GET 方法进行 HTTP 请求
        curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "GET");

        // 将 curl_exec() 获取的信息以字符串返回，而不是直接输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // 连接等待不超过 5 秒
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        $output = curl_exec($ch);
        if (curl_errno($ch)) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = curl_error($ch);
            curl_close($ch);
            return $ret;
        }
        curl_close($ch);

        $ret['data'] = $output;
        return $ret;
    }

    /**
     * 【待删】需要改用 dsActivityApiGetQuery ，切换完毕后删除本接口
     *
     * 开房服务端接口调用
     */
    public function commonCleanCacheApi($uri, $type = '', $gameid = '')
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );
        if (empty($gameid)) {
            $gameid = C("G_USER.gameid");
        }

        $gameMod = new GameModel();
        $modRet = $gameMod->queryGameInfoById($gameid);
        if (ERRCODE_SUCCESS !== $modRet['code']) {
            $ret['code'] = $modRet['code'];
            $ret['msg'] = $modRet['msg'];
            return $ret;
        }
        switch ($type) {
            case 'activity':
                if (empty($modRet['data']['activity_api']) || empty($modRet['data']['activity_api_port'])) {
                    set_exception(__FILE__, __LINE__, "[commonCleanCacheApi] game id {$gameid}");
                    $ret['code'] = ERRCODE_API_ERR;
                    $ret['msg'] = "游戏配置未配置游戏服地址或端口";
                    return $ret;
                }
                $iphost = $modRet['data']['activity_api'];
                $port = $modRet['data']['activity_api_port'];
                break;

            default :
                if (empty($modRet['data']['api_ip']) || empty($modRet['data']['api_port'])) {
                    set_exception(__FILE__, __LINE__, "[commonCleanCacheApi] game id {$gameid}");
                    $ret['code'] = ERRCODE_API_ERR;
                    $ret['msg'] = "游戏配置未配置游戏服地址或端口";
                    return $ret;
                }
                $iphost = $modRet['data']['api_ip'];
                $port = $modRet['data']['api_port'];
                break;
        }

        $ch = curl_init();

        // URL
        curl_setopt($ch, CURLOPT_URL, $iphost.$uri);

        // 端口
        curl_setopt($ch, CURLOPT_PORT, $port);

        // 使用 GET 方法进行 HTTP 请求
        curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "GET");

        // 将 curl_exec() 获取的信息以字符串返回，而不是直接输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // 连接等待不超过 5 秒
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        $output = curl_exec($ch);
        if (curl_errno($ch)) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = curl_error($ch);
            curl_close($ch);
            return $ret;
        }
        curl_close($ch);

        $ret['data'] = $output;
        return $ret;
    }

    /**
     * 资源服务器上传图片接口
     * @param $path 要保存到服务器中的路径文件
     * @param $source 临时目录物理路径
     */
    public function resourceServerUploadImg($path, $source)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // svr url
        $url = C('RESOURCE_API_IPHOST')."/action.php";
        $port = C('RESOURCE_API_PORT');

        // post data
        $data = array(
            'act' => 'uploadImg',
            'path' => $path,
            'source' => curl_file_create($source),
        );

        $ch = curl_init();

        // URL
        curl_setopt($ch, CURLOPT_URL, $url);

        // 端口
        curl_setopt($ch, CURLOPT_PORT, $port);

        // 将 curl_exec() 获取的信息以字符串返回，而不是直接输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // 使用 POST 方法进行 HTTP 请求
        curl_setopt($ch, CURLOPT_POST, 1);

        // 传输的参数
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $output = curl_exec($ch);
        if (curl_errno($ch)) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = curl_error($ch);
            curl_close($ch);
            return $ret;
        }
        curl_close($ch);

        $apiRet = json_decode($output, true);
        if (is_null($apiRet)) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = "JSON解码失败：{$output}";
            return $ret;
        }
        if (ERRCODE_SUCCESS !== $apiRet['code']) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = $apiRet['msg'];
            return $ret;
        }
        return $ret;
    }

    /**
     * 资源服务器删除图片接口
     * @param $path 服务器中待删除的路径
     */
    public function resourceServerDeleteImg($path)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // svr url
        $url = C('RESOURCE_API_IPHOST')."/action.php";
        $port = C('RESOURCE_API_PORT');

        // post data
        $data = array('act' => 'deleteImg', 'path' => $path);

        $ch = curl_init();

        // URL
        curl_setopt($ch, CURLOPT_URL, $url);

        // 端口
        curl_setopt($ch, CURLOPT_PORT, $port);

        // 将 curl_exec() 获取的信息以字符串返回，而不是直接输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // 使用 POST 方法进行 HTTP 请求
        curl_setopt($ch, CURLOPT_POST, 1);

        // 传输的参数
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $output = curl_exec($ch);
        if (curl_errno($ch)) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = curl_error($ch);
            curl_close($ch);
            return $ret;
        }
        curl_close($ch);

        $apiRet = json_decode($output, true);
        if (is_null($apiRet)) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = "JSON解码失败：{$output}";
            return $ret;
        }
        if (ERRCODE_SUCCESS !== $apiRet['code']) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = $apiRet['msg'];
            return $ret;
        }
        return $ret;
    }

    /**
     * 资源服删除图片目录接口
     * @author Carter
     */
    public function resourceServerRmImgDir($path)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // svr url
        $url = C('RESOURCE_API_IPHOST')."/action.php";
        $port = C('RESOURCE_API_PORT');

        // post data
        $data = array('act' => 'delImgDir', 'path' => $path);

        $ch = curl_init();

        // URL
        curl_setopt($ch, CURLOPT_URL, $url);

        // 端口
        curl_setopt($ch, CURLOPT_PORT, $port);

        // 将 curl_exec() 获取的信息以字符串返回，而不是直接输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // 使用 POST 方法进行 HTTP 请求
        curl_setopt($ch, CURLOPT_POST, 1);

        // 传输的参数
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $output = curl_exec($ch);
        if (curl_errno($ch)) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = curl_error($ch);
            curl_close($ch);
            return $ret;
        }
        curl_close($ch);

        $apiRet = json_decode($output, true);
        if (is_null($apiRet)) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = "JSON解码失败：{$output}";
            return $ret;
        }
        if (ERRCODE_SUCCESS !== $apiRet['code']) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = $apiRet['msg'];
            return $ret;
        }
        return $ret;
    }

    /**
     * 资源服上传 zip 压缩文件并解压接口
     * @author Carter
     */
    public function resourceServerUpZip($path, $source)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // svr url
        $url = C('RESOURCE_API_IPHOST')."/action.php";
        $port = C('RESOURCE_API_PORT');

        // post data
        $data = array(
            'act' => 'uploadZip',
            'path' => $path,
            'source' => curl_file_create($source),
        );

        $ch = curl_init();

        // URL
        curl_setopt($ch, CURLOPT_URL, $url);

        // 端口
        curl_setopt($ch, CURLOPT_PORT, $port);

        // 将 curl_exec() 获取的信息以字符串返回，而不是直接输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // 使用 POST 方法进行 HTTP 请求
        curl_setopt($ch, CURLOPT_POST, 1);

        // 传输的参数
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $output = curl_exec($ch);
        if (curl_errno($ch)) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = curl_error($ch);
            curl_close($ch);
            return $ret;
        }
        curl_close($ch);

        $apiRet = json_decode($output, true);
        if (is_null($apiRet)) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = "JSON解码失败：{$output}";
            return $ret;
        }
        if (ERRCODE_SUCCESS !== $apiRet['code']) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = $apiRet['msg'];
            return $ret;
        }
        return $ret;
    }

    /**
     * 分享后台发送配置文件
     * @author carter
     */
    public function shareServerSendConfFile($confFile)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $iphost = C('SHARE_SERVER_IPHOST');
        $port = C('SHARE_SERVER_PORT');

        // post data
        $data = array(
            'source' => curl_file_create($confFile),
        );

        $ch = curl_init();

        // URL
        curl_setopt($ch, CURLOPT_URL, $iphost.'/Api/sendConfFile');

        // 端口
        curl_setopt($ch, CURLOPT_PORT, $port);

        // 将 curl_exec() 获取的信息以字符串返回，而不是直接输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // 使用 POST 方法进行 HTTP 请求
        curl_setopt($ch, CURLOPT_POST, 1);

        // 传输的参数
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        // 连接等待不超过 5 秒
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        $output = curl_exec($ch);
        if (curl_errno($ch)) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = curl_error($ch);
            curl_close($ch);
            return $ret;
        }
        curl_close($ch);

        $apiRet = json_decode($output, true);
        if (is_null($apiRet)) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = "JSON解码失败：{$output}";
            return $ret;
        }
        if (ERRCODE_SUCCESS !== $apiRet['code']) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = $apiRet['msg'];
            return $ret;
        }
        $ret['data'] = $apiRet['data'];
        return $ret;
    }

    /**
     * 上传白名单文件
     * @author liyao
     */
    public function whitelistSendConfFile($path, $source)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // svr url
        $whitelisthost = C('WHITELIST_SERVER_IPHOST');
        $whitelistport = C('WHITELIST_SERVER_PORT');
        $url = $whitelisthost."/action.php";
        $port = $whitelistport;

        // post data
        $data = array(
            'act' => 'uploadWriteList',
            'path' => $path,
            'source' => curl_file_create($source),
        );

        $ch = curl_init();

        // URL
        curl_setopt($ch, CURLOPT_URL, $url);

        // 端口
        curl_setopt($ch, CURLOPT_PORT, $port);

        // 将 curl_exec() 获取的信息以字符串返回，而不是直接输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // 使用 POST 方法进行 HTTP 请求
        curl_setopt($ch, CURLOPT_POST, 1);

        // 传输的参数
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $output = curl_exec($ch);

        if (curl_errno($ch)) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = curl_error($ch);
            curl_close($ch);
            return $ret;
        }
        curl_close($ch);

        $apiRet = json_decode($output, true);
        if (is_null($apiRet)) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = "JSON解码失败：{$output}";
            return $ret;
        }
        if (ERRCODE_SUCCESS !== $apiRet['code']) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = $apiRet['msg'];
            return $ret;
        }
        return $ret;
    }

    /**
     * 上传落地页配置
     * @author liyao
     */
    public function landpageSendConfFile($path, $source)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // svr url
        $host = C('LANDPAGE_SERVER_IPHOST');
        $port = C('LANDPAGE_SERVER_PORT');
        $url = $host."/action.php";

        // post data
        $data = array(
            'act' => 'uploadLandpage',
            'path' => $path,
            'source' => curl_file_create($source),
        );

        $ch = curl_init();

        // URL
        curl_setopt($ch, CURLOPT_URL, $url);

        // 端口
        curl_setopt($ch, CURLOPT_PORT, $port);

        // 将 curl_exec() 获取的信息以字符串返回，而不是直接输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // 使用 POST 方法进行 HTTP 请求
        curl_setopt($ch, CURLOPT_POST, 1);

        // 传输的参数
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $output = curl_exec($ch);

        if (curl_errno($ch)) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = curl_error($ch);
            curl_close($ch);
            return $ret;
        }
        curl_close($ch);

        $apiRet = json_decode($output, true);
        if (is_null($apiRet)) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = "JSON解码失败：{$output}";
            return $ret;
        }
        if (ERRCODE_SUCCESS !== $apiRet['code']) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = $apiRet['msg'];
            return $ret;
        }
        return $ret;
    }

    /**
     * 上传活动配置
     * @author liyao
     */
    public function actSendConfFile($host, $port, $uri, $data)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // svr url
        $key = C('ACT_API_CONFKEY');
        $url = $host. ':'. $port. $uri;

        $postData['data'] = json_encode($data);
        $postData['sign'] = md5(md5($postData['data']). $key);

        $ch = curl_init();
        // URL
        curl_setopt($ch, CURLOPT_URL, $url);

        // 端口
        curl_setopt($ch, CURLOPT_PORT, $port);

        // 将 curl_exec() 获取的信息以字符串返回，而不是直接输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // 使用 POST 方法进行 HTTP 请求
        curl_setopt($ch, CURLOPT_POST, 1);

        // 传输的参数
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $output = curl_exec($ch);

        if (curl_errno($ch)) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = curl_error($ch);
            curl_close($ch);
            return $ret;
        }

        curl_close($ch);
        $apiRet = json_decode($output, true);
        if (is_null($apiRet)) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = "配置上传失败";
            return $ret;
        }

        if (ERRCODE_SUCCESS !== $apiRet['status']) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = $apiRet['message'];
            return $ret;
        }

        $ret['data'] = $apiRet['data'];
        return $ret;
    }

    public function actGetAnalysis($uri, $data)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // svr url
        $host = C('ACT_API_CONFURL');
        $port = C('ACT_API_CONFPORT');
        $key = C('ACT_API_CONFKEY');
        $url = $host. ':'. $port. $uri;

        $postData['product_id'] = $data['product_id'];
        $postData['act_id'] = $data['act_id'];
        if (empty($data['user_id'])) {
            unset($data['user_id']);
        } else {
            $postData['user_id'] = $data['user_id'];
        }

        $postData['data'] = json_encode($data);
        $postData['sign'] = md5(md5($postData['data']). $key);

        $ch = curl_init();
        // URL
        curl_setopt($ch, CURLOPT_URL, $url);

        // 端口
        curl_setopt($ch, CURLOPT_PORT, $port);

        // 将 curl_exec() 获取的信息以字符串返回，而不是直接输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // 使用 POST 方法进行 HTTP 请求
        curl_setopt($ch, CURLOPT_POST, 1);

        // 传输的参数
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $output = curl_exec($ch);
        if (curl_errno($ch)) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = curl_error($ch);
            curl_close($ch);
            return $ret;
        }

        curl_close($ch);
        $apiRet = json_decode($output, true);
        if (is_null($apiRet)) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = "获取数据失败";
            return $ret;
        }

        if (ERRCODE_SUCCESS !== $apiRet['status']) {
            $ret['code'] = ERRCODE_API_ERR;
            $ret['msg'] = $apiRet['message'];
            return $ret;
        }

        $ret['data'] = $apiRet['data'];
        return $ret;
    }
}
