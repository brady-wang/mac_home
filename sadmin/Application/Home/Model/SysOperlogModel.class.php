<?php
namespace Home\Model;

use Think\Model;

class SysOperlogModel extends Model
{
    /**
     * 通过参数获取查询流水列表
     * @author Carter
     */
    public function querySysOperateLogByWhere($where, $field = "*")
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        // 分页获取
        $pageSize = C('PAGE_SIZE');
        $count = $this->where($where)->count();
        $paginate = new \Think\Page($count, $pageSize);
        $pagination = $paginate->show();

        $page = $paginate->getCurPage();
        try {
            $list = $this->field($field)->where($where)->order('id DESC')->page("{$page},{$pageSize}")->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[querySysOperateLogByWhere] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data']['list'] = $list;
        $ret['data']['pagination'] = $pagination;
        return $ret;
    }

    /**
     * 插入操作流水
     * @author Carter
     */
    public function insertSysOperateLog($uid, $gameId, $main, $sublevel, $third, $operCode, $cont)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $insertData = array(
            'uid' => $uid,
            'game_id' => $gameId,
            'main_code' => $main,
            'sublevel_code' => $sublevel,
            'third_code' => $third,
            'oper_code' => $operCode,
            'oper_cont' => $cont,
            'create_time' => time(),
        );
        try {
            $this->add($insertData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertSysOperateLog] insert failed ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_ADD_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        return $ret;
    }
}
