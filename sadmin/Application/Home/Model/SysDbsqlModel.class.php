<?php
namespace Home\Model;

use Think\Model;

class SysDbsqlModel extends Model
{
    // 状态
    const STATUS_PENDING = 1; // 待审核
    const STATUS_SUCCESS = 10; // 已执行（成功）
    const STATUS_FAILED = 15; // 已执行（报错）
    const STATUS_REJECT = 20; // 驳回
    const STATUS_CANCEL = 99; // 取消
    public $statusMap = array(
        self::STATUS_PENDING => array('name' => '待审核', 'label' => 'label-warning'),
        self::STATUS_SUCCESS => array('name' => '已执行（成功）', 'label' => 'label-success'),
        self::STATUS_FAILED => array('name' => '已执行（报错）', 'label' => 'label-danger'),
        self::STATUS_REJECT => array('name' => '驳回', 'label' => 'label-danger'),
        self::STATUS_CANCEL => array('name' => '取消', 'label' => 'label-default'),
    );

    /**
     * 获取后台库所有表及表结构
     * @author Carter
     */
    public function querySysDbTableStruct()
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $tbData = array();

        try {
            $tbList = $this->query('show tables');
            foreach ($tbList as $v) {
                $tableName = current($v);
                $info = $this->query("show create table {$tableName}");
                $tableStruct = $info[0]['Create Table'];
                $tbData[] = array(
                    'tbname' => $tableName,
                    'struct' => $tableStruct,
                );
            }
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[querySysDbTableStruct] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $tbData;
        return $ret;
    }

    /**
     * 根据 id 获取记录
     * @author Carter
     */
    public function querySysSqlInfoById($id, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        try {
            $info = $this->field($field)->where(array('id' => $id))->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[querySysSqlInfoById] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        if (empty($info)) {
            $ret['code'] = ERRCODE_DB_DATA_EMPTY;
            $ret['msg'] = "{$id} 不存在该记录";
            return $ret;
        }

        $ret['data'] = $info;
        return $ret;
    }

    /**
     * 获取数据库流水列表
     * @author Carter
     */
    public function querySysSqlExceList($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        // 查询字段
        $field = "id,status,sql_describe,requester_id";

        // 查询条件
        $where = array();
        if ($attr['status']) {
            $where['status'] = $attr['status'];
        }
        if ($attr['table_name']) {
            $where['table_name'] = $attr['table_name'];
        }
        if ($attr['start_date']) {
            // 确保时间是从指定日期的0点0分0秒开始
            $startStamp = strtotime(date('Y-m-d', strtotime($attr['start_date'])));
            $where['request_time'][] = array('egt', $startStamp);
        }
        if ($attr['end_date']) {
            // 确保时间是从指定日期的23点59分59秒结束
            $endStamp = strtotime(date('Y-m-d 23:59:59', strtotime($attr['end_date'])));
            $where['request_time'][] = array('elt', $endStamp);
        }

        try {
            // 分页
            $pageSize = C('PAGE_SIZE');
            $count = $this->where($where)->count();
            $paginate = new \Think\Page($count, $pageSize);
            $pagination = $paginate->show();
            $page = $paginate->getCurPage();

            // 获取数据库执行流水数据
            $sqlList = $this->field($field)->where($where)->page("{$page},{$pageSize}")->order("id DESC")->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[querySysSqlExceList] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data']['list'] = $sqlList;
        $ret['data']['pagination'] = $pagination;
        return $ret;
    }

    /**
     * 插入数据库待执行语句
     * @author Carter
     */
    public function insertSysSqlApply($tableName, $describle, $requesterId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $insertData = array(
            'status' => self::STATUS_PENDING,
            'table_name' => $tableName,
            'sql_describe' => $describle,
            'requester_id' => $requesterId,
            'request_time' => time(),
            'executor_id' => 0,
            'execute_time' => 0,
            'remark' => '',
        );
        try {
            $id = $this->add($insertData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertSysSqlApply] insert failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_ADD_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = array_merge(array('id' => $id), $insertData);
        return $ret;
    }

    /**
     * 更新数据库语句信息
     * @author Carter
     */
    public function updateSysSqlRow($id, $status, $remark)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        $updateData = array(
            'status' => $status,
            'executor_id' => C('G_USER.uid'),
            'execute_time' => time(),
            'remark' => $remark,
        );

        try {
            $this->where(array('id' => $id))->save($updateData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateSysSqlRow] update failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        return $ret;
    }

    /**
     * 取消数据库修改申请
     * @author Carter
     */
    public function exceCancelSysSqlApply($id, $uid, $remark)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        try {
            $field = 'status,requester_id';
            $info = $this->field($field)->where(array('id' => $id))->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[exceCancelSysSqlApply] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        // 状态校验
        if (self::STATUS_PENDING != $info['status']) {
            set_exception(__FILE__, __LINE__, "[exceCancelSysSqlApply] status {$info['status']}");
            $ret['code'] = ERRCODE_DATA_ERR;
            $ret['msg'] = "只能取消待审核的语句";
            return $ret;
        }

        // 申请人校验
        if ($uid != $info['requester_id']) {
            set_exception(__FILE__, __LINE__, "[exceCancelSysSqlApply] uid {$uid}, requester {$info['requester_id']}");
            $ret['code'] = ERRCODE_DATA_ERR;
            $ret['msg'] = "只能取消自己申请的语句";
            return $ret;
        }

        $updateData = array(
            'status' => self::STATUS_CANCEL,
            'remark' => $remark,
        );

        try {
            $this->where(array('id' => $id))->save($updateData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[exceCancelSysSqlApply] update failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        return $ret;
    }

    /**
     * 驳回 sql 语句
     * @author Carter
     */
    public function execRejectSysSqlApply($id, $remark)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        try {
            $info = $this->field('status')->where(array('id' => $id))->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[execRejectSysSqlApply] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        // 状态校验
        if (self::STATUS_PENDING != $info['status']) {
            set_exception(__FILE__, __LINE__, "[execRejectSysSqlApply] id {$id}, status {$info['status']}");
            $ret['code'] = ERRCODE_DATA_ERR;
            $ret['msg'] = "只有未执行的语句可以驳回";
            return $ret;
        }

        $updateData = array(
            'status' => self::STATUS_REJECT,
            'executor_id' => C('G_USER.uid'),
            'execute_time' => time(),
            'remark' => $remark,
        );

        try {
            $this->where(array('id' => $id))->save($updateData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[execRejectSysSqlApply] update failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DATA_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        return $ret;
    }
}
