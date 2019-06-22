<?php

/**
 * Created by PhpStorm.
 * User: brady
 * Date: 2018/10/18
 * Time: 11:16
 */
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/Format.php';
class User extends  \Restserver\Libraries\REST_Controller
{
    public function __construct($config='rest')
    {
        parent::__construct($config);
        $this->__request_data = $this->input->get_post();
        $this->load->model("Tb_admin_user");
        //$this->__check_sign($this->__request_data['sign'],$this->__request_data['token']);
    }

    /*验证令牌*/
    private function __check_sign($sign, $token)
    {
        if ($sign != api_mobile_create_sign($token) && config_item('verify_token') == true) {
            $this->error_response('999');
        }
    }


    public function index_get()
    {
        try{
            if(empty($this->_user_info)){
                throw new Exception("100000");
            }
            $page = isset($this->__request_data['page']) ? $this->__request_data['page'] : 1;
            $page_size = 10;
            $params = [
                'select'=>'*',
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
            $this->success_response(['list'=>$list,'page'=>$page,'page_size'=>$page_size,'total_rows'=>$total_rows,'total_page'=>$total_page,'user_info'=>$this->_user_info]);
        }catch(Exception $e){
            $this->error_response($e->getMessage());
        }
    }
}