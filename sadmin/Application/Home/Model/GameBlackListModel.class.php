<?php
namespace Home\Model;

use Think\Model;

class GameBlackListModel extends Model
{
    // 黑名单类型
    const BLACK_TYPE_UID = 3; // 游戏uid
    public $blackTypeMap = array(
        self::BLACK_TYPE_UID => array('name' => '游戏uid', 'label' => 'label-primary'),
    );

    /**
     * 通过参数获取所有黑名单列表
     * @author Carter
     */
    public function queryGameBlackUserByAttr($gameId, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array('game_id' => $gameId);
        if ($attr['black_type']) {
            $where['black_type'] =  $attr['black_type'];
        }
        if ($attr['black_val']) {
            $where['black_val'] = (int) $attr['black_val'];
        }

        try {
            // 分页获取
            $pageSize = C('PAGE_SIZE');
            $count = $this->where($where)->count();
            $paginate = new \Think\Page($count, $pageSize);
            $pagination = $paginate->show();

            $page = $paginate->getCurPage();

            $field = 'id,black_type,black_val,remark';
            $list = $this->field($field)->where($where)->order('id DESC')->page("{$page},{$pageSize}")->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGameBlackUserByAttr] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data']['list'] = $list;
        $ret['data']['pagination'] = $pagination;
        return $ret;
    }

    /**
     * 通过参数获取所有黑名单列表，不分页
     * @author Carter
     */
    public function queryAllGameBlackListByAttr($gameId)
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
            $field = 'black_type,black_val';
            $list = $this->field($field)->where($where)->select();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryAllGameBlackListByAttr] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;
        return $ret;
    }

    /**
     * 添加黑名单
     * @author Carter
     */
    public function insertGameBlackList($gameId, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        // 检查同名
        $where = array(
            'game_id' => $gameId,
            'black_type' => self::BLACK_TYPE_UID,
            'black_val' => $attr['black_val'],
        );
        try {
            $info = $this->field('id')->where($where)->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertGameBlackList] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if (!empty($info)) {
            $ret['code'] = ERRCODE_DATA_OVERLAP;
            $ret['msg'] = '已存在同类型黑名单';
            return $ret;
        }

        // 插入数据
        $insertData = array(
            'game_id' => $gameId ,
            'black_type' =>  self::BLACK_TYPE_UID,
            'black_val' =>  $attr['black_val'],
            'remark' =>  $attr['remark'],
        );
        try {
            $id = $this->add($insertData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertGameBlackList] insert failed: ".$e->getMessage());
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
     * 删除黑名单用户
     * @author Carter
     */
    public function deleteGameBlackList($id)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        try {
            $this->where(array('id' => $id))->delete();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[deleteGameBlackList] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_DELETE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        return $ret;
    }
}
