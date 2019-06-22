<?php
namespace Home\Model;

use Think\Model;

class SysDbsqlStatementModel extends Model
{
    // 状态
    const STATUS_PENDING = 1; // 未执行
    const STATUS_SUCCESS = 10; // 已执行（成功）
    const STATUS_FAILED = 15; // 已执行（报错）
    public $statusMap = array(
        self::STATUS_PENDING => array('name' => '未执行', 'text' => 'text-muted'),
        self::STATUS_SUCCESS => array('name' => '已执行（成功）', 'text' => 'text-success'),
        self::STATUS_FAILED => array('name' => '已执行（报错）', 'text' => 'text-danger'),
    );

    /**
     * 根据父 id 数组获取语句列表
     * @author Carter
     */
    public function querySysSqlStatementList($idArr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try {
            $field = 'id,sql_id,sql_statement,status';
            $list = $this->field($field)->where(array('sql_id' => array('in', $idArr)))->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[querySysSqlStatementList] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $list;
        return $ret;
    }

    /**
     * 插入SQL申请对应的语句
     * @author Carter
     */
    public function insertSysSqlStatement($sqlId, $statement)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $insertData = array(
            'sql_id' => $sqlId,
            'sql_statement' => $statement,
            'status' => self::STATUS_PENDING,
        );

        try {
            $id = $this->add($insertData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertSysSqlStatement] insert failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_ADD_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = array_merge(array('id' => $id), $insertData);

        return $ret;
    }

    /**
     * 根据 id 修改语句
     * @author Carter
     */
    public function updateSysSqlStatement($id, $sqlId, $statement)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        try {
            $field = 'sql_id,status';
            $info = $this->field($field)->where(array('id' => $id))->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateSysSqlStatement] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        // SQL父id校验
        if ($sqlId != $info['sql_id']) {
            $ret['code'] = ERRCODE_DATA_ERR;
            $ret['msg'] = "语句id与SQL父id不符，不能修改";
            return $ret;
        }

        // 状态校验
        if (self::STATUS_PENDING != $info['status']) {
            $ret['code'] = ERRCODE_DATA_ERR;
            $ret['msg'] = "只有未执行的语句可以修改";
            return $ret;
        }

        try {
            $this->where(array('id' => $id))->save(array('sql_statement' => $statement));
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateSysSqlStatement] update failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        return $ret;
    }

    /**
     * 执行 sql 语句
     * @author Carter
     */
    public function execExecuteSysSqlStatement($sqlId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        try {
            $field = 'id,sql_statement,status';
            $list = $this->field($field)->where(array('sql_id' => $sqlId))->order('id ASC')->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[execExecuteSysSqlStatement] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        // 先统一校验，再逐句执行
        foreach ($list as $v) {
            // 状态校验
            if (self::STATUS_PENDING != $v['status']) {
                set_exception(__FILE__, __LINE__, "[execExecuteSysSqlStatement] id {$v['id']}, status {$v['status']}");
                $ret['code'] = ERRCODE_DATA_ERR;
                $ret['msg'] = "只有未执行的语句可以执行";
                return $ret;
            }
        }

        foreach ($list as $v) {
            // 执行语句
            $execErrMsg = '';
            try {
                $this->execute($v['sql_statement']);
            } catch(\Exception $e) {
                $execErrMsg = $e->getMessage();
            }

            // 执行失败，更新语句执行结果
            try {
                if ($execErrMsg) {
                    $ret['code'] = ERRCODE_DATA_ERR;
                    $ret['msg'] = "语句执行报错：{$execErrMsg}";

                    $this->where(array('id' => $v['id']))->save(array('status' => self::STATUS_FAILED));
                } else {
                    $this->where(array('id' => $v['id']))->save(array('status' => self::STATUS_SUCCESS));
                }
            } catch(\Exception $e) {
                set_exception(__FILE__, __LINE__, "[execExecuteSysSqlStatement] update failed: ".$e->getMessage());
                $ret['code'] = ERRCODE_DATA_ERR;
                $ret['msg'] = $e->getMessage();
                return $ret;
            }

        }

        return $ret;
    }
}
