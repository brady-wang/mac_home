<?php
namespace Home\Model;

use Think\Model;

class StatUserDailyModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据id获取每日简报统计数据
     * @author Carter
     */
    public function queryStatDailyListById($id, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        try {
            $info = $this->field($field)->where(array('id' => $id))->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryStatDailyListById] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $info;

        return $ret;
    }

    /**
     * 取得数据汇总数据
     * @author liyao
     */
    public function queryStatDailyGroupByAttr($attr, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $where = array();
        if (isset($attr['gameIds'])) {
            $where['game_id'] = array('in', $attr['gameIds']);
        }
        if (isset($attr['sDataTime'])) {
            $where['data_time'][] = array('egt', $attr['sDataTime']);
        }
        if (isset($attr['eDataTime'])) {
            $where['data_time'][] = array('elt', $attr['eDataTime']);
        }

        try {
            $list = $this->field($field)->where($where)->group('data_time')->order("data_time desc")->select();
            $count = array();
            $count["sum_add_user"] = $this->where($where)->sum("add_user");
            $count["sum_login_user"] = $this->where($where)->sum("login_user");
            $count["sum_active_user"] = $this->where($where)->sum("active_user");
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryStatDailyGroupByAttr] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data']['list'] = $list;
        $ret['data']['count'] = $count;

        return $ret;
    }

    /**
     * 根据条件获取每日简报统计数据
     * @author Carter
     */
    public function queryStatDailyListByAttr($attr, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $where = array();
        if (isset($attr['gameId'])) {
            $where['game_id'] = $attr['gameId'];
        }
        if (isset($attr['sDataTime'])) {
            $where['data_time'][] = array('egt', $attr['sDataTime']);
        }
        if (isset($attr['eDataTime'])) {
            $where['data_time'][] = array('elt', $attr['eDataTime']);
        }

        try {
            $list = $this->field($field)->where($where)->order("data_time desc")->select();
            $count = array();
            $count["sum_add_user"] = $this->where($where)->sum("add_user");
            $count["sum_login_user"] = $this->where($where)->sum("login_user");
            $count["sum_active_user"] = $this->where($where)->sum("active_user");
            $count["sum_consume_prop"] = $this->where($where)->sum("consume_prop");
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryStatDailyListByAttr] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data']['list'] = $list;
        $ret['data']['count'] = $count;

        return $ret;
    }

    /**
     * 取得每日简报统计数据
     * @author Carter
     */
    public function queryStatUserDailyPageData($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $where = array();
        if (isset($attr['game_id'])) {
            $where['game_id'] = $attr['game_id'];
        }
        if (isset($attr['start_date'])) {
            $where['data_time'][] = array('egt', strtotime($attr['start_date']));
        }
        if (isset($attr['end_date'])) {
            $where['data_time'][] = array('elt', strtotime($attr['end_date']));
        }

        try {
            // 分页获取
            $pageSize = C('PAGE_SIZE');
            $count = $this->where($where)->count();
            $paginate = new \Think\Page($count, $pageSize);
            $pagination = $paginate->show();

            $page = $paginate->getCurPage();

            $field = 'id,data_time,add_user,login_user,active_user,consume_prop';
            $list = $this->field($field)->where($where)->order('data_time DESC')->page("{$page},{$pageSize}")->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryStatUserDailyPageData] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data']['list'] = $list;
        $ret['data']['pagination'] = $pagination;
        return $ret;
    }

    /**
     * 取得每日简报全部数据
     * @author liyao
     */
    public function queryStatUserDailyAllData($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $where = array();
        if (isset($attr['game_id'])) {
            $where['game_id'] = $attr['game_id'];
        }
        if (isset($attr['start_date'])) {
            $where['data_time'][] = array('egt', strtotime($attr['start_date']));
        }
        if (isset($attr['end_date'])) {
            $where['data_time'][] = array('elt', strtotime($attr['end_date']));
        }

        try {
            $field = 'data_time,add_user,active_user,consume_prop';
            $list = $this->field($field)->where($where)->order('data_time DESC')->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryStatUserDailyAllData] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data']['list'] = $list;
        return $ret;
    }

    /**
     * 获取指定游戏最近一天的统计信息
     * @author Carter
     */
    public function queryStatDailyLatestInfo($gameId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        try {
            $field = "id,data_time";
            $info = $this->field($field)->where(array('game_id' => $gameId))->order('data_time DESC')->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryStatDailyLatestInfo] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $info;
        return $ret;
    }

    /**
     * 插入每日简报统计数据
     * @author Carter
     */
    public function insertStatUserDaily($gameid, $timeStamp, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $insertData = array(
            'game_id' => $gameid,
            'data_time' => $timeStamp,
            'add_user' => $attr['addUser'],
            'login_user' => $attr['loginUser'],
            'active_user' => $attr['activeUser'],
            'consume_prop' => $attr['consumeProp'],
        );

        try {
            $id = $this->add($insertData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertStatUserDaily] insert failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_ADD_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $id;

        return $ret;
    }

    /**
     * 更新每日简报数据
     * @author Carter
     */
    public function updateStatUserDaily($statId, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $updateData = array(
            'add_user' => $attr['addUser'],
            'login_user' => $attr['loginUser'],
            'active_user' => $attr['activeUser'],
            'consume_prop' => $attr['consumeProp'],
        );

        try {
            $this->where(array('id' => $statId))->save($updateData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateStatUserDaily] update failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        return $ret;
    }
}
