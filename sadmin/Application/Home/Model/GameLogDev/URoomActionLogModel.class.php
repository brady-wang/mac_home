<?php
namespace Home\Model\GameLogDev;

use Think\Model;

/**
 * 朋友局房间动作日志
 */
class URoomActionLogModel extends Model
{
    // 初始配置
    protected $connection = 'CONF_DBTYPE_GAME_LOG_DEV';
    protected $trueTableName = 'u_room_action_log';

    // 用户行为
    const ACTION_TYPE_CREATE = 1; // 创建房间
    const ACTION_TYPE_ENTER = 2; // 进入房间
    const ACTION_TYPE_REQUEST = 3; // 申请解散
    const ACTION_TYPE_ANSWER = 4; // 同意解散
    const ACTION_TYPE_REFUSE = 11; // 拒绝解散
    const ACTION_TYPE_KILL = 5; // 关闭房间
    const ACTION_TYPE_EXIT = 6; // 退出房间
    const ACTION_TYPE_START = 7; // 行牌开始
    const ACTION_TYPE_START2 = 8; // 创建桌子（当前不会被用到，其含义为：桌子创建完毕后的成功开局返回）
    const ACTION_TYPE_ROUND = 9; // 单局结算
    const ACTION_TYPE_LOGOUT_ROOM = 10; // 断线重连
    public $actionTypeMap = array(
        self::ACTION_TYPE_CREATE => '创建房间',
        self::ACTION_TYPE_ENTER => '进入房间',
        self::ACTION_TYPE_REQUEST => '申请解散',
        self::ACTION_TYPE_ANSWER => '同意解散',
        self::ACTION_TYPE_REFUSE => '拒绝解散',
        self::ACTION_TYPE_KILL => '关闭房间',
        self::ACTION_TYPE_EXIT => '退出房间',
        self::ACTION_TYPE_START => '行牌开始',
        self::ACTION_TYPE_START2 => '创建桌子（当前不会被用到，其含义为：桌子创建完毕后的成功开局返回）',
        self::ACTION_TYPE_ROUND => '单局结算',
        self::ACTION_TYPE_LOGOUT_ROOM => '断线重连',
    );

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 通过参数获取游戏房间日志列表
     * @author Carter
     */
    public function queryGameRoomActionLogByAttr($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array();
        if (!empty($attr['roomId'])) {
            $where['roomId'] = $attr['roomId'];
        }
        if (!empty($attr['userId'])) {
            $where['userId'] = $attr['userId'];
        }
        if (!empty($attr['start_date'])) {
            // 确保时间是从指定日期的0点0分0秒开始
            $where['datetime'][] = array('egt', date('Y-m-d 00:00:00', strtotime($attr['start_date'])));
        }
        if (!empty($attr['end_date'])) {
            // 确保时间是从指定日期的23点59分59秒结束
            $where['datetime'][] = array('elt', date('Y-m-d 23:59:59', strtotime($attr['end_date'])));
        }

        try {
            // 分页获取
            $pageSize = C('PAGE_SIZE');
            $count = $this->where($where)->count();
            $paginate = new \Think\Page($count, $pageSize);
            $pagination = $paginate->show();

            $page = $paginate->getCurPage();

            $field = "id,roomId,userId,nickName,actionType,datetime";
            $list = $this->field($field)->where($where)->order('id DESC')->page("{$page},{$pageSize}")->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGameRoomActionLogByAttr] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data']['list'] = $list;
        $ret['data']['pagination'] = $pagination;
        return $ret;
    }
}
