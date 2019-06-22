<?php

/**
 * Created by PhpStorm.
 * User: brady
 * Date: 2018/10/18
 * Time: 10:09
 */
use JasonGrimes\Paginator;
class Index extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Tb_admin_user");
    }

    public function index()
    {
    	$this->_viewData['cur_title'] = 'user';
        $this->_viewData['user_info'] = $this->_user_info;
        $page = $this->input->get('page',true);
        $page = isset($page) ? $page : 1;
        $page_size = 10;
        $params = [
            'select'=>'*',
            'where'=>[
                'is_del'=>0
            ],
            'order_by'=>'id asc'
        ];
        $total_rows = $this->Tb_admin_user->get($params, $get_one = false, $get_rows = true);
        if($total_rows >0){
            $params['limit'] = [
                'page'=>$page,
                'page_size'=>$page_size
            ];
            $list = $this->Tb_admin_user->get($params, $get_one = false, $get_rows = false);
            $total_page = ceil($total_rows / $page_size);
        } else {
            $list = [];
            $total_rows = 0;
            $total_page = 0;
        }
        $this->_viewData['page'] = $page;
        $this->_viewData['list'] = $list;
        $this->_viewData['total_page'] = $total_page;
        $this->_viewData['total_rows'] = $total_rows;

        parent::index('admin/index');
    }

    public function del_user()
    {
        $id = intval($this->input->post('id',true));
        try{
            if(empty($id)){
                throw new Exception("参数错误");
            }

            if($id == 1) {
                throw new Exception("超级管理员不能删除");
            }

            if($this->_user_info['id'] == $id){
                throw new Exception("不能删除自己");
            }

            $info = $this->Tb_admin_user->get_user_info($id);
            if(empty($info)){
                throw new Exception("系统错误 用户已经被删除");
            }

            $res = $this->Tb_admin_user->delete_user($id);
            if($res){
                $this->success_response('操作成功',$id);
            } else {
                $this->error_response('操作失败');
            }


        } catch(Exception $e){
            $this->error_response($e->getMessage());
        }
    }
}