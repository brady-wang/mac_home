<?php
namespace Home\Model;

use Think\Model;

class SysApiLogModel extends Model
{
    // 接口类型
    const TYPE_ADMIN = 1; // 后台接口
    const TYPE_ACTIVITY_SVR = 7; // 活动服
    const TYPE_GAME_SVR = 9; // 游戏服

    public $typeMap = array(
        self::TYPE_ADMIN => '后台接口',
        self::TYPE_ACTIVITY_SVR => '活动服',
        self::TYPE_GAME_SVR => '游戏服',
    );

    // Api Code
    // 游戏服：9000 - 9999
    const API_CODE_GAME_COMMON = 9000; // 游戏服通用接口
    const API_CODE_GAME_ROOM_INFO = 9001; // 游戏服-房间信息接口

    public $typeUrlCodeMap = array(
        // 后台接口
        'admin' => array(

        ),

        // 游戏服
        'game' => array(
            '/console/' => array(
                'type' => self::TYPE_GAME_SVR,
                'code' => self::API_CODE_GAME_COMMON,
                'name' => '游戏服通用接口',
            ),
            '/api/room/' => array(
                'type' => self::TYPE_GAME_SVR,
                'code' => self::API_CODE_GAME_ROOM_INFO,
                'name' => '房间信息接口',
            ),

        ),

    );

    /**
     * 插入接口流水
     * @author Carter
     */
    public function insertSysApiLog($type, $apiCode, $url, $request, $response)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $data = array(
            'type' => $type,
            'api_code' => $apiCode,
            'url' => $url,
            'request' => serialize($request),
            'response' => serialize($response),
            'create_time' => date('Y-m-d H:i:s'),
        );
        try {
            $this->add($data);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertSysApiLog] insert failed, ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_ADD_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        return $ret;
    }

    /**
     * 根据参数获取流水列表
     * @author Carter
     */
    public function querySysApiLogByAttr($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array();
        if (!empty($attr['api_type'])) {
            $where['type'] = $attr['api_type'];
        }
        if (!empty($attr['api_code'])) {
            $where['api_code'] = $attr['api_code'];
        }
        if (!empty($attr['request_key'])) {
            $where['_string'] = "MATCH (request) AGAINST ('".$attr['request_key']."' IN BOOLEAN MODE)";
        }
        if (!empty($attr['response_key'])) {
            if (is_null($where['_string'])) {
                $where['_string'] = '';
            } else {
                $where['_string'] .= ' AND ';
            }
            $where['_string'] .= "MATCH (response) AGAINST ('".$attr['response_key']."' IN BOOLEAN MODE)";
        }
        if (!empty($attr['start_time'])) {
            $where['create_time'][] = array('egt', $attr['start_time']);
        }
        if (!empty($attr['end_time'])) {
            $where['create_time'][] = array('elt', $attr['end_time']);
        }
        if ($attr['start_date']) {
            // 确保时间是从指定日期的0点0分0秒开始
            $where['create_time'][] = array('egt', date('Y-m-d', strtotime($attr['start_date'])));
        }
        if ($attr['end_date']) {
            // 确保时间是从指定日期的23点59分59秒结束
            $where['create_time'][] = array('elt', date('Y-m-d 23:59:59', strtotime($attr['end_date'])));
        }

        // 分页获取
        $pageSize = C('PAGE_SIZE');
        $count = $this->where($where)->count();
        $paginate = new \Think\Page($count, $pageSize);
        $pagination = $paginate->show();

        $page = $paginate->getCurPage();
        try {
            $logList = $this->where($where)->order('id DESC')->page("{$page},{$pageSize}")->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[querySysApiLogByAttr] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        // var_export 后数组键值之间会换行，取消换行；缩进是两个空格，改为四个空格
        $patterns = array("/=> \n */", "/  /");
        $replaces = array("=> ", "    ");

        foreach ($logList as $k => $v) {
            // request
            $logList[$k]['request'] = preg_replace($patterns, $replaces, var_export(unserialize($v['request']), true));

            // response
            $resp = unserialize($v['response']);
            if (is_array($resp)) {
                $logList[$k]['response'] = preg_replace($patterns, $replaces, var_export($resp, true));
            } else {
                $logList[$k]['response'] = "api occur fatal error";
            }
        }

        $ret['data'] = array(
            'list' => $logList,
            'pagination' => $pagination,
        );
        return $ret;
    }

    /**
     * 根据时间删除日志流水
     * @author Carter
     */
    public function deleteSysApiLogByTime($time)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );


        try {
            $count = $this->where(['create_time' => ['lt', $time]])->delete();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[deleteSysApiLogByTime] delete failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_DELETE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $count;

        return $ret;
    }
}
