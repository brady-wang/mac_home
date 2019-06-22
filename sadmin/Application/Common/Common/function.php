<?php

/**
 * 输出变量调试函数
 * @author Carter
 */
function dd() {
    header("Content-Type: text/html; charset=UTF-8");
    $arr = func_get_args();
    $trace = debug_backtrace();
    echo "<pre>";
    echo "{$trace[0]['file']}: {$trace[0]['line']}\n\n";
    var_dump($arr);
    echo "</pre>";
    die;
}

function p()
{
    header("Content-Type: text/html; charset=UTF-8");
    $argc = func_get_args();
    foreach ($argc as $val) {
        echo '<pre>';
        print_r($val);
    }
    exit;
}

/**
 * 可防止时序攻击的字符串比较
 * 相同返回 true，否则返回 false
 * @author Carter
 */
function equals($knownString, $userInput) {
    $knownString = (string) $knownString;
    $userInput = (string) $userInput;

    if (function_exists('hash_equals')) {
        return hash_equals($knownString, $userInput);
    }

    $knownLen = strlen($knownString);
    $userLen = strlen($userInput);

    // Extend the known string to avoid uninitialized string offsets
    $knownString .= $userInput;

    // Set the result to the difference between the lengths
    $result = $knownLen - $userLen;

    // Note that we ALWAYS iterate over the user-supplied length
    // This is to mitigate leaking length information
    for ($i = 0; $i < $userLen; $i++) {
        $result |= (ord($knownString[$i]) ^ ord($userInput[$i]));
    }

    // They are only identical strings if $result is exactly 0...
    return 0 === $result;
}

/**
 * 系统加密接口
 * @author Carter
 */
function think_encrypt($value) {
    // 加密算法与模式
    $cipher = MCRYPT_RIJNDAEL_128;
    $mode = MCRYPT_MODE_CBC;

    // 密钥，必须是 32 位随机字符串
    $encryptionKey = C('THINK_ENCRYPT_KEY');

    // 块位数
    $block = 16;

    // 初始向量大小
    $ivSize = mcrypt_get_iv_size($cipher, $mode);

    // 建初始向量
    $iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);

    // 输入序列化
    $value = serialize($value);

    // 对输入值做 PKCS#7 填充，填充后的 $paddingValue 长度会是 $block 的整数倍
    $pad = $block - (strlen($value) % $block);
    $paddingValue = $value . str_repeat(chr($pad), $pad);

    // 加密明文
    $encryptValue = mcrypt_encrypt($cipher, $encryptionKey, $paddingValue, $mode, $iv);

    // base64 编码
    $base64Iv = base64_encode($iv);
    $base64Value = base64_encode($encryptValue);

    // 使用 HMAC 方法生成带有密钥的哈希值
    $data = $base64Iv . $base64Value;
    $mac = hash_hmac('sha256', $data, $encryptionKey);

    // 将 iv, value, mac 打包进行 json 编码，base64 编码
    return base64_encode(json_encode(compact('base64Iv', 'base64Value', 'mac')));
}

/**
 * 系统解密接口
 * @author Carter
 */
function think_decrypt($payload) {
    // 解密算法与模式
    $cipher = MCRYPT_RIJNDAEL_128;
    $mode = MCRYPT_MODE_CBC;

    // 密钥，必须是 32 位随机字符串
    $encryptionKey = C('THINK_ENCRYPT_KEY');

    // 初始解码
    $payload = json_decode(base64_decode($payload), true);
    if (!$payload ||
            !is_array($payload) ||
            !isset($payload['base64Iv']) ||
            !isset($payload['base64Value']) ||
            !isset($payload['mac'])
    ) {
        return false;
    }

    // 生成一段伪随机字节
    $bytes = openssl_random_pseudo_bytes(16, $strong);
    if (false === $bytes || true !== $strong) {
        return false;
    }

    // 根据 iv 和 value 计算 mac
    $data = hash_hmac('sha256', $payload['base64Iv'] . $payload['base64Value'], $encryptionKey);
    $calcMac = hash_hmac('sha256', $data, $bytes, true);

    // 加载数据的 mac
    $mac = hash_hmac('sha256', $payload['mac'], $bytes, true);

    // 判断 MAC 是否一致
    if (!equals($mac, $calcMac)) {
        return false;
    }

    // base64 解码
    $value = base64_decode($payload['base64Value']);
    $iv = base64_decode($payload['base64Iv']);

    // 解密密文
    $decryptValue = mcrypt_decrypt($cipher, $encryptionKey, $value, $mode, $iv);

    // 去除 PKCS#7 填充数据
    $pad = ord($decryptValue[($len = strlen($decryptValue)) - 1]);
    $beforePad = strlen($decryptValue) - $pad;
    if (substr($decryptValue, $beforePad) == str_repeat(substr($decryptValue, -1), $pad)) {
        $seriValue = substr($decryptValue, 0, $len - $pad);
    } else {
        $seriValue = $decryptValue;
    }

    // 反序列化
    return unserialize($seriValue);
}

/**
 * api 加密接口，轻量级加解密算法
 * @author Carter
 */
function api_encrypt($string) {
    $encryptKey = md5("uis&82");
    $keyLen = strlen($encryptKey);

    $data = substr(md5($string . $encryptKey), 0, 8) . $string;
    $dataLen = strlen($data);

    $rndkey = array();
    $box = array();
    $cipherText = "";

    for ($i = 0; $i < 256; $i++) {
        $rndkey[$i] = ord($encryptKey[$i % $keyLen]);
        $box[$i] = $i;
    }

    for ($i = 0, $j = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for ($i = 0, $j = 0, $k = 0; $i < $dataLen; $i++) {
        $k = ($k + 1) % 256;
        $j = ($j + $box[$k]) % 256;
        $tmp = $box[$k];
        $box[$k] = $box[$j];
        $box[$j] = $tmp;
        $cipherText .= chr(ord($data[$i]) ^ ($box[($box[$k] + $box[$j]) % 256]));
    }

    return str_replace('=', '', base64_encode($cipherText));
}

/**
 * api 解密接口，轻量级解密算法
 * @author Carter
 */
function api_decrypt($cipherText) {
    $encryptKey = md5("uis&82");
    $keyLen = strlen($encryptKey);

    $cipherText = base64_decode($cipherText);
    $textLen = strlen($cipherText);

    $rndkey = array();
    $box = array();
    $decryptText = "";

    for ($i = 0; $i < 256; $i++) {
        $rndkey[$i] = ord($encryptKey[$i % $keyLen]);
        $box[$i] = $i;
    }

    for ($i = 0, $j = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for ($i = 0, $j = 0, $k = 0; $i < $textLen; $i++) {
        $k = ($k + 1) % 256;
        $j = ($j + $box[$k]) % 256;
        $tmp = $box[$k];
        $box[$k] = $box[$j];
        $box[$j] = $tmp;
        $decryptText .= chr(ord($cipherText[$i]) ^ ($box[($box[$k] + $box[$j]) % 256]));
    }

    if (substr($decryptText, 0, 8) == substr(md5(substr($decryptText, 8) . $encryptKey), 0, 8)) {
        return substr($decryptText, 8);
    } else {
        return false;
    }
}

/**
 * 获取游戏地区 map
 * @author Carter
 */
function get_region_map($roleId = 0) {

    try {
        // 要封装成 model，不允许跨越出来用join TODO
        if ($roleId > 0) {
            $where = array(
                'a.role_id' => $roleId,
                'g.game_status' => 1,
            );
            $data = M()
                    ->field('g.game_id,g.game_name')
                    ->table(C('DB_PREFIX') . 'sys_game_auth as a INNER JOIN ' . C('DB_PREFIX') . 'game as g ON a.game_id = g.game_id')
                    ->where($where)
                    ->order('g.id ASC')
                    ->select();
        } else {
            $data = M("Game")->field('game_id,game_name')->where(array("game_status" => 1))->order("id ASC")->select();
        }
    } catch (\Exception $e) {
        set_exception(__FILE__, __LINE__, "[get_region_map] select failed: " . $e->getMessage());
        return array();
    }
    $game = array();
    foreach ($data as $v) {
        $game[$v["game_id"]] = $v["game_name"];
    }
    return $game;
}

/**
 * 获取游戏端指定游戏的子玩法 map
 * @author Carter
 */
function get_game_play_map($gameId) {

    $confSer = new Common\Service\DbLoadConfigService();
    if (true === $confSer->load($gameId, 'GAME_DICT_DB', 0)) {
        try {
            $placeMod = new Home\Model\DsqpDict\DictPlaceModel();
            $gameMod = new Home\Model\DsqpDict\DictPlaceGameModel();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[get_game_play_map] ".$e->getMessage());
            return array();
        }
    } else {
        set_exception(__FILE__, __LINE__, "[get_game_play_map] {$gameId} load GAME_DICT_DB failed");
        return array();
    }

    $modRet = $placeMod->queryDsqpPlaceListByFirstId($gameId, 'placeID');
    if (ERRCODE_SUCCESS !== $modRet['code']) {
        return array();
    }
    $placeArr = array_column($modRet['data'], 'placeID');

    $modRet = $gameMod->queryDsqpPlaceGameByPlaceId($placeArr, 'gameId,gameName');
    if (ERRCODE_SUCCESS !== $modRet['code']) {
        return array();
    }

    return array_combine(array_column($modRet['data'], 'gameId'), array_column($modRet['data'], 'gameName'));
}

/**
 * 获取上一级页面
 * @author Carter
 */
function get_referer() {
    return urldecode(htmlspecialchars_decode(str_replace('+', '', I('server.HTTP_REFERER'))));
}

/**
 * 记录系统错误信息
 * @author Carter
 */
function set_exception($file, $line, $msg) {
    $logMod = new Home\Model\SysErrlogModel();

    $exceptionFile = substr_replace($file, '', 0, strlen(APP_PATH) - 12);

    $logMod->insertSysException($exceptionFile, $line, $msg);

    return true;
}

/**
 * 记录系统操作，系统的所有操作行为都要有所记录，只记录操作成功的
 * @author Carter
 */
function set_operation($msg, $data = array(), $operCode = 0) {

    $logMod = new Home\Model\SysOperlogModel();

    $cont = serialize(array('msg' => $msg, 'data' => $data));

    $uid = C('G_USER.uid');
    $gameId = C('G_USER.gameid');
    $main = C('G_ACCESS_MAIN');
    $sublevel = C('G_ACCESS_SUBLEVEL');
    $third = C('G_ACCESS_THIRD');

    $logMod->insertSysOperateLog($uid, $gameId, $main, $sublevel, $third, $operCode, $cont);

    return true;
}

/**
 * csv数据处理
 * @author liyao
 */
function CsvVal($str) {
    return '"'.str_replace("\"", "\"\"", $str).'"';
}

/**
 * 导出文件
 * @author liyao
 */
function export_file($filename, $title, $list) {

    header('Content-Disposition: attachment; filename="'.trim($filename).'"');
    header('Content-Type: application/octet-stream');

    $crlf = "\r\n";
    $arr = array();
    foreach ($title as $v) {
        $arr[] = $v;
    }
    echo implode(',', $arr).$crlf;
    foreach ($list as $vv) {
        $arr = array();
        foreach ($title as $k => $v) {
            $arr[] = isset($vv[$k]) ? $vv[$k] : 0;
        }
        echo implode(',', $arr).$crlf;
    }
    exit;
}

/**
 * 检测网络文件是否存在
 */
function check_url_file_exist($url) {
    $fp = @fopen($url, 'r');
    if ($fp !== false) {
        fclose($fp);
        return 1;
    }
    return 0;
}

/**
 * 将百分位数据格式化为页面输出格式，例如金钱等
 * @author Carter
 */
function format_li($value) {
    return floatval(number_format($value / 100, 2, '.', ''));
}

/**
 * 将千分位数据格式化为页面输出格式，例如重量、税率等
 * @author Carter
 */
function format_milli($value) {
    return floatval(number_format($value / 1000, 3, '.', ''));
}

/**
 * 将秒数时间转化为时分秒格式的字符串
 * @author Carter
 */
function format_second_time($second) {
    $hour = intval($second / 3600);
    $min = intval(($second - $hour * 3600) / 60);
    $second = $second % 60;
    if ($hour > 0) {
        $timeStr = "{$hour}小时{$min}分{$second}秒";
    } else if ($min > 0) {
        $timeStr = "{$min}分{$second}秒";
    } else {
        $timeStr = "{$second}秒";
    }
    return $timeStr;
}

/**
 * 验证网址
 * @author tangjie
 */
function is_url($str) {
    return preg_match("/^(http|https):\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"])*$/", $str);
}

/**
 * 验证网址访问性
 * @author Carter
 */
function is_url_valid($url) {

    // 有效状态
    $validCode = array(
        '200', // 请求已成功
    );

    $info = get_headers($url);
    if (false === $info) {
        return false;
    }

    list($a, $b, $c) = explode(' ', $info[0]);
    if (empty($b) || !in_array($b, $validCode)) {
        return false;
    }

    return true;
}

/**
 * 兼容功能开关，返回 true 功能可用，false 功能不可用
 * @author Carter
 */
function is_function_enable($eKey) {

    $edtMod = new \Home\Model\SysEditionModel();

    $gameId = C('G_USER.gameid');

    // 默认兼容功能不开通，以防备后台配置被人误删的情况
    $modRet = $edtMod->querySysEditionByKey($eKey);
    if (ERRCODE_SUCCESS !== $modRet['code']) {
        return false;
    }
    if (empty($modRet['data'])) {
        return false;
    }

    $gameList = unserialize($modRet['data']['game']);
    if (!in_array($gameId, $gameList)) {
        return false;
    }

    return true;
}

/**
 * 记录 API 流水
 * @author Carter
 */
function set_apilog($key, $url, $request, $response) {

    $logMod = new Home\Model\SysApiLogModel();

    $urlMap = $logMod->typeUrlCodeMap;

    // 接口地址不存在
    if (is_null($urlMap[$key][$url])) {
        set_exception(__FILE__, __LINE__, "{$url} 地址不存在");
        return false;
    }
    $type = $urlMap[$key][$url]['type'];
    $apiCode = $urlMap[$key][$url]['code'];

    $modRet = $logMod->insertSysApiLog($type, $apiCode, $url, $request, $response);
    if (ERRCODE_SUCCESS !== $modRet['code']) {
        return false;
    }

    return true;
}


/**
 * 验证字符编码
 */
function is_utf8($str) {
    $x = $str;
    $y = @iconv("utf-8", "GBK", $x);
    $z = @iconv("GBK", "utf-8", $y);
    if ($x == $z) {
        $len = strlen($y);
        for ($i = 0; $i < $len; $i++) {
            if (ord($y{$i}) >= 0x80) {
                $i++;
                if ($i < $len) {
                    if (ord($y{$i}) <= 0x80) {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        }
        return true;
    }
    return false;
}

/**
 * 标准化方法返回格式
 * @param $code int
 * @param $data array
 * @param $msg string
 * @param $isJson bool 是否为json
 * @return mixed
 * @author daniel
 */
function return_format($code = ERRCODE_SUCCESS, $data = [], $msg = '', $isJson = false) {
    $ret = [
        'code' => $code,
        'data' => $data,
        'msg'  => $msg
    ];

    return $isJson ? json_encode($ret) : $ret;
}
