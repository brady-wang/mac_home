<?php
namespace Home\Model;

use Think\Model;

class GameShareDomainModel extends Model
{
    // 是否被封杀
    const IS_BLOCKADE_FALSE = 0; // 否
    const IS_BLOCKADE_TRUE = 1; // 是
    public $isBlockadeMap = array(
        self::IS_BLOCKADE_FALSE => array('name' => '否', 'label' => 'label-success'),
        self::IS_BLOCKADE_TRUE => array('name' => '是', 'label' => 'label-danger'),
    );

    // 状态
    const STATUS_NORMAL = 1; // 正常
    const STATUS_CLOSE = 9; // 关闭
    public $statusMap = array(
        self::STATUS_NORMAL => array('name' => '正常', 'label' => 'label-success'),
        self::STATUS_CLOSE => array('name' => '关闭', 'label' => 'label-default'),
    );

    /**
     * 获取指定域名配置
     * @author carter
     */
    public function queryGameShareDomainById($id, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        try {
            $info = $this->field($field)->where(array('id' => $id))->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGameShareDomainById] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if (empty($info)) {
            $ret['code'] = ERRCODE_DB_DATA_EMPTY;
            $ret['msg'] = "domain {$id} is empty";
            return $ret;
        }
        $ret['data'] = $info;
        return $ret;
    }

    /**
     * 根据参数获取域名配置列表
     * @author carter
     */
    public function queryGameShareDomainListByAttr($attr, $field = '*')
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $where = array();
        if ($attr['game_id']) {
            if (is_array($attr['game_id'])) {
                $where['game_id'] = array('in', $attr['game_id']);
            } else {
                $where['game_id'] = $attr['game_id'];
            }
        }
        if ($attr['share_min']) {
            $where['share_count'][] = array('egt', $attr['share_min']);
        }
        if ($attr['share_max']) {
            $where['share_count'][] = array('elt', $attr['share_max']);
        }
        if ($attr['status']) {
            $where['status'] = $attr['status'];
        }

        try {
            $list = $this->field($field)->where($where)->order('id ASC')->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGameShareDomainListByAttr] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;
        return $ret;
    }

    /**
     * 插入域名配置
     * @author carter
     */
    public function insertShareDomainConf($gameId, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $insertData = array(
            'game_id' => $gameId,
            'link' => $attr['link'],
            'share_count' => 0,
            'is_blockade' => self::IS_BLOCKADE_FALSE,
            'status' => self::STATUS_NORMAL,
        );

        try {
            $id = $this->add($insertData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertShareDomainConf] insert failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_ADD_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = array_merge(array('id' => $id), $insertData);
        return $ret;
    }

    /**
     * 修改域名配置
     * @author carter
     */
    public function updateShareDomainConf($gameId, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try {
            $confInfo = $this->field('game_id,link,status')->where(array('id' => $attr['id']))->find();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateShareDomainConf] select failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if (empty($confInfo)) {
            $ret['code'] = ERRCODE_DB_DATA_EMPTY;
            $ret['msg'] = "域名配置{$attr['id']}数据不存在";
            return $ret;
        }

        // 游戏不匹配
        if ($confInfo['game_id'] != $gameId) {
            $ret['code'] = ERRCODE_DATA_ERR;
            $ret['msg'] = "域名配置{$attr['id']}对应的游戏id{$confInfo['game_id']}与用户当前选择游戏不匹配";
            return $ret;
        }

        $updateData = array(
            'status' => $attr['status'],
        );

        try {
            $this->where(array('id' => $attr['id']))->save($updateData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateShareDomainConf] update failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = array(
            'link' => $confInfo,
            'upData' => $updateData,
        );

        return $ret;
    }

    /**
     * 对指定域名配置分享次数加一
     * @author Carter
     */
    public function updateShareDomainCountInc($confId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        try {
            $this->where(array('id' => $confId))->setInc('share_count');
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateShareDomainCountInc] update failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        return $ret;
    }

    /**
     * 删除域名配置
     * @author carter
     */
    public function deleteShareDomainConf($id)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try {
            $confInfo = $this->field('is_blockade,status')->where(array('id' => $id))->find();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[deleteShareDomainConf] select failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if (empty($confInfo)) {
            $ret['code'] = ERRCODE_DB_DATA_EMPTY;
            $ret['msg'] = "域名配置{$id}数据不存在";
            return $ret;
        }

        try {
            $this->where(array('id' => $id))->delete();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[deleteShareDomainConf] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_DELETE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $confInfo;

        return $ret;
    }
}
