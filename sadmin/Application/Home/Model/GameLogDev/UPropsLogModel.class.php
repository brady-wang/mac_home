<?php
namespace Home\Model\GameLogDev;

use Think\Model;

class UPropsLogModel extends Model
{
    // 初始配置
    protected $connection = 'CONF_DBTYPE_GAME_LOG_DEV';
    protected $trueTableName = 'u_props_log';

    public function __construct()
    {
        parent::__construct();
    }

    // 途径
    const WAY_RECHARGE = 0; // 充值
    const WAY_DAILY_SHARE = 12; // 每日分享
    const WAY_MSG_INGOT = 17; // 邮件送元宝
    const WAY_INVITE = 21; // 填写邀请人
    const WAY_INVITE_FRIEND = 32; // 邀请好友
    const WAY_API = 98; // Web 接口
    const WAY_ROOM = 100; // 牌局结束扣钻
    const WAY_REGISTER = 101; // 创建账号
    const WAY_WIN_INGOT = 110; // 大赢家
    const WAY_API2 = 115; // Web 接口

    // 货币
    const PROP_DIAMOND = 10008; // 钻石
    const PROP_INGOT = 10009; // 元宝

    /**
     * 根据条件获取资产日志数据
     * @author Carter
     */
    public function queryDevLogPropListByAttr($attr, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        /*
         * propsid 10008 钻石
         * way 0 游戏内购
         * way 12 分日分享
         * way 21,32 邀请好友
         * way 100 钻石消耗
         */

        $where = array();
        if (isset($attr['way'])) {
            if (is_array($attr['way'])) {
                $where['way'] = array('in', $attr['way']);
            } else {
                $where['way'] = $attr['way'];
            }
        }
        if (isset($attr['gameId'])) {
            $where['gameId'] = $attr['gameId'];
        }
        if (isset($attr['sCreateTime'])) {
            $where['createTime'][] = array('egt', $attr['sCreateTime']);
        }
        if (isset($attr['eCreateTime'])) {
            $where['createTime'][] = array('elt', $attr['eCreateTime']);
        }
        if (isset($attr['propsId'])) {
            $where['propsId'] = $attr['propsId'];
        }

        try {
            $list = $this->field($field)->where($where)->select();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryDevLogPropListByAttr] select failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;
        return $ret;
    }

    /**
     * 获取指定游戏下首条游戏资产日志
     * @author Carter
     */
    public function queryFirstGamePropsLog()
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try {
            $field = 'createTime';
            $info = $this->field($field)->order('createTime ASC')->find();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryFirstGamePropsLog] select failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if (empty($info)) {
            $ret['code'] = ERRCODE_DB_DATA_EMPTY;
            $ret['msg'] = '没有游戏资产日志';
            return $ret;
        }
        $ret['data'] = $info;
        return $ret;
    }

    /**
     * 获取指定游戏下指定日期的钻石产出日志列表
     * @author Carter
     */
    public function queryGamePropsDiamondProduceList($gameId, $time)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        /*
         * propsid 10008 钻石
         * way 0 游戏内购
         * way 12 分日分享
         * way 17 邮件送钻
         * way 21,32 邀请好友
         */

        try {
            $field = 'id,way,propsNum';
            $where = array(
                'way' => array('in', '0,12,17,21,32'),
                //'gameId' => $gameId, // 现在prop表中许多way都是不填gameId字段的，等后端把该字段补充完整再加上该条件
                'createTime' => array(
                    'between',
                    array(date('Y-m-d 00:00:00', $time), date('Y-m-d 23:59:59', $time)),
                ),
                'propsId' => 10008,
            );
            $list = $this->field($field)->where($where)->select();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGamePropsDiamondProduceList] select failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;
        return $ret;
    }

    /**
     * 条件获取钻石/房卡消费全部列表
     * 主要用于数据统计报表时，钻石消耗计算条件。
     * @param array $where 查询条件
     * @param int $startNum 开始行
     * @param int $limitNum 查询数据行
     * @param string $field 字段名
     * @author tangjie
     */
    public function queryUPropsLogsListByWhere($where, $startNum = 0 ,$limitNum = 0 ,$field = '*')
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        if (empty($where)) {
            $ret['code'] = ERRCODE_PARAM_NULL;
            $ret['msg'] = '查询钻石产出日志为空';
            return $ret ;
        }
        try {
            if ($limitNum) {
                $ret['data'] = $this->field($field)->where($where)->order(' id DESC ')->limit($startNum, $limitNum)->select();
            } else {
                $ret['data'] = $this->field($field)->where($where)->order(' id DESC ')->select();
            }
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryUPropsLogsListByWhere] select failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        return $ret;
    }

    /**
     * 查询消耗的钻石或元宝总数
     * @author liyao
     */
    public function queryPropsCount($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array();
        if (isset($attr['userId'])) {
            $where['userId'] = $attr['userId'];
        }
        if (isset($attr['propsId'])) {
            $where['propsId'] = $attr['propsId'];
        }
        $where["_string"] = "propsBefore > propsAfter";
        try {
            $ret['data'] = $this->field("SUM(propsBefore-propsAfter) as prop")->where($where)->select();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryPropsCount] select failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        return $ret;
    }
}
