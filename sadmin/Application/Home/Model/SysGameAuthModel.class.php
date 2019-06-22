<?php
namespace Home\Model;

use Think\Model;

class SysGameAuthModel extends Model
{
    /**
     * 通过参数获取角色列表
     * @author tangjie
     */
    public function querySysGameAuthListByAttr($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array();
        if ($attr['role_id']) {
            if (is_array($attr['role_id'])) {
                $where['role_id'] = array('in', $attr['role_id']);
            } else {
                $where['role_id'] = $attr['role_id'];
            }
        }

        try {
            $field = "id,role_id,game_id";
            $list = $this->field($field)->where($where)->order("id DESC")->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[querySysGameAuthListByAttr] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $list;
        return $ret;
    }

    /**
     * 获取权限组游戏授权
     */
    public function getRoleGameAuthAllList()
    {
        $result = $this->join( ' AS auth RIGHT JOIN '.C('DB_PREFIX').'sys_role AS role ON role.id = auth.role_id ' )->field('role.id , role_id,game_id,role_name,sort')->order(' role.sort ASC , role.sort DESC  ')->select();
        $dataList = array();
        foreach ($result as $item ){
            $info =& $dataList[ $item['id'] ];
            if(empty($info)){
                $info = array();
            }
            $info['id']  = $item['id'];
            $info['role_id']  = $item['role_id'];
            $info['role_name']  = $item['role_name'];
            $info['sort']  = $item['sort'];
            $info['gamelist'][]  = array(
                'game_id' => $item['game_id']
            );
        }
        unset($result);

        return $dataList;
    }

    /**
     * 新增授权
     * @author tangjie
     */
    public function addGameAuth($roleId,$gameIds)
    {
        $result = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        if( empty($roleId) || empty($gameIds) ){
            $result['code'] = ERRCODE_PARAM_NULL;
            $result['msg'] = '权限组或游戏ID参数错误';
        }

        foreach ($gameIds as $gameId){
            $insert[] = array(
                'role_id' => $roleId,
                'game_id' => $gameId,
                'create_time' => time()
            );
        }

        try {

            $addreeult = $this->addAll($insert);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[addGameAuth] ".$e->getMessage());
            $result['code'] = ERRCODE_DB_SELECT_ERR;
            $result['msg'] = $e->getMessage();
            return $result;
        }

        $result['data'] = $addreeult;
        return $result;
    }

    /**
     * 新增授权
     * @author tangjie
     */
    public function deleteGameAuth($roleId,$gameIds)
    {
        $result = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        if( empty($roleId) || empty($gameIds) ){
            $result['code'] = ERRCODE_PARAM_NULL;
            $result['msg'] = '权限组或游戏ID参数错误';
        }

        $where = array(
            'role_id'=>$roleId,
            'game_id' => array('in',$gameIds)
        );

        try {

            $addreeult = $this->where($where) ->delete();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[addGameAuth] ".$e->getMessage());
            $result['code'] = ERRCODE_DB_SELECT_ERR;
            $result['msg'] = $e->getMessage();
            return $result;
        }

        $result['data'] = $addreeult;
        return $result;
    }

    /**
     * 更新权限操作
     * @param int $roleid 权限组ID
     * @param array $gameids 游戏IDs
     * @return array 返回操作的提示，代码
     * @author tangjie
     */
    public function updateRodeGameAuth($roleid, $gameids)
    {
        $retData = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "操作完成",
        );

        if ($roleid < 1 || empty($gameids)) {
            $retData['code'] = ERRCODE_PARAM_NULL ; //参数为空或者参数错误
            $retData['msg'] = '参数为空或者参数错误';
            return $retData ;
        }

        //获得游戏列表
        $authWhere = array(
            'role_id' => $roleid
        );
        $roleAuth = $this->querySysGameAuthListByAttr($authWhere);
        $authIds = array();
        if($roleAuth['data']){
            $authIds = array_column($roleAuth['data'],'game_id' );
        }

        //新增授权
        $addDiff = array_diff((array)$gameids, (array) $authIds); //游戏ID的差异,得到要新增到数据库的ID。
        //写入到数据库
        if($addDiff){
            $result = D('SysGameAuth')->addGameAuth($roleid,$addDiff);
            if($result['code'] != ERRCODE_SUCCESS  ){
                $retData['code'] = ERRCODE_PARAM_NULL ; //参数为空或者参数错误
                $retData['msg'] = '新增授权失败';
                return $retData ;
            }

        }

        //删除已取消的授权
        $deleteDiff = array_diff((array) $authIds,(array)$gameids ); //游戏ID的差异,得到要删除数据库的游戏ID。
        //写入到数据库
        if($deleteDiff){
            $result = D('SysGameAuth')->deleteGameAuth($roleid,$deleteDiff);
            if($result['code'] != ERRCODE_SUCCESS  ){
                $retData['code'] = ERRCODE_PARAM_NULL ; //参数为空或者参数错误
                $retData['msg'] = '更新授权失败';
                return $retData ;
            }
        }

        return $retData ;
    }
}
