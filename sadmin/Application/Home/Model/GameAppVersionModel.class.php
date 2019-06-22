<?php
namespace Home\Model;

use Think\Model;
use Common\Service\ApiService;

class GameAppVersionModel extends Model
{
    // 更新方式
    const UPDATE_MODE_REPLACE = 1; // 强更
    const UPDATE_MODE_PACKS = 2; // 热更
    public $updateModeMap = array(
        self::UPDATE_MODE_REPLACE => array('name' => '强更', 'val' => 'replace'),
        self::UPDATE_MODE_PACKS => array('name' => '热更', 'val' => 'packs'),
    );

    // 状态
    const STATUS_PENDING = 10; // 待上线
    const STATUS_PUBLISHED = 50; // 已上线
    const STATUS_CANCEL = 99; // 已取消
    public $statusMap = array(
        self::STATUS_PENDING => array('name' => '待上线', 'label' => 'label-primary', 'text' => 'text-info'),
        self::STATUS_PUBLISHED => array('name' => '已上线', 'label' => 'label-success', 'text' => 'text-success'),
        self::STATUS_CANCEL => array('name' => '已取消', 'label' => 'label-default', 'text' => 'text-muted'),
    );

    public function __construct() {
        parent::__construct();
    }

    /**
     * 用于校验一个版本号是否符合命名规范，仅支持纯数字下点号分隔
     * @author Carter
     */
    private function _checkVersionValidity($ver)
    {
        $arr = explode('.', $ver);
        foreach ($arr as $v) {
            if (false === filter_var($v, FILTER_VALIDATE_INT) || $v < 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * 用于对比两个版本号的大小，若新版本号大于旧版本号，返回true，否则返回false
     * @author Carter
     */
    private function _compareVersion($newVer, $oldVer)
    {
        $nv = explode('.', $newVer);
        $ov = explode('.', $oldVer);
        for ($i = 0; $i < count($nv); $i++) {
            if ($nv[$i] > $ov[$i]) {
                return true;
            } else if ($nv[$i] < $ov[$i]) {
                return false;
            }
        }
        return false;
    }

    /**
     * 根据id获取版本信息
     * @author Carter
     */
    public function queryGameAppVersionById($id, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        try {
            $info = $this->field($field)->where(array('id' => $id))->find();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGameAppVersionById] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if (empty($info)) {
            $ret['code'] = ERRCODE_DB_DATA_EMPTY;
            $ret['msg'] = "不存在该版本信息{$id}";
            return $ret;
        }
        $ret['data'] = $info;

        return $ret;
    }

    /**
     * 根据条件查询版本列表，不分页
     * @author Carter
     */
    public function queryGameAllAppVersionByAttr($attr, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $where = array();
        if ($attr['game_id']) {
            $where['game_id'] = $attr['game_id'];
        }
        if ($attr['latest_flag']) {
            $where['latest_flag'] = $attr['latest_flag'];
        }

        try {
            $list = $this->field($field)->where($where)->select();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGameAllAppVersionByAttr] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $list;
        return $ret;
    }

    /**
     * 获取指定渠道的版本分页列表
     * @author Carter
     */
    public function queryGameAppVersionList($channelCode, $page)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        // 渠道版本每页展示 8 条
        $pageSize = 8;

        $where = array(
            'channel_code' => $channelCode,
        );

        try {
            // 获取总记录数，用来计算总共页数
            $count = $this->where($where)->count();

            $field = 'id,channel_code,update_mode,update_version,update_url,update_md5,remark,status,update_time';
            $list = $this->field($field)->where($where)->order('id DESC')->page("{$page},{$pageSize}")->select();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGameAppVersionList] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        // 数据处理
        foreach ($list as $k => $v) {
            $list[$k]['update_time'] = date('Y-m-d H:i:s', $v['update_time']);
        }

        $ret['data'] = array(
            'list' => $list,
            'totalPage' => ceil($count / $pageSize),
        );

        return $ret;
    }

    /**
     * 提交新版本，插入版本信息
     * @author Carter
     */
    public function insertGameAppVersion($gameId, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $apiSer = new ApiService();

        // 若存在待上线的版本，不得提新版
        $where = array(
            'game_id' => $gameId,
            'channel_code' => $attr['channel_code'],
            'status' => self::STATUS_PENDING,
        );
        try {
            $info = $this->field('id')->where($where)->find();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertGameAppVersion] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if (!empty($info)) {
            $ret['code'] = ERRCODE_PARAM_INVALID;
            $ret['msg'] = "该渠道已存在待上线版本，不能重复提交";
            return $ret;
        }

        // 检查版本号命名规范
        if (true !== $this->_checkVersionValidity($attr['update_version'])) {
            $ret['code'] = ERRCODE_PARAM_INVALID;
            $ret['msg'] = "游戏版本号不符合命名规范，仅能以数字命名，通过点号分隔";
            return $ret;
        }

        // 提交的版本号必须大于当前最新版本号
        $where = array(
            'game_id' => $gameId,
            'channel_code' => $attr['channel_code'],
            'latest_flag' => 1,
        );
        try {
            $lInfo = $this->field('id,update_version,status')->where($where)->find();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertGameAppVersion] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if ($lInfo) {
            if ($lInfo['status'] != self::STATUS_PUBLISHED) {
                // 只有已上线的版本允许置为最新版本
                set_exception(__FILE__, __LINE__, "[insertGameAppVersion] id {$lInfo['id']}, status {$lInfo['status']}");
            }
            if (true !== $this->_compareVersion($attr['update_version'], $lInfo['update_version'])) {
                $ret['code'] = ERRCODE_PARAM_INVALID;
                $ret['msg'] = "提交的游戏版本号必须大于当前最新版本号";
                return $ret;
            }
        }

        // 强更
        if (self::UPDATE_MODE_REPLACE == $attr['update_mode']) {
            // url 必须可访问
            if (true !== is_url_valid($attr['update_url'])) {
                $ret['code'] = ERRCODE_PARAM_INVALID;
                $ret['msg'] = "无效地址，{$attr['update_url']}不可访问";
                return $ret;
            }
            $updateUrl = $attr['update_url'];
            $updateMd5 = '';
        }
        // 热更
        else if (self::UPDATE_MODE_PACKS == $attr['update_mode']) {
            $fileSource = ROOT_PATH."FileUpload/VersionResource/".$attr['file_savename'];
            if (!file_exists($fileSource)) {
                $ret['code'] = ERRCODE_DATA_ERR;
                $ret['msg'] = '资源不存在，请重新上传';
                return $ret;
            }
            $svrPath = "VerSource/{$gameId}/{$attr['channel_code']}/{$attr['update_version']}/ResDir/";
            $serRet = $apiSer->resourceServerUpZip($svrPath, $fileSource);
            if (ERRCODE_SUCCESS !== $serRet['code']) {
                $ret['code'] = $serRet['code'];
                $ret['msg'] = $serRet['msg'];
                return $ret;
            }
            $updateUrl = $svrPath;
            $updateMd5 = md5_file($fileSource);
        }

        // 插入版本信息
        $insertData = array(
            'game_id' => $gameId,
            'latest_flag' => 0,
            'channel_code' => $attr['channel_code'],
            'update_mode' => $attr['update_mode'],
            'update_version' => $attr['update_version'],
            'update_url' => $updateUrl,
            'update_md5' => $updateMd5,
            'remark' => $attr['remark'],
            'status' => self::STATUS_PENDING,
            'update_time' => time(),
        );
        try {
            $id = $this->add($insertData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertGameAppVersion] insert failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_ADD_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = array_merge(array('id' => $id), $insertData);

        return $ret;
    }

    /**
     * 修改版本信息
     * @author carter
     */
    public function updateGameAppVersion($id, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $apiSer = new ApiService();

        try {
            $info = $this->where(array('id' => $id))->find();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateGameAppVersion] select failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if (empty($info)) {
            $ret['code'] = ERRCODE_DB_DATA_EMPTY;
            $ret['msg'] = "版本{$id}数据不存在";
            return $ret;
        }

        // 热更资源
        if (self::UPDATE_MODE_PACKS == $info['update_mode']) {
            unset($attr['update_url']);
            if (isset($attr['file_savename'])) {
                $fileSource = ROOT_PATH."FileUpload/VersionResource/".$attr['file_savename'];
                if (!file_exists($fileSource)) {
                    $ret['code'] = ERRCODE_DATA_ERR;
                    $ret['msg'] = '资源不存在，请重新上传';
                    return $ret;
                }
                $attr['update_md5'] = md5_file($fileSource);
            }
        }

        // 先过滤出拥有相同 key 的数组，再获取 value 不同的列
        $intersectArr = array_intersect_key($attr, $info);
        $updateData = array_diff_assoc($intersectArr, $info);
        if ($updateData == array()) {
            $ret['code'] = ERRCODE_UPDATE_NONE;
            $ret['msg'] = '无任何修改';
            return $ret;
        }

        if (isset($updateData['update_url'])) {
            // url 必须可访问
            if (true !== is_url_valid($updateData['update_url'])) {
                $ret['code'] = ERRCODE_PARAM_INVALID;
                $ret['msg'] = "无效强更地址，{$updateData['update_url']}不可访问";
                return $ret;
            }
        }

        if (isset($updateData['update_md5'])) {
            // 更新资源文件
            $svrPath = "VerSource/{$info['game_id']}/{$info['channel_code']}/{$info['update_version']}/ResDir/";
            $serRet = $apiSer->resourceServerUpZip($svrPath, $fileSource);
            if (ERRCODE_SUCCESS !== $serRet['code']) {
                $ret['code'] = $serRet['code'];
                $ret['msg'] = $serRet['msg'];
                return $ret;
            }
        }

        try {
            $this->where(array('id' => $id))->save($updateData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateGameAppVersion] update failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        // 将修改内容返回记录
        $ret['data'] = $updateData;

        return $ret;
    }

    /**
     * 进行发版操作
     * @author Carter
     */
    public function exceGameAppVersionPublish($gameId, $id)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try {
            $field = 'game_id,channel_code,status';
            $info = $this->field($field)->where(array('id' => $id))->find();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[exceGameAppVersionPublish] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        // id校验
        if (empty($info)) {
            $ret['code'] = ERRCODE_DATA_ERR;
            $ret['msg'] = "版本{$id}不存在，请勿私自拼接id";
            return $ret;
        }

        // 游戏校验
        if ($gameId != $info['game_id']) {
            $ret['code'] = ERRCODE_DATA_ERR;
            $ret['msg'] = '游戏不匹配，请勿私自拼接id';
            return $ret;
        }

        // 只有待上线的版本可以发布
        if (self::STATUS_PENDING != $info['status']) {
            $ret['code'] = ERRCODE_DATA_ERR;
            $ret['msg'] = "只有待上线的版本可以发布，当前状态为{$info['status']}";
            return $ret;
        }

        // 先将当前最新版本标志置0，因为发版后，本id即将成为最新的
        try {
            $updateWhere = array(
                'game_id' => $gameId,
                'latest_flag' => 1,
                'channel_code' => $info['channel_code'],
            );
            $this->where($updateWhere)->save(array('latest_flag' => 0));
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[exceGameAppVersionPublish] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        // 修改发版状态
        $updateData = array(
            'latest_flag' => 1,
            'status' => self::STATUS_PUBLISHED,
            'update_time' => time(),
        );
        try {
            $this->where(array('id' => $id))->save($updateData);
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[exceGameAppVersionPublish] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $updateData;

        return $ret;
    }

    /**
     * 取消版本，逻辑删除
     * @author Carter
     */
    public function exceGameAppVersionCancel($gameId, $id)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try {
            $field = 'game_id,status';
            $info = $this->field($field)->where(array('id' => $id))->find();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[exceGameAppVersionCancel] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        // id校验
        if (empty($info)) {
            $ret['code'] = ERRCODE_DATA_ERR;
            $ret['msg'] = "版本{$id}不存在，请勿私自拼接id";
            return $ret;
        }

        // 游戏校验
        if ($gameId != $info['game_id']) {
            $ret['code'] = ERRCODE_DATA_ERR;
            $ret['msg'] = '游戏不匹配，请勿私自拼接id';
            return $ret;
        }

        // 只有待上线的版本可以发布
        if (self::STATUS_PENDING != $info['status']) {
            $ret['code'] = ERRCODE_DATA_ERR;
            $ret['msg'] = "只有待上线的版本可以删除，当前状态为{$info['status']}";
            return $ret;
        }

        // 修改状态为已取消
        $updateData = array(
            'status' => self::STATUS_CANCEL,
            'update_time' => time(),
        );
        try {
            $this->where(array('id' => $id))->save($updateData);
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[exceGameAppVersionCancel] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data'] = $updateData;

        return $ret;
    }
}
