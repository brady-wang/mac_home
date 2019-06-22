<?php
namespace Home\Model;

use Think\Model;

class SysCacheModel extends Model
{
    /**
     * 取得临时缓存数据
     * @author Carter
     */
    public function querySysCacheByKey($gameId, $key)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $where = array(
            'game_id' => $gameId,
            'cache_name' => $key
        );

        try {
            $info = $this->field('id,cache_sting')->where($where)->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[querySysCacheByKey] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $info;

        return $ret;
    }

    /**
     * 设置缓存数据，不做cache删除，删除另取方法执行
     * @author Carter
     */
    public function exceSetSysCache($gameId, $key, $value ,$remark)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        // 校验参数
        if (empty($key) || empty($gameId)) {
            $ret['code'] = ERRCODE_PARAM_NULL ;
            $ret['msg'] = 'Key或游戏id不能为空';
            return $ret ;
        }

        try {
            // 检查key是否存在
            $where = array(
                "game_id" => $gameId,
                "cache_name" => $key
            );
            $info = $this->field('id')->where($where)->find();
            // 存在则更新
            if ($info) {
                $updateData = array(
                    'cache_sting' => $value,
                );
                $this->where(array('id' => $info['id']))->save($updateData);
            }
            // 不存在则插入
            else {
                $insertData = array(
                    'game_id' => $gameId,
                    'cache_name' => $key,
                    'cache_sting' => $value,
                    'remark' => $remark,
                );
                $this->data($insertData)->add();
            }
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[exceSetSysCache] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        return $ret;
    }
}
