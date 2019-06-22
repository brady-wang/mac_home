<?php

/**
 * Created by PhpStorm.
 * User: brady
 * Date: 2018/10/18
 * Time: 10:14
 */
class MY_Controller extends CI_Controller
{
    protected $_user_info;  //登陆用户信息
    protected $_viewData; //视图数据

    public function __construct()
    {
        parent::__construct();
	    $this->load->model("tb_category");
	    $this->load->model("tb_admin_user");
	    $this->load->model("tb_admin_action_log");
	    $this->load->model("exception_model");
	    $this->load->model("Tb_daily_sentence");
	    $this->load->model("Tb_articles");
	    $this->__check_login();
	    $this->__init_data();
    }


    //初始化数据
    public function __init_data()
    {

	    $daily = $this->Tb_daily_sentence->get_sentence(); //每日一句
	    //获取 热门推荐
	    $hot_recommend = $this->Tb_articles->get_hot_recommend();
	    $this->_viewData['hot_recommend'] = $hot_recommend;
	    $this->_viewData['now'] = date("Y-m-d H:i:s",time());

	    $this->_viewData['daily'] = $daily;
	    $this->_viewData['front'] = config_item('front_verify');//前端验证开关

    }

    //检测是否登陆
    public function __check_login()
    {
        if ($this->uri->segment(1) == 'admin') {

            $userInfoSeri = filter_input(INPUT_COOKIE, 'userInfo');
            $user_info = json_decode($userInfoSeri, true);
            if (!empty($user_info) && is_array($user_info)) {
                unset($user_info['password']);
                $this->_user_info = $user_info;
	            $this->_viewData['user_info'] = $this->_user_info;
            } else {
                redirect(base_url('/admin/login'));
            }
        }
    }

    //成功返回
    function success_response($msg,$data=[])
    {
        echo json_encode(['success' => true, 'msg' => $msg,'data'=>$data]);
        exit();
    }

	//失败返回
    function error_response($msg)
    {
        echo json_encode(['success' => false, 'msg' => $msg]);
        exit();
    }


    public function index()
    {

        $def_vars = func_get_args();
        $url = $def_vars[0];
        $s = explode('/', $url);


	    $this->load->model('tb_category');
	    $this->load->model('tb_site_info');
	    $this->load->model('Tb_tag');
	    $this->load->model('Tb_friend_ship');

	    $this->_viewData['all_category'] =  $all_category = $this->tb_category->getAllCategory();
	    $this->_viewData['siteinfo']= $this->tb_site_info->info();
	    $this->_viewData['all_tag'] = $this->Tb_tag->get_all_tags();
	    $this->_viewData['friendship'] = $this->Tb_friend_ship->getAllFriend();


        if ($s[0] == 'index') {

        } elseif ($s[0] == 'admin') {
            $this->load->view($s[0] . '/header.php',$this->_viewData);
            $this->load->view($s[0] . '/top.php', $this->_viewData);
            $this->load->view($s[0] . '/menu.php', $this->_viewData);
            $this->load->view($url, $this->_viewData);
            $this->load->view($s[0] . '/footer',$this->_viewData);
        } else {
            $this->load->view($url, $this->_viewData);
        }

    }

}



