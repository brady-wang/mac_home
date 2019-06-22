<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Category extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model("Tb_category");
        $this->_viewData['cur_title'] = __CLASS__;
    }

    public function index(){

        parent::index("admin/category_index");
    }

    public  function add(){
        $category = substr(trim($this->input->post("category",true)),0,50);

        try{
            if(empty($category)){
                throw  new Exception("请输入分类名称");
            }
            //检测分类名称是否存在
            $check_exist = $this->Tb_category->check_exists($category);
            if(!empty($check_exist)){
                throw new Exception('分类已存在');
            }

            $res = $this->Tb_category->add_category($category);
            if($res > 0 ){
                $this->success_response("新增成功");
            } else{
                $this->error_response("新增失败");
            }

        }catch(Exception $e){
            $this->error_response($e->getMessage());
        }

    }

    public  function cate_del(){
        $id = intval($this->input->post("id",true));
        $this->load->model("Tb_articles");

        try{
            if(in_array($id,config_item("not_allow_del_category"))){
                throw new Exception("前台主分类 不能删除");
            }
            $number = $this->Tb_articles->get_total_rows_by_cate($id,true);
            if($number > 0){
                throw new Exception("分类下有还有文章 不能删除!");
            }


            $aff_rows = $this->Tb_category->del_category($id);
            if($aff_rows > 0 ){
                $this->success_response("操作成功");
            } else {
                throw new Exception("操作失败");
            }

        }catch(Exception $e){
            $this->error_response($e->getMessage());
        }


    }

    public function cate_edit()
    {
        $id = intval($this->input->post('id',true));
        $category = $this->input->post('category',true);

        try{
            if(empty($id)){
                throw new Exception("参数错误 分类id不存在");
            }

            if(empty($category)){
                throw new Exception("请输入分类名称");
            }

            //检测分类名称是否存在
            $check_exist = $this->Tb_category->check_exists($category);
            if(!empty($check_exist)){
                throw new Exception('分类已存在');
            }

            $res = $this->Tb_category->edit_category($id,$category);
            if($res > 0 ){
                $this->success_response("编辑成功");
            } else{
                $this->error_response("编辑失败");
            }

        }catch(Exception $e){
            $this->error_response($e->getMessage());
        }
    }


}
