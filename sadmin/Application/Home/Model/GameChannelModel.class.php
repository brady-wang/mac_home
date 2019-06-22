<?php
namespace Home\Model;

use Think\Model;

class GameChannelModel extends Model
{
    /**
     * 根据条件查询渠道信息列表，不分页
     * @author Carter
     */
    public function queryGameAllChannelByAttr($attr, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $where = array();
        if ($attr['game_id']) {
            $where['game_id'] = $attr['game_id'];
        }
        if ($attr['channel_code']) {
            $where['code'] = $attr['channel_code'];
        }
        if ($attr['channel_name_like']) {
            $where['name'] = array('like', "%{$attr['channel_name_like']}%");
        }

        try {
            $list = $this->field($field)->where($where)->select();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGameAllChannelByAttr] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;
        return $ret;
    }

    /**
     * 得到标识码
     * @author liyao
     */
    public function getChannelCode($where = [], $gameid = "")
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        if (!empty($gameid)) {
            $game_id = $gameid;
        } else {
            $game_id = C('G_USER.gameid');
        }
        $wh = array("game_id" => $game_id);
        if (isset($where["os"]))
            $wh["os"] = $where["os"];
        try {
            $list = $this->where($wh)->order('code ASC')->select();
        } catch (\Exception $e) {
                set_exception(__FILE__, __LINE__, "[getChannelCode] select failed: ".$e->getMessage());
                $ret['code'] = ERRCODE_DB_SELECT_ERR;
                $ret['msg'] = $e->getMessage();
                return $ret;

        }
        $ret['data']['list'] = $list;
        return $ret;
    }

    /**
     * 得到游戏对应的平台类型
     * @author liyao
     */
    public function getOsType($gameid = "")
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        if (!empty($gameid)) {
            $game_id = $gameid;
        } else {
            $game_id = C('G_USER.gameid');
        }
        $wh = array("game_id" => $game_id);
        try {
            $list = $this->field('os')->where($wh)->group('os')->order('os ASC')->select();
        } catch (\Exception $e) {
                set_exception(__FILE__, __LINE__, "[getOsType] select failed: ".$e->getMessage());
                $ret['code'] = ERRCODE_DB_SELECT_ERR;
                $ret['msg'] = $e->getMessage();
                return $ret;
        }
        $ret['data']['list'] = $list;
        return $ret;
    }
}
