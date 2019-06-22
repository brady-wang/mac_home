<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/23 0023
 * Time: 11:13
 */

namespace Home\Model;


use Think\Model;

class GameAppSubversionModel extends Model
{

    /**
     * 获取游戏对应子游戏玩法版本
     * @param $gameId
     * @return array
     */
    public function queryGameAllSubversion($gameId, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array(
            'game_id' => $gameId,
        );

        try {
            $list = $this->field($field)->where($where)->select();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGameAllSubversion] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $list;
        return $ret;
    }

    /**
     * 指定子游戏热更版本
     * @param $gameId
     * @param $attr
     * @return array
     */
    public function updatePlayVersionByAttr($gameId, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        // 检查同名
        $where = array(
            'game_id' => $gameId,
            'play_id' => $attr['play_id']
        );

        try {
            $info = $this->field('id,version')->where($where)->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updatePlayVersionByAttr] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        if (!empty($info)) {

            if ($attr['version'] == $info['version']) {
                $ret['code'] = ERRCODE_UPDATE_NONE;
                $ret['msg'] = '没有修改';
                return $ret;
            }

            try {
                $this->where($where)->save(array('version' => $attr['version']));
            } catch(\Exception $e) {
                set_exception(__FILE__, __LINE__, "[updatePlayVersionByAttr] select failed: ".$e->getMessage());
                $ret['code'] = ERRCODE_DB_SELECT_ERR;
                $ret['msg'] = $e->getMessage();
                return $ret;
            }

            return $ret;
        }

        // 插入数据
        $insertData = array(
            'game_id' => $gameId ,
            'play_id' =>  $attr['play_id'],
            'version' =>  $attr['version']
        );
        try {
            $id = $this->add($insertData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updatePlayVersionByAttr] insert failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_ADD_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = array(
            'id' => $id,
            'insert' => $insertData,
        );

        return $ret;
    }
}