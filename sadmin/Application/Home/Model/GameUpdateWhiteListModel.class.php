<?php
/**
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/8/27 0027
 * Time: 16:26
 */

namespace Home\Model;


use Think\Model;

class GameUpdateWhiteListModel extends Model
{
    // 白名单类型
    const WHITE_TYPE_IP = 1; // ip
    const WHITE_TYPE_UNIONID = 2; // 微信unionId
    const WHITE_TYPE_UID = 3; // 游戏uid
    public $whiteTypeMap = array(
        self::WHITE_TYPE_IP => array('name' => 'ip', 'label' => 'label-danger'),
        self::WHITE_TYPE_UNIONID => array('name' => '微信unionId', 'label' => 'label-success'),
        self::WHITE_TYPE_UID => array('name' => '游戏uid', 'label' => 'label-primary'),
    );

    /**
     * 通过参数获取所有热更新白名单列表
     * @author Carter
     */
    public function queryGameUpdateWhiteUserByAttr($gameId, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array('game_id' => $gameId);
        if ($attr['white_type']) {
            $where['white_type'] =  $attr['white_type'];
        }
        if ($attr['white_val']) {
            $where['white_val'] = (int) $attr['white_val'];
        }

        try {
            // 分页获取
            $pageSize = C('PAGE_SIZE');
            $count = $this->where($where)->count();
            $paginate = new \Think\Page($count, $pageSize);
            $pagination = $paginate->show();

            $page = $paginate->getCurPage();

            $field = 'id,white_type,white_val,remark';
            $list = $this->field($field)->where($where)->order('id DESC')->page("{$page},{$pageSize}")->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGameUpdateWhiteUserByAttr] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data']['list'] = $list;
        $ret['data']['pagination'] = $pagination;
        return $ret;
    }

    /**
     * 通过参数获取所有白名单列表，不分页
     * @author Neo
     */
    public function queryAllGameUpdateWhiteListByAttr($gameId)
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
            $field = 'white_type,white_val';
            $list = $this->field($field)->where($where)->select();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryAllGameWhiteListByAttr] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;
        return $ret;
    }

    /**
     * 添加白名单(热更新)
     * @author Neo
     */
    public function insertGameUpdateWhiteList($gameId, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        // 检查同名
        $where = array(
            'game_id' => $gameId,
            'white_type' => $attr['white_type'],
            'white_val' => $attr['white_val'],
        );
        try {
            $info = $this->field('id')->where($where)->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertGameWhiteList] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if (!empty($info)) {
            $ret['code'] = ERRCODE_DATA_OVERLAP;
            $ret['msg'] = '已存在同类型白名单';
            return $ret;
        }

        // 插入数据
        $insertData = array(
            'game_id' => $gameId ,
            'white_type' =>  $attr['white_type'],
            'white_val' =>  $attr['white_val'],
            'remark' =>  $attr['remark'],
        );
        try {
            $id = $this->add($insertData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertGameWhiteList] insert failed: ".$e->getMessage());
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

    /**
     * 删除白名单用(热更新)
     * @author Neo
     */
    public function deleteGameUpdateWhiteList($id)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        try {
            $this->where(array('id' => $id))->delete();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[deleteGameWhiteList] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_DELETE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        return $ret;
    }
}