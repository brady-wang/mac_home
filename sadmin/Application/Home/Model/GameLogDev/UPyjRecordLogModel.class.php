<?php
namespace Home\Model\GameLogDev;

use Think\Model;

class UPyjRecordLogModel extends Model
{
    // 初始配置
    protected $connection = 'CONF_DBTYPE_GAME_LOG_DEV';
    protected $trueTableName = 'u_pyj_record_log';

    // 付费类型
    const PYJ_PAY_TYPE_CLUBLE = 0; // 俱乐部收费
    const PYJ_PAY_TYPE_OWNER = 1; // 房主收费
    const PYJ_PAY_TYPE_WINNER = 2; // 大赢家收费
    const PYJ_PAY_TYPE_AA = 3; // AA付费
    public $typeMap = array(
        self::PYJ_PAY_TYPE_CLUBLE => '俱乐部付费',
        self::PYJ_PAY_TYPE_OWNER => '房主付费',
        self::PYJ_PAY_TYPE_WINNER => '大赢家付费',
        self::PYJ_PAY_TYPE_AA => 'AA付费',
    );

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取指定日期内的指定数据
     * @author Carter
     */
    public function queryUPyjRecordLogListByDate($dateTime, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        // 'between xxx and yyy' 在效率上与 '>= xxx and <= yyy' 无差
        $sTime = date('Y-m-d 00:00:00', $dateTime);
        $eTime = date('Y-m-d 23:59:59', $dateTime);
        $where = array(
            'gameStartTime' => array('between', array($sTime, $eTime)),
        );

        try {
            $list = $this->field($field)->where($where)->select();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryUPyjRecordLogListByDate] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $list;

        return $ret;
    }

    /**
     * 获取指定时间段内所有成功开局的数据
     * @author Carter
     */
    public function queryUPyjOpeningRecordLogByTime($startTime, $endTime, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $sTime = date('Y-m-d H:i:s', $startTime);
        $eTime = date('Y-m-d H:i:s', $endTime);
        $where = array(
            'roundCount' => array('gt', 0),
            'gameStartTime' => array('between', array($sTime, $eTime)),
        );

        try {
            $list = $this->field($field)->where($where)->select();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryUPyjOpeningRecordLogByTime] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $list;

        return $ret;
    }

    /**
     * 分段获取指定时间段内所有成功开局的数据
     * @author Carter
     */
    public function queryUPyjOpeningRecordLogByBlock($startTime, $endTime, $field, $limit, $page)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array(
            'roundCount' => array('gt', 0),
            'gameStartTime' => array('between', array($startTime, $endTime)),
        );

        try {
            $list = $this->field($field)->where($where)->order('id DESC')->limit($limit)->page($page)->select();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryUPyjOpeningRecordLogByBlock] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $list;

        return $ret;
    }

    /**
     * 获取指定日期亲友圈房间开局记录，对clubId去重
     * @author Carter
     */
    public function queryUPyjClubRecordByDate($date)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array(
            'clubId' => array('gt', 0),
            'roundCount' => array('gt', 0),
            'gameStartTime' => array(
                array('egt', date('Y-m-d 00:00:00', strtotime($date))),
                array('elt', date('Y-m-d 23:59:59', strtotime($date))),
            ),
        );

        try {
            $list = $this->field('clubId')->where($where)->group('clubId')->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryUPyjClubRecordByDate] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;

        return $ret;
    }

    /**
     * 根据条件获取房间开局列表，不分页
     * @author Carter
     */
    public function queryUPyjRecordListByAttr($attr, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array();
        if (isset($attr['roomId'])) {
            if (is_array($attr['roomId'])) {
                $where['roomId'] = array('in', $attr['roomId']);
            } else {
                $where['roomId'] = $attr['roomId'];
            }
        }

        try {
            $list = $this->field($field)->where($where)->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryUPyjRecordListByAttr] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;

        return $ret;
    }

     /**
     * 通过参数查询列表
     * @author tangjie
     */
    public function queryPyjRecorListByAttr($where, $field = "*")
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try{
            // 分页获取
            $pageSize = 10;
            $count = $this->where($where)->count();
            $paginate = new \Think\Page($count, $pageSize);
            $pagination = $paginate->show();

            $page = $paginate->getCurPage();
            $recorList = $this->field($field)->where($where)->order('id DESC')->page("{$page},{$pageSize}")->select();

            foreach ($recorList as $key => $item ){

                $recorList[$key]['roomFreeType']  = $this->typeMap[ $item['payType'] ]? $this->typeMap[ $item['payType'] ] : ' 房主付费 ' ;

            }
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGameWhiteUserByAttr] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data']['list'] = $recorList;
        $ret['data']['pagination'] = $pagination;
        return $ret;
    }

    /**
     * 查询单条参数
     * @author tangjie
     */
    public function queryPyjRecorInfoByAttr($where, $field = "*")
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try{
            $info = $this->field($field)->where($where)->find();

            $info['roomFreeType'] =  $this->typeMap[ $info['payType'] ]? $this->typeMap[ $info['payType'] ] : ' 历史数据 ' ;
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryPyjRecorInfoByAttr] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $info;
        return $ret;
    }
}
