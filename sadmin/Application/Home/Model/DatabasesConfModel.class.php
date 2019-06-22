<?php
namespace Home\Model;

use Think\Model;

class DatabasesConfModel extends Model
{
    // 类型
    const DB_TYPE_GAME_DEV = 31;          // 游戏主库
    const DB_TYPE_GAME_LOG_DEV = 33;      // 游戏日志库
    const DB_TYPE_DSQP_DICT = 41;         // 游戏字典库
    const DB_TYPE_DSQP_ACTIVITY = 42;     // 活动库
    const DB_TYPE_DSQP_LOG_ACTIVITY = 44; // 活动日志库
    const DB_TYPE_ALL_CLUB_DICT = 45;     // 亲友圈字典库
    const DB_TYPE_CLUB_LOG = 47;          // 亲友圈日志库
    const DB_TYPE_CLUB = 50;              // 亲友圈代理商库
    public $typeMap = array(
        self::DB_TYPE_GAME_DEV => '游戏主库',
        self::DB_TYPE_GAME_LOG_DEV => '游戏日志库',
        self::DB_TYPE_DSQP_DICT => '游戏字典库',
        self::DB_TYPE_DSQP_ACTIVITY => '活动库',
        self::DB_TYPE_DSQP_LOG_ACTIVITY => '活动日志库',
        self::DB_TYPE_CLUB => '亲友圈代理商库',
        self::DB_TYPE_ALL_CLUB_DICT => '亲友圈字典库',
        self::DB_TYPE_CLUB_LOG => '亲友圈日志库',
    );

    /**
     * 获得单条数据
     * @param int $gameid 游戏ID
     * @param int $abType 游戏库类型
     * @return array|boole 返回单挑数据或false
     * @author tangjie <tj_youxiang_2008@163.com>
     */
    public function queryGameConfigInfo($gameId, $dbType)
    {
        static $config ;
        if (empty($gameId) || empty($dbType)) {
            return false;
        }
        $gameDb = $gameId.$dbType;
        if (isset($config[$gameDb])) {
            return $config[$gameDb];
        }

        try {
            $where = array(
                'game_id' => $gameId,
                'db_type' => $dbType
            );
            $field = 'db_type,game_id,host,port,user,pwd,db_name,charset,is_master';
            $data = $this->field($field)->where($where)->find();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGameConfigInfo] ".$e->getMessage());
            return false;
        }

        $config[$gameDb] = $data;
        return $data;
    }

    /**
     * 取得全部配置列表
     * @author liyao
     */
    public function queryDbConfAllList($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array();
        if (isset($attr['db_type'])) {
            $where['db_type'] = (int)$attr['db_type'];
        }
        if (isset($attr['game_id'])) {
            $where['game_id'] = (int)$attr['game_id'];
        }

        try {
            $field = "host,port,user,pwd,db_name,charset,is_master";
            $list = $this->field($field)->where($where)->select();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryDbConfAllList] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;
        return $ret;
    }

    /**
     * 获取外库配置列表
     * @author Carter
     */
    public function queryDbConfList($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $where = array();
        if ($attr['game_id']) {
            $where['game_id'] = array('in', array(0, (int)$attr['game_id']));
        }

        try {
            // 分页获取
            $pageSize = C('PAGE_SIZE');
            $count = $this->where($where)->count();
            $paginate = new \Think\Page($count, $pageSize);
            $pagination = $paginate->show();

            $page = $paginate->getCurPage();
            $field = 'id,db_type,game_id,host,port,user,pwd,db_name,charset,is_master,remark';
            $list = $this->field($field)->where($where)->order('game_id ASC,db_type ASC')->page("{$page},{$pageSize}")->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryDbConfList] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data']['list'] = $list;
        $ret['data']['pagination'] = $pagination;
        return $ret;
    }

    /**
     * 添加外库配置
     * @author liyao
     */
    public function insertDbConf($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $insertData = array(
            'game_id' => $attr['game_id'],
            'db_type' => $attr['db_type'],
            'host' => $attr['host'],
            'port' => $attr['port'],
            'user' => $attr['user'],
            'pwd' => $attr['pwd'],
            'db_name' => $attr['db_name'],
            'charset' => $attr['charset'],
            'is_master' => $attr['is_master'],
            'remark' => $attr['remark'],
            'create_time' => time(),
            'update_time' => time(),
        );

        try {
            $id = $this->add($insertData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertDbConf] insert failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_ADD_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = array(
            'id' => $id,
            'insert' => $insertData,
        );
        return $ret;
    }

    /**
     * 更新外库配置
     * @author liyao
     */
    public function updateDbConf($id, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try {
            $confInfo = $this->where(array('id' => $id))->find();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateDbConf] select failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if (empty($confInfo)) {
            $ret['code'] = ERRCODE_DB_DATA_EMPTY;
            $ret['msg'] = "外库配置{$id}数据不存在";
            return $ret;
        }

        // 先过滤出拥有相同 key 的数组，再获取 value 不同的列
        $intersectArr = array_intersect_key($attr, $confInfo);
        $updateData = array_diff_assoc($intersectArr, $confInfo);
        if ($updateData == array()) {
            $ret['code'] = ERRCODE_UPDATE_NONE;
            $ret['msg'] = '无任何修改';
            return $ret;
        }

        try {
            $this->where(array('id' => $id))->save($updateData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateDbConf] update failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        // 将修改内容返回记录
        $ret['data'] = $updateData;

        return $ret;
    }

    /**
     * 删除外库配置
     * @author liyao
     */
    public function deleteDbConf($id)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        try {
            $this->where(array('id' => $id))->delete();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[deleteDbConf] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_DELETE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        return $ret;
    }

    /**
     * 根据游戏id删除外库配置
     * @author carter
     */
    public function deleteDbConfByGameId($gameId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        try {
            $this->where(array('game_id' => $gameId))->delete();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[deleteDbConfByGameId] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_DELETE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        return $ret;
    }
}
