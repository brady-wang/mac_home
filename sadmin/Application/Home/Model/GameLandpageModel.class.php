<?php
namespace Home\Model;
use Common\Service\ApiService;

use Think\Model;

class GameLandpageModel extends Model {
    const PIC_PATH = "SA/LandingPage/";
    const TEMP_PIC_PATH = "FileUpload/Landpage";
    /**
     * 得到落地页配置
     * @author liyao
     */
    public function getGameData($attr, $field = '*')
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $where = [];
        if (is_array($attr)) {
            foreach ($attr as $key => $value) {
                if (is_array($attr[$key])) {
                    $where[$key] = ['in', $value];
                } else {
                    $where[$key] = $value;
                }
            }
        } elseif (is_string($attr)) {
            $where = $attr;
        }

        try {
            $list = $this->field($field)->where($where)->select();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[getGameData] select failed: " . $e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;

        }
        $ret['data'] = $list;
        return $ret;
    }
    
    /**
     * 保存落地页配置
     */
    public function saveGameData($data) {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );
        $delFlag = false;
        $apiSer = new ApiService();
        $imgName = '';
        // 上传图片
        if (!empty($data['image_path'])) {
            $imgPath = $data['image_path'];
            $picPath = self::PIC_PATH.C('G_USER.gameid').'/';
            if (strpos($imgPath, $picPath) !== false) {
                $imgName = substr($imgPath, strpos($imgPath, $picPath)+strlen($picPath));
            } else {
                $imgName = str_replace("/".self::TEMP_PIC_PATH."/",'', $imgPath);
                $imgSource = ROOT_PATH.self::TEMP_PIC_PATH."/".$imgName;
                if (empty($imgName) || !file_exists($imgSource)) {
                    $ret['code'] = ERRCODE_DATA_ERR;
                    $ret['msg'] = '图片资源不存在，请重新上传';
                    return $ret;
                }
                $svrPath = $picPath.$imgName;
                $serRet = $apiSer->resourceServerUploadImg($svrPath, $imgSource);
                if (ERRCODE_SUCCESS !== $serRet['code']) {
                    $ret['code'] = $serRet['code'];
                    $ret['msg'] = $serRet['msg'];
                    return $ret;
                }
                $delFlag = true;
            }
        }
        
        $updateData = array();
        if (!empty($data['title']))
            $updateData['title'] = $data['title'];
        if (!empty($imgName))
            $updateData['image_name'] = $imgName;
        if (!empty($data['ios_downlink']))
            $updateData['ios_downlink'] = $data['ios_downlink'];
        if (!empty($data['and_downlink']))
            $updateData['android_downlink'] = $data['and_downlink'];
        if (!empty($data['placeId']))
            $placeId = $data['placeId'];
        try {
            $info = $this->where(array('game_id' => C('G_USER.gameid'), 'place_id' => $placeId))->find();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[saveGameData] select failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if ($info) {
            $intersectArr = array_intersect_key($updateData, $info);
            $updateData = array_diff_assoc($intersectArr, $info);
            if (count($updateData) == 0) {
                $ret['code'] = ERRCODE_UPDATE_NONE;
                $ret['msg'] = '无任何修改';
                return $ret;
            }
        }
        $oldImgName = "";
        try {
            if ($info) {
                $oldImgName = $info['image_name'];
                $this->where(array('game_id' => C('G_USER.gameid'), 'place_id' => $placeId))->save($updateData);
            } else {
                $updateData['game_id'] = C('G_USER.gameid');
                $updateData['place_id'] = $placeId;
                $id = $this->add($updateData);
            }
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[saveGameData] select failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        //上传落地页配置
        $fname = 'landpage.json';
        $fpath = TEMP_PATH.$fname;
        try {
	    $list = $this->select();
	} catch (Exception $e) {
            set_exception(__FILE__, __LINE__, "[saveGameData] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;

	}
        foreach ($list as $k=>$v) {
            $list[$k]['image_path'] = C('RESOURCE_SERVER_IPHOST').':'.C('RESOURCE_SERVER_PORT').'/'.self::PIC_PATH.$v['game_id'].'/';
        }
        if (false === file_put_contents($fpath, serialize($list))) {
            $ret['code'] = ERRCODE_SYSTEM;
            $ret['msg'] = "写入落地页配置文件失败";
            return $ret;
        }
        $apiSer = new ApiService();
        $svrRet = $apiSer->landpageSendConfFile($fname, $fpath);
        if (ERRCODE_SUCCESS !== $svrRet['code']) {
            $ret['code'] = $svrRet['code'];
            $ret['msg'] = $svrRet['msg'];
            return $ret;
        }
        //删除不用图片
        if ($delFlag && !empty($oldImgName)) {
            $path = self::PIC_PATH.C('G_USER.gameid').'/'.$oldImgName;
            // 删除旧图片
            $serRet = $apiSer->resourceServerDeleteImg($path);
            if (ERRCODE_SUCCESS !== $serRet['code']) {
                $ret['code'] = $serRet['code'];
                $ret['msg'] = $serRet['msg'];
                return $ret;
            }            
        }
        $ret['data'] = $updateData;
        return $ret;
    }
}
