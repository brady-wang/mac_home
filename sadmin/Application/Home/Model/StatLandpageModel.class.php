<?php
namespace Home\Model;
use Think\Model;

class StatLandpageModel extends Model
{
    /**
     * 查询统计数据
     * @author liyao
     */
    public function queryLandpageStaticsData($where)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        try {
            $list = $this->where($where)->order('id DESC')->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryLandpageStaticsData] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data']['list'] = $list;
        return $ret;
    }

}

