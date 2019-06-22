<?php

/**
 * Created by PhpStorm.
 * User: brady
 * Date: 2018/2/2
 * Time: 12:17
 */
class Article extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Tb_articles");
    }

    public function index()
    {
        $this->_viewData['active'] = __CLASS__;

        $page = intval(ceil($this->input->get('page',true)));
        $page = ($page >= 1 ) ? $page : 1;
        $page_size = config_item('page_size');

        //获取总数
        $total_rows = $this->Tb_articles->get_total_rows();
        $total_page = ceil($total_rows / $page_size);
        $page = ($page > $total_page) ? $total_page : $page;
        if($total_rows > 0 ){
            $list = $this->Tb_articles->get_list($page,$page_size);
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

        parent::index('index/article');
    }

    /**
     * 分类下的文章
     */
    public function category()
    {
        $cate_id = intval($this->input->get('id',true));

        $page = intval(ceil($this->input->get('page',true)));
        $page = ($page >= 1 ) ? $page : 1;
        $page_size = config_item('page_size');

        //获取总数
        $total_rows = $this->Tb_articles->get_total_rows_by_cate($cate_id);
        $total_page = ceil($total_rows / $page_size);
        $page = ($page > $total_page) ? $total_page : $page;
        if($total_rows > 0 ){
            $list = $this->Tb_articles->get_list_by_cate($page,$page_size,$cate_id);
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
        $this->_viewData['cate_id'] = $cate_id;

        $this->_viewData['active'] = "Category".$cate_id;
        parent::index('index/category');

    }

    public function search()
    {
        $keywords = trim($this->input->get('keywords',true));

        $this->load->model("O_es");
        $this->O_es->init(config_item("db")['db'],'articles');
        $lists = $this->O_es->search_index_mul(['title','content'],$keywords);


        $page = intval(ceil($this->input->get('page',true)));
        $page = ($page >= 1 ) ? $page : 1;
        $page_size = config_item('page_size');

        //获取总数
        $total_rows = count($lists);
        $total_page = ceil($total_rows / $page_size);
        $page = ($page > $total_page) ? $total_page : $page;
        if($total_rows > 0 ){
            foreach($lists as $v){
                $list[] = $v['_source'];
            }
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
        $this->_viewData['keywords'] = $keywords;

        parent::index('index/search');
    }

    public function tag($tag_id)
    {
        $tag_id = intval($tag_id);
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

        $this->_viewData['active'] = "Tag".$tag_id;
        parent::index('index/tag');

    }

    /**
     * 归档的文章
     */
    public function month($year_month)
    {

        $page = intval(ceil($this->input->get('page',true)));
        $page = ($page >= 1 ) ? $page : 1;
        $page_size = config_item('page_size');

        //获取总数
        $total_rows = $this->Tb_articles->get_total_rows_by_year_month($year_month);
        $total_page = ceil($total_rows / $page_size);
        $page = ($page > $total_page) ? $total_page : $page;
        if($total_rows > 0 ){
            $list = $this->Tb_articles->get_list_by_year_month($page,$page_size,$year_month);
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
        $this->_viewData['year_month'] = $year_month;

        $this->_viewData['active'] = [
            'index'=>'',
            'article'=>'active',
            'about_me'=>''
        ];

        parent::index('index/month');

    }

    public function detail($id)
    {
        $this->load->database();
        //统计文章访问数
        $user_ip_name = 'user_ip_'.$id;
        if(empty($_SESSION[$user_ip_name])){
            $this->db->set('pv', 'pv+1', FALSE);
            $this->db->where('id', $id);
            $this->db->update('articles');
            $user_ip=$_SERVER["REMOTE_ADDR"];
            $user_ip = array($user_ip_name => $user_ip);
            $this->session->set_userdata($user_ip);
        }



        $data = $this->Tb_articles->get_article_detail($id);
        $this->_viewData['data'] = $data;
        parent::index('index/article_detail');
    }
}