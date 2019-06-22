<?php
namespace Home\Model\DsqpDict;

use Think\Model;
use Common\Service\ApiService;

class PlaceGameModel extends Model
{
    /* 文件命名不符合规范，已经新建了 DictPlaceGameModel，本Model未来要删掉 */

    // 初始配置
    protected $connection = 'GAME_DICT_DB';
    protected $trueTableName = 'dict_place_game';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取玩法名称
     * @author liyao
     */
    public function getPlayNameByPlayId($playID) {
	$ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try{
            $field = "gameName";
            $where = array('gameId'=>$playID);
            $list = $this->field($field)->where($where)->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[getPlayNameByPlayId] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $list;
        return $ret;
    }
    /**
     * 查询游戏配置信息
     * @param int $id 查询ID
     * @return array|boole 返回单条数据或false
     * @author tangjie <tj_youxiang_2008@163.com>
     */
    public function getGameConfigByPlaceId($placeId,$gameid = 0)
    {
        if($placeId < 1 ){
            return false;
        }
        $where = array(
            'placeID' => (int)$placeId
        );
        if($gameid){
            $where['gameId'] = $gameid ;
        }
        $data = $this->where(  $where)->find();

        return $data;
    }

    /**
     * 查询游戏包的玩法配置列表
     * @param int $id 查询ID
     * @return array|boole 返回单条数据或false
     * @author tangjie <tj_youxiang_2008@163.com>
     */
    public function getGameListByGameId($placeId)
    {
        if($placeId < 1 ){
            return false;
        }
        $where = array(
            'p.firstID' => array('LIKE',"{$placeId}%")
        );

        // 不允许在model里面 join 别的表，这个方法要废掉 TODO

        $data = $this->join(' as game INNER JOIN dict_place as p ON game.placeID = p.placeID')->field('game.placeID,game.gameId,game.gameName,game.roomFee')->where( $where)->select();

        return $data;
    }

    /**
     * 更新数据
     * @param int $date 默认显示数据
     * @return array  返回单条数据状态
     * @author tangjie <tj_youxiang_2008@163.com>
     */
    public function updateGameConfigData($placeid, $data,$gid = 0)
    {
        if( empty( $data) || empty($placeid)){
            $result =  array(
                'code' => ERRCODE_PARAM_INVALID  ,
                'data' => array() ,
                'msg' => '数据为空或id参数为空'
            );
            return $result ;
        }

        $savedata['roomFee'] = $data;

        $where= array(
            'placeID' => (int) $placeid,
            'gameId'  => (int) $gid
        );

        $info = $this->where($where)->save($savedata);

        if ($info) {
            $result =  array(
                'code' => ERRCODE_SUCCESS  ,
                'data' => $info ,
                'msg' => '修改成功'
            );

            $this->updateApiCache();//更新游戏服务器缓存
        } else {
            $result =  array(
                'code' => ERRCODE_DB_UPDATE_ERR  ,
                'data' => '' ,
                'msg' => '修改失败'
            );
        }
        return $result;
    }

    // 更新服务器缓存
    // 不要在model里面调后端接口，哪个行为刷过接口很难看到，要移到Controller下面，这个方法要废掉 TODO
    // 调了api后一定要判断返回，不能就这么默默走下去
    private function updateApiCache()
    {
        $api = new ApiService();
        $api->kaifangApiQuery('/console/?act=reload');  //重载系统配置
        return $api->kaifangApiQuery('/console/?act=reloadPyjConfig');  //重载配置
    }
}
