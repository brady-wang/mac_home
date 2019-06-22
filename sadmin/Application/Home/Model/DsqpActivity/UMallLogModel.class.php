<?php
namespace Home\Model\DsqpActivity;

use Think\Model;

class UMallLogModel extends Model
{
    // 初始配置
    protected $connection = 'GAME_ACTIVITY_LOG_DB';
    protected $trueTableName = 'act_u_mall_log';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 分页查询数据列表
     * @param array $where 默认显示数据
     * @param int $limit 查询数据条数
     * @return array|boole 返回单条数据或false
     * @author tangjie <tj_youxiang_2008@163.com>
     */
    public function getLogListByPage($where = array() ,  $limit = 20)
    {
        $result = array(
            'code' => ERRCODE_SUCCESS,
            'msg' => ''
        );

        $page = (int)I('get.p');
        if($page < 1){
            $page = 1 ;
        }

        try {
            $data = $this->where( (array) $where)->limit($limit)->page($page)->order(' id DESC ')->select();

            $count      = $this->where($where)->count();// 查询满足要求的总记录数
            $Page       = new \Think\Page($count,$limit);// 实例化分页类 传入总记录数和每页显示的记录数
            $show       = $Page->show();// 分页显示输出

            $result['data'] = array(
                'data' =>$data,
                'pagehtml' => $show
            );
        } catch(\Exception $e) { //初始化数据库model失败
            $result['msg'] = "查询SQL错误：".$e->getMessage();
            $result['code'] = ERRCODE_DB_DATA_EMPTY ;
            return $result ;
        }

        return $result;
    }
}
