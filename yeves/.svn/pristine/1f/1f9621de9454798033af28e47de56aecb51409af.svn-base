<?php
/**
 * Created by PhpStorm.
 * User: brady
 * Date: 2018/3/10
 * Time: 16:29
 */

class Tag extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($tag_id)
    {
        $tag_id = intval($this->input->get('id',true));

        $page = intval(ceil($this->input->get('page',true)));
        $page = ($page >= 1 ) ? $page : 1;
        $page_size = config_item('page_size');

        //获取总数
        $total_rows = $this->Tb_articles->get_total_rows_by_tag($tag_id);
        $total_page = ceil($total_rows / $page_size);
        $page = ($page > $total_page) ? $total_page : $page;
        if($total_rows > 0 ){
            $list = $this->Tb_articles->get_list_by_tag($page,$page_size,$tag_id);
        } else {
            $list = [];
            $page = 1;
            $total_rows = 0;
        }
        $this->_viewData['data'] = $list;
        $this->_viewData['page'] = $page ;
        $this->_viewData['total_rows'] = $total_rows;
        $this->_viewData['total_page'] = $total_page;
        $this->_viewData['page_size'] = $page_size;
        $this->_viewData['cate_id'] = $tag_id;

        $this->_viewData['active'] = "Category".$tag_id;
        parent::index('index/tag');

    }
}