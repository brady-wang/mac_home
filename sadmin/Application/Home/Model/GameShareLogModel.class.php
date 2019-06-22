<?php
namespace Home\Model;

use Think\Model;

class GameShareLogModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据条件获取游戏分享流水
     * @author Carter
     */
    public function queryGameShareLogByAttr($attr, $field)
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
        if (isset($attr['createDate'])) {
            $where['create_date'] = $attr['createDate'];
        }

        try {
            $list = $this->field($field)->where($where)->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGameShareLogByAttr] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;
        return $ret;
    }
}
