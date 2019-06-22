<?php
namespace Home\Model;

use Think\Model;

class GameModel extends Model
{
    const GAME_STATUS_ON = 1; // 上线
    const GAME_STATUS_OFF = 2; // 下架
    public $statusMap = array(
        self::GAME_STATUS_ON => array('name' => '上线', 'label' => 'label-success'),
        self::GAME_STATUS_OFF => array('name' => '下架', 'label' => 'label-danger'),
    );

    // 针对合服市包，将其相关信息映射到省包结构下
    public $localMap = array(
        // 河南麻将全集
        4156 => array(
            // 商丘
            10070 => array(
                'pkName' => 'com.dashengzhangyou.pykf.henanshangqiu',
                'gName' => '商丘',
            ),
            // 焦作
            10092 => array(
                'pkName' => 'com.dashengzhangyou.pykf.jiaozuo',
                'gName' => '焦作',
            ),
            // 河南新包，为了防止重gameId，相同游戏不同包名前面加9
            94156 => array(
                'pkName' => 'com.dszy.gdmj.henan',
                'gName' => '河南麻将全集 com.dszy.gdmj.henan',
            ),
        ),
        // 江苏麻将大全
        3422 => array(
            // 徐州
            10007 => array(
                'pkName' => 'com.dashengzhangyou.pykf.xuzhou',
                'gName' => '徐州',
            ),
            // 常州
            10008 => array(
                'pkName' => 'com.dashengzhangyou.pykf.changzhou',
                'gName' => '常州',
            ),
            // 无锡
            10039 => array(
                'pkName' => 'com.dashengzhangyou.pykf.wuxi',
                'gName' => '无锡',
            ),
            // 宿迁
            10040 => array(
                'pkName' => 'com.dashengzhangyou.pykf.suqian',
                'gName' => '宿迁',
            ),
            // 江苏新包，为了防止重gameId，相同游戏不同包名前面加9
            93422 => array(
                'pkName' => 'com.dszy.gdmj.jiangsu',
                'gName' => '江苏麻将大全 com.dszy.gdmj.jiangsu',
            ),
        ),
        // 安徽麻将大全
        5537 => array(
            // 淮南
            10021 => array(
                'pkName' => 'com.dashengzhangyou.pykf.huainan',
                'gName' => '淮南',
            ),
            // 蚌埠
            10022 => array(
                'pkName' => 'com.dashengzhangyou.pykf.bengbu',
                'gName' => '蚌埠',
            ),
            // 宿州
            10023 => array(
                'pkName' => 'com.dashengzhangyou.pykf.suzhou',
                'gName' => '宿州',
            ),
            // 安徽大全新包，为了防止重gameId，相同游戏不同包名前面加9
            95537 => array(
                'pkName' => 'com.dszy.gdmj.anhuidq',
                'gName' => '安徽麻将大全 com.dszy.gdmj.anhuidq',
            ),
        ),
        // 安徽麻将全集
        5538 => array(
            // 阜阳
            10030 => array(
                'pkName' => 'com.dashengzhangyou.pykf.fuyang',
                'gName' => '阜阳',
            ),
            // 安徽全集新包，为了防止重gameId，相同游戏不同包名前面加9
            95538 => array(
                'pkName' => 'com.dszy.gdmj.anhuiquanji',
                'gName' => '安徽麻将全集 com.dszy.gdmj.anhuiquanji',
            ),
        ),
        // 安徽麻将精华版
        5539 => array(
            // 安庆
            10019 => array(
                'pkName' => 'com.dashengzhangyou.pykf.anqing',
                'gName' => '安庆',
            ),
            // 安徽精华新包，为了防止重gameId，相同游戏不同包名前面加9
            95539 => array(
                'pkName' => 'com.dszy.gdmj.anhuijinghua',
                'gName' => '安徽精华版 com.dszy.gdmj.anhuijinghua',
            ),
        ),
        // 广东
        4444 => array(
            // 广东新包，为了防止重gameId，相同游戏不同包名前面加9
            94444 => array(
                'pkName' => 'com.dszy.gdmj.guangdong',
                'gName' => '广东麻将大全 com.dszy.gdmj.guangdong',
            ),
        ),
        // 新淮北
        5541 => array(
            // 新淮北新包，为了防止重gameId，相同游戏不同包名前面加9
            95541 => array(
                'pkName' => 'com.dszy.gdmj.newhuaibei',
                'gName' => '新淮北 com.dszy.gdmj.newhuaibei',
            ),
        ),
    );

    /**
     * 获得数据列表
     * @author Carter
     */
    public function queryGameAllList($attr, $field)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        $where = array();
        if ($attr['game_status']) {
            $where['game_status'] = $attr['game_status'];
        }

        try {
            $data = $this->field($field)->where($where)->order('id ASC')->select();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGameAllList] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $data;
        return $ret;
    }

    /**
     * 获得游戏单条信息
     * @author Carter
     */
    public function queryGameInfoById($gameId, $field = '*')
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        try {
            $info = $this->field($field)->where(array('game_id' => $gameId))->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGameInfoById] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if (empty($info)) {
            $ret['code'] = ERRCODE_DB_DATA_EMPTY;
            $ret['msg'] = "游戏 {$gameId} 数据不存在";
            return $ret;
        }
        $ret['data'] = $info;

        return $ret;
    }

    /**
     * 插入数据
     * @author liyao
     */
    public function insertGameData($attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        $insertData = array(
            'game_id' => $attr['game_id'],
            'game_name' => $attr['game_name'],
            'ios_package_name' => $attr['ios_package_name'],
            'android_package_name' => $attr['android_package_name'],
            'api_ip' => $attr['api_ip'],
            'api_port' => $attr['api_port'],
            'activity_api' => $attr['activity_api'],
            'activity_api_port' => $attr['activity_api_port'],
            'resource_ip' => $attr['resource_ip'],
            'resource_port' => $attr['resource_port'],
            'game_status' => $attr['game_status'],
        );

        try {
            $id = $this->add($insertData);
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[insertGameData] insert failed: ".$e->getMessage());
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
     * 修改数据
     * @author liyao
     */
    public function updateGameData($id, $attr)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array(),
        );

        try {
            $confInfo = $this->where(array('id' => $id))->find();
        } catch (\Exception $e) {
            set_exception(__FILE__, __LINE__, "[updateGameData] select failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        if (empty($confInfo)) {
            $ret['code'] = ERRCODE_DB_DATA_EMPTY;
            $ret['msg'] = "游戏配置{$id}数据不存在";
            return $ret;
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
            set_exception(__FILE__, __LINE__, "[updateGameData] update failed. ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_UPDATE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        // 将修改内容返回记录
        $ret['data'] = $updateData;

        return $ret;
    }

    /**
     * 删除游戏
     * @author carter
     */
    public function deleteGameByGameId($gameId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        try {
            $this->where(array('game_id' => $gameId))->delete();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[deleteGameByGameId] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_DELETE_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        return $ret;
    }

    /**
     * 检测游戏ID是否唯一
     * @author liyao
     */
    public function execCheckGameId($id, $gameId)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
        );

        try {
            $where = array('game_id' => $gameId);
            if ($id) {
                $where['id'] = array('neq', $id);
            }
            $check = $this->where($where)->find();
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[execCheckGameId] ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data'] = $check;

        return $ret;
    }
}
