<?php
namespace Home\Model\DsqpDict;

use Home\Model\GameModel;
use Think\Model;

class DictPlaceModel extends Model
{
    // 初始配置
    protected $connection = 'GAME_DICT_DB';
    protected $trueTableName = 'dict_place';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取指定游戏的地区列表
     * @author Carter
     */
    public function queryDsqpPlaceListByFirstId($id, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array();
        if (is_array($id)) {
            $where['firstID'] = array('in', $id);
        } else {
            $where['firstID'] = $id;
        }

        try{
            $list = $this->field($field)->where($where)->order('placeID ASC')->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryDsqpPlaceListByFirstId] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $list;
        return $ret;
    }

    /**
     * 获取指定游戏信息
     * @author Carter
     */
    public function queryDsqpPlaceByPlaceId($id, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $where = array();
        if (is_array($id)) {
            $where['placeID'] = array('in', $id);
        } else {
            $where['placeID'] = $id;
        }

        try{
            $list = $this->field($field)->where($where)->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryDsqpPlaceByPlaceId] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $list;
        return $ret;
    }

    /**
     * 获取地区列表
     * @author liyao
     */
    public function queryAreaList($firstId = "")
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        if (!isset($firstId) || empty($firstId)) {
            $firstId = C('G_USER.gameid');
        }
        if (is_array($firstId)) {
            $where['firstID'] = ['in', $firstId];
        } else {
            $where['firstID'] = $firstId;
        }

        try{
            $field = "placeID,placeName,placeLevel,parentPlaceID";
            $list = $this->field($field)->where($where)->order('placeID')->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryAreaList] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $list;
        return $ret;
    }

    /**
     * 取得路径
     * @author liyao
     */
    public function getPlacePath($place_id) {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $arr = array();
        try{
            $loop = 0;
            while (true) {
                $loop++;
                $field = "placeID,placeName,placeLevel,parentPlaceID";
                $where = array('placeID'=>$place_id);
                $info = $this->field($field)->where($where)->find();
                if ($info) {
                    $arr[] = $info['placeName'];
                    $place_id = $info['parentPlaceID'];
                    if ($info['placeLevel'] == 1)
                        break;
                    if ($place_id <= 0)
                        break;
                } else
                    break;
                if ($loop > 3)
                    break;
            }
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[getPlacePath] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $arr = array_reverse($arr);
        $ret['data'] = implode("-", $arr);
        return $ret;
    }

    /**
     * 判断此placeId是否为省包或合服市包
     * @author daniel
     */
    public function queryIsProvincePackage($placeId)
    {
        $ret = [
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => true
        ];
        if (empty($placeId) || !isset($placeId)) {
            $ret['code'] = ERRCODE_PARAM_NULL;
            $ret['msg'] = "placeId不能为空";
            return $ret;
        }
        $gameId = C("G_USER.gameid");
        if ($placeId == $gameId) {
            return $ret;
        }
        $modRet = new GameModel();
        $localMap = $modRet->localMap;
        if (!isset($localMap[$gameId])) {
            $ret['data'] = false;
            return $ret;
        }
        try {
            $field = "firstID, placeLevel";
            $where = ['placeID' => $placeId];
            $placeInfo = $this->field($field)->where($where)->find();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, '[' . __FUNCTION__ . ']' . $e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if (in_array($placeInfo['firstID'], array_keys($localMap[$gameId])) && $placeInfo['placeLevel'] == 1){
            return $ret;
        }
        $ret['data'] = false;
        return $ret;
    }
}
