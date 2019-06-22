<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class welcome extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Tb_articles', 'tb_category','Tb_daily_sentence']);
    }

    public function index()
    {

        $page = intval(ceil($this->input->get('page',true)));
        $page = ($page >= 1 ) ? $page : 1;
        $page_size = config_item('page_size');

        //获取总数
        $total_rows = $this->Tb_articles->get_total_rows();
        $total_page = ceil($total_rows / $page_size);
        $page = ($page > $total_page) ? $total_page : $page;
        if($total_rows > 0 ){
            $list = $this->Tb_articles->get_list($page,$page_size,'','desc');
        } else {
            $list = [];
            $page = 1;
            $total_rows = 0;
        }
        //获取最新的五个标签
        $this->load->model("Tb_tag");
        $number = 5;
        $recent_tag = $this->Tb_tag->get_recent_tag($number);
        $this->_viewData['data'] = $list;
        $this->_viewData['recent_tag'] = $recent_tag;
        $this->_viewData['page'] = $page ;
        $this->_viewData['total_rows'] = $total_rows;
        $this->_viewData['total_page'] = $total_page;
        $this->_viewData['page_size'] = $page_size;

        $this->_viewData['active'] = "Welcome";

        parent::index('index/welcome');
    }
}
