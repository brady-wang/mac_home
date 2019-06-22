<?php
namespace Home\Model;

use Think\Model;

class StatGameitemConsumeModel extends Model
{
    /**
     * 分页获得数据列表
     * @param int $gameId 游戏ID
     * @param array $param 查询条件
     * @param int $limit 默认显示数据
     * @return array|boole 返回单条数据或false
     * @author tangjie <tj_youxiang_2008@163.com>
     * 
     */
    
    public function getGameStatDiamondConsumeDataList($gameId = '',$param = array() ,$limit = 20){
        
       
        $where = array(
            'game_id' => $gameId
        );
        
        $where = array_merge($where, (array) $param);
        
        $page = (int)I('get.p');
        if($page < 1){
            $page = 1 ;
        }
        
        $data = $this->where($where)->limit($limit)->page($page)->order(' data_time DESC')->select(); 
        return $data;
    }
    
    /**
     * 得到钻石消耗的图形数据
     * @author liyao
     */
    public function queryGameitemConsumeChartData($where, $chartMap, $stime, $etime)
    {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );

        try {
            if ($stime && $etime) {
                $list = $this->where($where)->order('data_time DESC')->select();
                $list = array_reverse($list);
            } else if ($stime) {
                $list = $this->where($where)->order('data_time ASC')->limit(7)->select();
            } else {
                $list = $this->where($where)->order('data_time DESC')->limit(7)->select();
                $list = array_reverse($list);
            }
            $chartData = array();
            foreach ($chartMap as $k => $v) {
                $da = array();
                $xVal = array();
                for ($i = 0; $i < count($list); $i++) {
                    $xVal[] = date("Y-m-d", $list[$i]['data_time']);
                    $da[] = $list[$i][$k];
                }
                $chartData[] = array('name'=>$v, 'key'=>$k, 'xAxis'=>$xVal, 'data'=>$da);
            }
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[queryGameitemConsumeChartData] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }

        $ret['data']['list'] = $chartData;
        return $ret;
    }
    
    /**
     * 获取钻石消耗全部数据
     * @author liyao
     */
    public function getGameitemConsumeAllData($where) {
        $ret = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => "",
            'data' => array()
        );
                  
        try {
            $data = $this->where($where)->order('data_time DESC')->select(); 
        } catch(\Exception $e) {
            set_exception(__FILE__, __LINE__, "[getGameitemConsumeAllData] select failed: ".$e->getMessage());
            $ret['code'] = ERRCODE_DB_SELECT_ERR;
            $ret['msg'] = $e->getMessage();
            return $ret;
        }
        $ret['data']['list'] = $data;
        return $ret;
    }
    
    /**
     * 获得数据表的日期总天数
     * @param array $where 查询条件
     * @param int $limit 默认显示数据
     * @return array|boole 返回单条数据或false
     * @author tangjie <tj_youxiang_2008@163.com>
     * 
     */
    
    public function getGameStatDiamondConsumeCount($where = array()){
        
        $data = $this->where($where)->count(); 
        return $data;
    }
    
    
    /**
     * 判断数据是否存在
     * @param int $dateTime 默认显示数据
     * @return array|boole 返回单条数据或false
     * @author tangjie <tj_youxiang_2008@163.com>
     */
    public function getDateTimeIsExtsts($gameId,$dateTime){
        if( empty( $dateTime) || empty($gameId) ){
            return false ;
        }
        $map = array(
            'game_id' => $gameId,
            'data_time' => $dateTime
        );
        $info = $this->where($map)->find();
        return $info;
    }
    
    /**
     * 插入一条基础数据
     * @param int $date 默认显示数据
     * @return array|boole 返回单条数据或false
     * @author tangjie <tj_youxiang_2008@163.com>
     */
    public function addEmptyData($data){
        if( empty( $data) ){
            return false ;
        }
        
        $info = $this->add($data);
        return $info;
    }
    
    
    /**
     * 插入一条基础数据
     * @param int $date 默认显示数据
     * @return array|boole 返回单条数据或false
     * @author tangjie <tj_youxiang_2008@163.com>
     */
    public function updateStatData($id,$data){
        if( empty( $data) || empty($id)){
            return false ;
        }
        $data['update_time'] = time();
        $where= array(
            'id' => (int) $id
        );
        $info = $this->where($where)->save($data);
        return $info;
    }
    
    
    
}
