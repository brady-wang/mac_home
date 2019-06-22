<?php
namespace Home\Model;

use Think\Model;

class SysErrlogModel extends Model
{
    const STATUS_UNTREATED = 1; // 未处理
    const STATUS_TREATED = 2; // 已处理
    const STATUS_IGNORE = 3; // 不处理

    /**
     * 根据 id 获取系统错误流水
     * @author Carter
     */
    public function querySysExceptionById($id)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try {
            $field = "id,exce_file,exce_line,repeat_count,exce_log,create_time,handle_status,handler_id,handle_time,handle_remark";
            $info = $this->field($field)->where("id = %d", $id)->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[querySysExceptionById] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] =  $e->getMessage();
            return $ret;
        }
        if (empty($info)) {
            $ret['code'] = ERRCODE_DB_DATA_EMPTY;
            $ret['msg'] = '数据不存在';
            return $ret;
        }
        $ret['data'] = $info;
        return $ret;
    }

    /**
     * 获取错误流水列表
     * @author Carter
     */
    public function querySysExceptionList($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array();
        if ($attr['handle_status']) {
            $where['handle_status'] = $attr['handle_status'];
        }
        if ($attr['start_date']) {
            // 确保时间是从指定日期的0点0分0秒开始
            $startStamp = strtotime(date('Y-m-d', strtotime($attr['start_date'])));
            $where['create_time'][] = array('egt', $startStamp);
        }
        if ($attr['end_date']) {
            // 确保时间是从指定日期的23点59分59秒结束
            $endStamp = strtotime(date('Y-m-d 23:59:59', strtotime($attr['end_date'])));
            $where['create_time'][] = array('elt', $endStamp);
        }

        // 分页获取
        $pageSize = C('PAGE_SIZE');
        $count = $this->where($where)->count();
        $paginate = new \Think\Page($count, $pageSize);
        $pagination = $paginate->show();

        $page = $paginate->getCurPage();
        try {
            $list = $this->where($where)->order('id DESC')->page("{$page},{$pageSize}")->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[querySysExceptionList] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data']['list'] = $list;
        $ret['data']['pagination'] = $pagination;
        return $ret;
    }

    /**
     * 批量获取错误流水的内容，文件和行数相同，则认为属于同一批次
     * @author Carter
     */
    public function querySysExceptionBatch($id)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );
        try {
            $info = $this->field("exce_file,exce_line")->where(array('id' => $id))->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[querySysExceptionBatch] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] =  $e->getMessage();
            return $ret;
        }
        if (empty($info)) {
            $ret['code'] = ERRCODE_DB_DATA_EMPTY;
            $ret['msg'] = '错误记录不存在';
            return $ret;
        }

        $where = array(
            'exce_file' => $info['exce_file'],
            'exce_line' => $info['exce_line'],
            'handle_status' => self::STATUS_UNTREATED, // 只获取未处理的
        );
        try {
            $list = $this->field("exce_log")->where($where)->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[querySysExceptionBatch] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] =  $e->getMessage();
            return $ret;
        }

        $ret['data'] = array(
            'file' => $info['exce_file'],
            'line' => $info['exce_line'],
            'info' => $list,
        );
        return $ret;
    }

    /**
     * 插入错误流水
     * @author Carter
     */
    public function insertSysException($file, $line, $logCont)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        // 通过内容的 md5 值作为相同错误流水的索引
        $contIndex = md5(serialize(array(
            'file' => $file,
            'line' => $line,
            'cont' => $logCont,
        )));

        // 检查是否已有相同记录
        try {
            $where = array(
                'md5_index' => $contIndex,
                'handle_status' => '1',
            );
            $log = $this->where($where)->find();
        } catch(\Exception $e) {
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        // 不存在重复的流水，添加
        if (empty($log)) {
            $insertData = array(
                'md5_index' => $contIndex,
                'exce_file' => $file,
                'exce_line' => $line,
                'repeat_count' => 1,
                'exce_log' => $logCont,
                'create_time' => time(),
                'handle_status' => self::STATUS_UNTREATED,
            );
            try {
                $this->add($insertData);
            } catch(\Exception $e) {
                $this->ret['code'] = ERRCODE_DB_ADD_ERR;
                $this->ret['msg'] = $e->getMessage();
                return $ret;
            }
        }
        // 存在重复流水，只更新
        else {
            $updateData = array(
                'repeat_count' => intval($log['repeat_count']) + 1,
                'create_time' => time(),
            );
            try {
                $this->where(array('id' => $log['id']))->save($updateData);
            } catch(\Exception $e) {
                $this->ret['code'] = ERRCODE_DB_UPDATE_ERR;
                $this->ret['msg'] = $e->getMessage();
                return $ret;
            }
        }

        return $ret;
    }

    /**
     * 修改错误流水
     * @author Carter
     */
    public function updateSysException($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $updateData = array(
            'handle_status' => $attr['handle_status'],
            'handler_id' => C('G_USER.uid'),
            'handle_time' => time(),
            'handle_remark' => $attr['handle_remark'],
        );

        try {
            $this->where(array('id' => $attr['id']))->save($updateData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateSysException] update failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $updateData;

        return $ret;
    }

    /**
     * 批量修改错误流水
     * @author Carter
     */
    public function updateSysExceptionBatch($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $id = $attr['id'];

        try {
            $info = $this->field("exce_file,exce_line")->where(array('id' => $attr['id']))->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateSysExceptionBatch] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if (empty($info)) {
            $ret['code'] = ERRCODE_DB_DATA_EMPTY;
            $ret['msg'] = '错误记录不存在';
            return $ret;
        }

        $where = array(
            'exce_file' => $info['exce_file'],
            'exce_line' => $info['exce_line'],
            'handle_status' => self::STATUS_UNTREATED,
        );
        $updateData = array(
            'handle_status' => $attr['handle_status'],
            'handler_id' => C('G_USER.uid'),
            'handle_time' => time(),
            'handle_remark' => $attr['handle_remark'],
        );
        try {
            $this->where($where)->save($updateData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateSysExceptionBatch] update failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = array(
            'where' => $where,
            'update' => $updateData,
        );
        return $ret;
    }
}
