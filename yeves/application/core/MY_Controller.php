<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller{
    protected $_viewData;
    protected $user_info;

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
            //根据月份归档
            $sum_by_month = $this->Tb_articles->sum_by_month();
            $sum_by_cate = $this->Tb_articles->sum_by_cate();

            if(!empty($sum_by_cate)){
                foreach($sum_by_cate as &$v){
                    $v['cate_name'] = isset($all_category[$v['category']]['category'])?$all_category[$v['category']]['category'] : '' ;
                }
            }
            $this->_viewData['sum_by_month'] = $sum_by_month;
            $this->_viewData['sum_by_cate'] = $sum_by_cate;

            //获取所有标签
            $all_tag = $this->Tb_tag->get_all_tags();
            $this->_viewData['all_tag'] = $all_tag;
            $this->load->view($s[0] . '/header.php', $this->_viewData);
            $this->load->view($url, $this->_viewData);
            $this->load->view($s[0] . "/footer.php", $this->_viewData);
        } elseif ($s[0] == 'admin') {

            $admin_category = [];
            if(!empty($all_category)){
                foreach($all_category as $v){
                    $v['number'] = $this->Tb_articles->get_total_rows_by_cate($v['id'],true);
                    $admin_category[] = $v;
                }
            }
            $this->_viewData['admin_category'] = $admin_category;
            $this->load->view($s[0] . '/header.php',$this->_viewData);
            $this->load->view($s[0] . '/menu.php', $this->_viewData);
            $this->load->view($url, $this->_viewData);
            $this->load->view($s[0] . '/footer',$this->_viewData);
        } else {
            $this->load->view($url, $this->_viewData);
        }

    }

    public function __init_data()
    {
        $daily = $this->Tb_daily_sentence->get_sentence(); //每日一句
        //获取 热门推荐
        $hot_recommend = $this->Tb_articles->get_hot_recommend();
        $this->_viewData['hot_recommend'] = $hot_recommend;
        $this->_viewData['now'] = date("Y-m-d H:i:s",time());
        $this->_viewData['user_info'] = $this->user_info;
        $this->_viewData['daily'] = $daily;
        $this->_viewData['front'] = config_item('front_verify');//前端验证开关
    }

    public function __check_login()
    {
        if ($this->uri->segment(1) == 'admin') {

            $userInfoSeri = filter_input(INPUT_COOKIE, 'userInfo');
            $user_info = json_decode($userInfoSeri, true);
            if (!empty($user_info) && is_array($user_info)) {
                $uid = $user_info['id'];
                $user = $this->tb_admin_user->get_user_info($uid);
                $this->user_info = $user;
                $this->_viewData['user_info'] = $user;

            } else {
                redirect(base_url('/login'));
            }
        }
    }

    public function success_response($msg,$data=[])
    {
        echo json_encode(['success' => true, 'msg' => $msg,'data'=>$data]);
        exit();
    }

    public function error_response($msg)
    {
        echo json_encode(['success' => false, 'msg' => $msg]);
        exit();
    }
}
?>