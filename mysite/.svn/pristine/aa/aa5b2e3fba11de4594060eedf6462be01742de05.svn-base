<?php

class Images extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function upload()
    {
        $this->load->model("O_upload");
        try{
            $img_path = $this->O_upload->do_upload('article_list');
            $this->success_response("上传成功");
        }catch(Exception $e){
            $this->error_response($e->getMessage());
        }
    }

    /**
     * 上传头像
     */
    public function upload_face()
    {

    }
}