<?php
namespace Home\Model;
use Common\Service\ApiService;
use Think\Model;

class GameMailModel extends Model
{
    // 邮件过滤条件
    const MAIL_HAS_NOT_SEND = 0;     // 未发送
    const MAIL_HAS_SEND = 1;         // 已发送
    const MAIL_APPOINT_USER = 0;     // 指定玩家
    const MAIL_ALL_USER = 1;         // 全服玩家
    const MAIL_CHANNEL_USER = 2;     // 渠道玩家
    const MAIL_BATCH_USER = 3;       // 批量玩家
    const MAIL_STATUS_UNAUDITED = 0; // 未审核
    const MAIL_STATUS_PASS = 1;      // 审核通过,待发送
    const MAIL_STATUS_UNPASS = 2;    // 审核未通过
    const MAIL_STATUS_CANCEL = 3;    // 取消发送

    /**
     * 获取符合条件的全部数据
     * @author liyao
     */
    public function queryAllMailData($where)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        try {
            $list = $this->where($where)->order('id DESC')->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryAllMailData] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data']['list'] = $list;
        return $ret;
    }
    
    /**
     * 获取指定id的邮件信息
     * @author liyao
     */
    public function queryMailDataById($id)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        try {
            $list = $this->where(array("id" => $id))->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryMailDataById] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $list;
        return $ret;
    }    
    
    /**
     * 获取符合条件的分页数据
     * @author liyao
     */
    public function queryPageMailData($where, $field = "*")
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        // 分页获取
        try {
            $pageSize = C('PAGE_SIZE');
            $count = $this->where($where)->count();
            $paginate = new \Think\Page($count, $pageSize);
            $pagination = $paginate->show();

            $page = $paginate->getCurPage();
            $list = $this->field($field)->where($where)->join('__SYS_USER__ ON __GAME_MAIL__.operator_id = __SYS_USER__.uid')->order('id DESC')->page("{$page},{$pageSize}")->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryPageMailData] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data']['list'] = $list;
        $ret['data']['pagination'] = $pagination;
        return $ret;
    }           
 
    /**
     * 修改邮件信息为发送状态
     * @author liyao
     */
    public function updateMailSender($id)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        try {
            $this->where(array("id" => $id))->save(array('send_flag' => 1));
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateMailSender] update failed ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
        }
        return $ret;
    }
    
    /**
     * 更新邮件状态
     * @author liyao
     */
    public function updateMailStatus($id, $status)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        try {
            $this->where(array("id" => $id))->save(array('mail_status' => $status));
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateMailStatus] update failed ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
        }
        return $ret;
    }
    
    /**
     * 删除定时邮件队列中指定id的邮件
     * @author liyao
     */
    public function deleteMailTimer($id)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        try {
            $this->where(array("id" => $id, "send_flag" => 0))->delete();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[deleteMailTimer] delete failed ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
        }
        return $ret;
    }
    
    /**
     * 添加邮件消息发送
     * @author liyao
     */
    public function addMailTimer($data)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        try {
            $userids = array();
            if ($data['user_type'] == 0) {
                if (!empty($data['users'])) {
                    $users = str_replace(array("\r\n", "\r", "\n"), "", $data['users']);
                    $tu = explode(",", $users);
                    foreach ($tu as $v) {
                        if (!empty($v) && !in_array($v, $userids)) {
                            $userids[] = $v;
                        }
                    }
                }
                $struserids = implode(',', $userids);
            } else if ($data['user_type'] == 3) {
                $name = str_replace("/FileUpload/BatchUser/",'', $data['users']);
                $path = ROOT_PATH.'FileUpload/BatchUser/'.$name; 
                if (file_exists(($path))) {
                    $skipOne = true;
                    $fp = @fopen($path, "r");
                    if ($fp) {
                        while (!feof($fp)) {
                            $buf = fgets($fp);
                            if ($skipOne) {
                                $skipOne = false;
                                continue;
                            }
                            if (empty(trim($buf))) {
                                continue;
                            }
                            $arr = explode(",", $buf);
                            $uid = trim($arr[0]);
                            $val = intval($arr[1]);
                            $userids[] = array("uid" => $uid, "num" => $val);;
                        }
                        fclose($fp);
                    }
                }   
                $struserids = json_encode($userids);
            }
            $tmnow = time();
            $adddata = array(
                'subj' => html_entity_decode($data['subj']),
                'cont' => html_entity_decode($data['cont']),
                'users' => $struserids,
                'pay' => (!empty($data['reward'])?$data['reward']:''),
                'ctime' => $tmnow,
                'stime' => (!empty($data['starttime'])?strtotime($data['starttime']):$tmnow),
                'etime' => strtotime($data['endtime']),
                'operator_id' => C('G_USER.uid'),
                'user_type' => $data['user_type'],
                'timer_flag' => (empty($data['starttime']) ? 0:1),
		'channel_code' => intval($data['code']),
                'mail_status' => 0,
                'game_id' => C('G_USER.gameid')
            );
            $this->data($adddata)->add();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[addMailTimer] add failed ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
        }
        //if (empty($data['starttime'])) {
        //    $rer = $this->sendMail($adddata);
        //}
        return $ret;
    }
    /**
     * 将发送数据格式化为接口格式
     */
    public function sendMail($data, $gameid='') {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        $apiSer = new ApiService();
        if ($data['user_type'] == 3) {
            $arrUser = json_decode($data['users']);
            $sepUser = array();
            foreach ($arrUser as $v) {
                $v = get_object_vars($v);
                if (!isset($sepUser[$v['num']])) {
                    $sepUser[$v['num']] = array();
                }
                $sepUser[$v['num']][] = $v['uid'];
            }
            foreach ($sepUser as $k=>$v) {  
                $userseps = array_chunk($v, 500);
                foreach ($userseps as $vv) {
                    $reward = '10008:'.$k;
                    $userids = implode(',', $vv);
                    $url = $this->_makeMailUrl($data, $reward, $userids);
                    // 调用服务端接口，立即发送邮件
                    $serRet = $apiSer->kaifangApiQuery($url, $gameid);
                    if (ERRCODE_SUCCESS !== $serRet['code']) {
                        $ret['code'] = $serRet['code'];
                        $ret['msg'] = $serRet['msg'];
                        return $ret;
                    }
                }
            }
        } else {
            $url = $this->_makeMailUrl($data, $data['pay'], $data['users']);
            // 调用服务端接口，立即发送邮件
            $serRet = $apiSer->kaifangApiQuery($url, $gameid);
            if (ERRCODE_SUCCESS !== $serRet['code']) {
                $ret['code'] = $serRet['code'];
                $ret['msg'] = $serRet['msg'];
                return $ret;
            }
        }
        //刷新缓存
        $serRet = $apiSer->kaifangApiQuery('/console/?act=reload', $gameid);
        if (ERRCODE_SUCCESS !== $serRet['code']) {
            $ret['code'] = $serRet['code'];
            $ret['msg'] = $serRet['msg'];
            return $ret;
        }
        return $ret;
    }
    
    /**
     * 生成发送邮件链接
     */
    private function _makeMailUrl($data, $reward, $userids) {
        $arr = array();
        if ($data['user_type'] == 1) {
            $arr[] = 'act=insertsysmail';
	} else if ($data['user_type'] == 2) {
            $arr[] = 'act=insertsysmail';
	    $arr[] = 'filterValue='.urlencode('{"idC":"'.$data['channel_code'].'"}');
        } else {
            $arr[] = 'act=newusermails';
            $arr[] = 'userIds='.urlencode($userids);
        }
        $arr[] = 'title='.urlencode($data['subj']);
        $arr[] = 'content='.urlencode($data['cont']);
        $arr[] = 'reward='.urlencode($reward);
        if ($data['user_type'] == 1 || $data['user_type'] == 2) {
            $arr[] = 'startTime='.urlencode(date("Y-m-d H:i:s", (10+time())));
            $arr[] = 'endRegisterTime='.urlencode(date("Y-m-d H:i:s", $data['stime']));
        } else {
            $arr[] = 'startTime='.urlencode(date("Y-m-d H:i:s", $data['stime']));
        }
        $arr[] = 'startRegisterTime='.urlencode("1970-1-1 0:0:0");
        $arr[] = 'endTime='.urlencode(date("Y-m-d H:i:s", $data['etime']));
        $url = '/api/mail/?'.implode("&", $arr);
        return $url;
    }
}
