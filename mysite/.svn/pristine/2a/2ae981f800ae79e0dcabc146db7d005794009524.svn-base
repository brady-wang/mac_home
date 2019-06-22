<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Tag extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Tb_tag');
        $this->_viewData['cur_title'] = 'tag';
    }

    public function index(){
        $this->_viewData['data'] = $this->Tb_tag->getTagDuring(1, 100);
        parent::index('admin/tag_index');
    }

    public  function delete(){

        $id = intval($this->input->post('id'));

        try{
            if(empty($id)){
                throw new Exception("参数错误");
            }

            $tag_res = $this->Tb_tag->get_tag_by_id($id);
            if(empty($tag_res)){
                throw new Exception("不存在的标签");
            }

            $this->db->trans_begin();

            //删除标签
            $this->Tb_tag->delete_tag($id);

            if($this->db->trans_status() == true){
                $this->db->trans_commit();
                $this->success_response("操作成功");
            } else {
                $this->db->trans_rollback();
                throw new Exception("删除失败");
            }

        }catch(Exception $e){
            $this->error_response($e->getMessage());
        }

    }


}
