<?php
namespace Home\Model;

use Think\Model;
use Common\Service\ApiService;

class GameShareModel extends Model
{
    // 分享功能
    const SOURCE_HALL_NOAWARD = 1; // 大厅分享（无奖励，竖版独有）朋友圈
    const SOURCE_HALL_AWARD = 2; // 大厅分享（有奖励）朋友圈
    const SOURCE_DIAMOND = 3; // 领取钻石-朋友圈分享
    const SOURCE_CLUB = 4; // 俱乐部-朋友圈分享
    const SOURCE_ROOM = 5; // 房间等待界面-邀请好友（区分玩法）
    const SOURCE_FRIEND_NOAWARD = 6; // 大厅分享给好友（无奖励）
    const SOURCE_FRIEND_AWARD = 7; // 领取钻石-分享给好友
    const SOURCE_FRIEND_CLUB = 8; // 俱乐部分享给好友
    public $sourceMap = array(
        self::SOURCE_HALL_NOAWARD => '大厅分享（无奖励，竖版独有）朋友圈  ',
        self::SOURCE_HALL_AWARD => '大厅分享（有奖励）朋友圈',
        self::SOURCE_DIAMOND => '领取钻石-朋友圈分享',
        self::SOURCE_CLUB => '俱乐部-朋友圈分享',
        self::SOURCE_ROOM => '房间等待界面-邀请好友（区分玩法）',
        self::SOURCE_FRIEND_NOAWARD => '大厅分享给好友（无奖励）',
        self::SOURCE_FRIEND_AWARD => '领取钻石-分享给好友',
        self::SOURCE_FRIEND_CLUB => '俱乐部分享给好友',
    );

    // 分享方式
    const SHARE_TYPE_DYMC = 1; // 动态链接分享
    const SHARE_TYPE_SYS = 2; // 系统分享图片
    const SHARE_TYPE_APPID = 3; // 动态appid图片分享
    const SHARE_TYPE_TEXT = 4; // 系统分享纯文本
    public $shareTypeMap = array(
        self::SHARE_TYPE_DYMC => '动态链接分享',
        self::SHARE_TYPE_SYS => '系统分享图片',
        self::SHARE_TYPE_APPID => '动态appid图片分享',
        self::SHARE_TYPE_TEXT => '系统分享纯文本',
    );

    /**
     * 根据游戏配置id获取配置信息
     * @author Carter
     */
    public function queryGameShareById($confId, $field = '*')
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
        try {
            $info = $this->field($field)->where(array('id' => $confId))->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGameShareById] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if (empty($info)) {
            $ret['code'] = ERRCODE_DB_DATA_EMPTY;
            $ret['msg'] = "{$confId}分享配置不存在";
            return $ret;
        }
        $ret['data'] = $info;
        return $ret;
    }

    /**
     * 根据条件获取游戏分享配置列表
     * @author Carter
     */
    public function queryGameShareByAttr($attr, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $where = array();
        if ($attr['gameId']) {
            if (is_array($attr['gameId'])) {
                $where['game_id'] = array('in', $attr['gameId']);
            } else {
                $where['game_id'] = $attr['gameId'];
            }
        }
        if ($attr['place_id']) {
            $where['place_id'] = $attr['place_id'];
        }
        if ($attr['play_id']) {
            $where['play_id'] = $attr['play_id'];
        }
        if ($attr['source']) {
            if (is_array($attr['source'])) {
                $where['source'] = array('in', $attr['source']);
            } else {
                $where['source'] = $attr['source'];
            }
        }
        if ($attr['shareType']) {
            $where['share_type'] = $attr['shareType'];
        }

        try {
            $list = $this->field($field)->where($where)->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGameShareByAttr] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;
        return $ret;
    }

    /**
     * 插入分享配置
     * @author carter
     */
    public function insertGameShareConf($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $apiSer = new ApiService();

        // 不同分享功能对地区和玩法的要求不同
        switch ($attr['source']) {
            case self::SOURCE_HALL_NOAWARD: // 大厅分享（无奖励，竖版独有）朋友圈
            case self::SOURCE_HALL_AWARD: // 大厅分享（有奖励）朋友圈
            case self::SOURCE_DIAMOND: // 领取钻石-朋友圈分享
            case self::SOURCE_CLUB: // 俱乐部-朋友圈分享
            case self::SOURCE_FRIEND_NOAWARD: // 大厅分享给好友（无奖励）
            case self::SOURCE_FRIEND_AWARD: // 领取钻石-分享给好友
            case self::SOURCE_FRIEND_CLUB: // 俱乐部分享给好友
                $placeId = $attr['place_id'];
                $playId = 0;
                break;
            // 房间等待界面-邀请好友（区分玩法）
            case self::SOURCE_ROOM:
                $placeId = 0;
                $playId = $attr['play_id'];
                break;
            // 无效值
            default:
                $ret['code'] = ERRCODE_DATA_ERR;
                $ret['msg'] = "未知分享功能：{$attr['source']}";
                return $ret;
        }

        // 根据不同的分享功能，同一地区或玩法只能存在一条记录
        if ($placeId || $playId) {
            try {
                $where = array(
                    'game_id' => $attr['first_id'],
                    'source' => $attr['source'],
                );
                if ($placeId) {
                    $where['place_id'] = $placeId;
                }
                if ($playId) {
                    $where['play_id'] = $playId;
                }
                $info = $this->field('id')->where($where)->find();
            } catch(\Exception $e) {
                set_exception(__FILE__, __LINE__, "[insertGameShareConf] select failed: ".$e->getMessage());
                $ret['code'] = ERRCODE_DB_SELECT_ERR;
                $ret['msg'] = $e->getMessage();
                return $ret;
            }
            if (!empty($info)) {
                $ret['code'] = ERRCODE_DATA_OVERLAP;
                $ret['msg'] = '同一分享功能的配置已存在，不能重复插入';
                return $ret;
            }
        }

        // 上传缩略图或背景图到资源服
        if (!empty($attr['savename'])) {
            $imgSource = ROOT_PATH."FileUpload/ShareImg/".$attr['savename'];
            if (!file_exists($imgSource)) {
                $ret['code'] = ERRCODE_DATA_ERR;
                $ret['msg'] = '图片资源不存在，请重新上传';
                return $ret;
            }
            $svrPath = "Admin/GameShare/Image/".$attr['savename'];
            $serRet = $apiSer->resourceServerUploadImg($svrPath, $imgSource);
            if (ERRCODE_SUCCESS !== $serRet['code']) {
                $ret['code'] = $serRet['code'];
                $ret['msg'] = $serRet['msg'];
                return $ret;
            }
            $dataImage = $svrPath;
        } else {
            $dataImage = '';
        }

        // 动态分享
        if (self::SHARE_TYPE_DYMC == $attr['share_type']) {
            $dataTitle = $attr['title'];
            $dataDesc = $attr['desc'];
            $dataAddress = '';
            $dataQrcodeX = 0;
            $dataQrcodeY = 0;
        }
        // 系统分享
        else if (self::SHARE_TYPE_SYS == $attr['share_type']) {
            $dataTitle = '';
            $dataDesc = $attr['cont'];
            $dataAddress = $attr['address'];
            $dataQrcodeX = intval($attr['qrcode_x'] * 1000);
            $dataQrcodeY = intval($attr['qrcode_y'] * 1000);
        }

        $insertData = array(
            'game_id' => $attr['first_id'],
            'place_id' => $placeId,
            'play_id' => $playId,
            'source' => $attr['source'],
            'share_type' => $attr['share_type'],
            'title' => $dataTitle,
            'desc' => $dataDesc,
            'image' => $dataImage,
            'address' => $dataAddress,
            'qrcode_x' => $dataQrcodeX,
            'qrcode_y' => $dataQrcodeY,
        );

        try {
            $id = $this->add($insertData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertGameShareConf] insert failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_ADD_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = array_merge(array('id' => $id), $insertData);
        return $ret;
    }

    /**
     * 修改分享配置
     * @author carter
     */
    public function updateGameShareConf($id, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $apiSer = new ApiService();

        try {
            $confInfo = $this->where(array('id' => $id))->find();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateGameShareConf] select failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if (empty($confInfo)) {
            $ret['code'] = ERRCODE_DB_DATA_EMPTY;
            $ret['msg'] = "分享配置{$id}数据不存在";
            return $ret;
        }

        // 分享功能不匹配
        if ($confInfo['source'] != $attr['source']) {
            $ret['code'] = ERRCODE_DATA_ERR;
            $ret['msg'] = "分享配置{$id}对应的分享功能{$confInfo['source']}与参数分享功能{$attr['source']}不匹配";
            return $ret;
        }

        // 缩略图有更新，上传完新图片后需要删除资源服上旧图片
        if ("" != $attr['savename']) {
            $imgSource = ROOT_PATH."FileUpload/ShareImg/".$attr['savename'];
            if (empty($attr['savename']) || !file_exists($imgSource)) {
                $ret['code'] = ERRCODE_DATA_ERR;
                $ret['msg'] = '图片资源不存在，请重新上传';
                return $ret;
            }
            $svrPath = "Admin/GameShare/Image/".$attr['savename'];
            $serRet = $apiSer->resourceServerUploadImg($svrPath, $imgSource);
            if (ERRCODE_SUCCESS !== $serRet['code']) {
                $ret['code'] = $serRet['code'];
                $ret['msg'] = $serRet['msg'];
                return $ret;
            }
            $attr['image'] = $svrPath;

            // 上传成功后再删除旧图片，避免新图未上传，老图已删除的情况发生
            if ('' != $confInfo['image']) {
                $serRet = $apiSer->resourceServerDeleteImg($confInfo['image']);
                if (ERRCODE_SUCCESS !== $serRet['code']) {
                    $ret['code'] = $serRet['code'];
                    $ret['msg'] = $serRet['msg'];
                    return $ret;
                }
            }
        } else {
            // 房间分享允许不传缩略图
            if (self::SOURCE_ROOM == $confInfo['source'] && '' != $confInfo['image']) {
                $attr['image'] = '';
                $serRet = $apiSer->resourceServerDeleteImg($confInfo['image']);
                if (ERRCODE_SUCCESS !== $serRet['code']) {
                    $ret['code'] = $serRet['code'];
                    $ret['msg'] = $serRet['msg'];
                    return $ret;
                }
            }
        }

        // 动态分享
        if (self::SHARE_TYPE_DYMC == $attr['share_type']) {
            $attr['address'] = '';
            $attr['qrcode_x'] = 0;
            $attr['qrcode_y'] = 0;
        }
        // 系统分享
        else if (self::SHARE_TYPE_SYS == $attr['share_type']) {
            $attr['title'] = '';
            $attr['desc'] = $attr['cont'];
            $attr['qrcode_x'] = intval($attr['qrcode_x'] * 1000);
            $attr['qrcode_y'] = intval($attr['qrcode_y'] * 1000);
        }

        // 先过滤出拥有相同 key 的数组，再获取 value 不同的列
        $intersectArr = array_intersect_key($attr, $confInfo);
        $updateData = array_diff_assoc($intersectArr, $confInfo);
        if ($updateData == array()) {
            $ret['code'] = ERRCODE_UPDATE_NONE;
            $ret['msg'] = '无任何修改';
            return $ret;
        }

        try {
            $this->where(array('id' => $id))->save($updateData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateGameShareConf] update failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        // 将修改内容返回记录
        $ret['data'] = $updateData;

        return $ret;
    }

    /**
     * 根据地方id删除分享配置，并且返回删除前是系统分享的id
     * @author Carter
     */
    public function deleteGameShareByPlaceId($gameId, $placeId, $source)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $apiSer = new ApiService();

        try {
            $filed = 'id,share_type,image';
            $where = array(
                'game_id' => $gameId,
                'place_id' => $placeId,
                'source' => array('in', $source),
            );
            $list = $this->field($filed)->where($where)->select();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[deleteGameShareByPlaceId] select failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if (empty($list)) {
            $ret['code'] = ERRCODE_DB_DATA_EMPTY;
            $ret['msg'] = "分享配置{$placeId}数据不存在";
            return $ret;
        }

        // 删除数据库记录
        try {
            $this->where($where)->delete();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[deleteGameShareByPlaceId] delete failed, ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_DELETE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $delId = array();
        $sysTypeId = array();
        foreach ($list as $v) {
            $delId[] = $v['id'];

            // 整理出系统分享的id数组，用来删除二维码背景图
            if (self::SHARE_TYPE_SYS == $v['share_type']) {
                $sysTypeId[] = $v['id'];
            }

            // 删除资源服上的图片
            if (!empty($v['image'])) {
                $serRet = $apiSer->resourceServerDeleteImg($v['image']);
                if (ERRCODE_SUCCESS !== $serRet['code']) {
                    $ret['code'] = $serRet['code'];
                    $ret['msg'] = $serRet['msg'];
                    return $ret;
                }
            }
        }

        $ret['data'] = array(
            'delId' => $delId,
            'sysTypeId' => $sysTypeId,
        );

        return $ret;
    }
}
