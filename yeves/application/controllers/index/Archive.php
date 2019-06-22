<?php
/**
 * Created by PhpStorm.
 * User: brady
 * Date: 2018/3/7
 * Time: 9:24
 */

class Archive extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Tb_articles');
    }

    public function index()
    {
        $this->_viewData['active'] = __CLASS__;

        //获取所有的文章
        $total_rows = $this->Tb_articles->get_total_rows();
        $list = $this->Tb_articles->get_list(1,$total_rows,'create_time,title,id,category','desc');

        //对数据进行处理 分为年月日分组
        $year_list = [];
        foreach($list as $v){
            $year_list[date("Y",strtotime($v['create_time']))][] = $v;
        }

        $month_list = [];
        foreach($year_list as $k=>$year){
            foreach($year as $month){
                $month_list[$k][date("m",strtotime($month['create_time']))][] = $month;
            }
        }
        $this->_viewData['list'] = $month_list;
        parent::index('index/archive');
    }
}