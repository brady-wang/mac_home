<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Setting extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model("Tb_admin_user");

    }

    /**
     * 上传头像
     */
    public function upload_face()
    {
        //图片上传
        $this->load->model("O_upload");
        try{
            if(preg_match('/^\/upload/',$_POST['image'])){
                $path = $_POST['image'];
            } else {
                $img_path = $this->O_upload->do_upload('user_info',$base64_img=$_POST['image']);
                $path  = $img_path;
            }
            $data = ['path'=>$path];

            //更新用户表的face
            $this->load->model("Tb_admin_user");
            $old_face = $this->user_info['face'];
            $this->Tb_admin_user->update_admin_info($this->user_info['id'], ['face'=>$path]);
            //删除原有的头像
             unlink(".".$old_face);
            if($this->db->trans_status() === true){
                $this->db->trans_commit();
                $this->success_response("修改头像成功",$data);
            } else {
                $this->db->trans_rollback();
                throw new Exception("修改失败");
            }



        }catch(Exception $e){
            $this->error_response($e->getMessage());
        }

    }

    //站点信息
    public function site_info()
    {
        $this->load->model('tb_site_info');
        $this->_viewData['data']= $this->tb_site_info->info();

        $this->_viewData['cur_title'] = __FUNCTION__;
        parent::index("admin/site_info");
    }

    public function change_password()
    {
        if(empty($this->user_info)){
            redirect(base_url('/login'));
        }
        $this->_viewData['cur_title'] = __FUNCTION__;
        parent::index("admin/change_password");
    }

    //博主信息
    public function blog_info()
    {
        $this->load->model('Tb_blog_info');
        $this->_viewData['data']= $this->Tb_blog_info->info();

        $this->_viewData['cur_title'] = __FUNCTION__;
        parent::index("admin/blog_info");
    }

    //头像
    public function face()
    {
        $this->load->model('Tb_blog_info');
        $this->_viewData['data']= $this->Tb_blog_info->info();

        $this->_viewData['cur_title'] = __FUNCTION__;
        parent::index("admin/face");
    }

    public function back_up()
    {
        $this->_viewData['path'] =  dirname(dirname(dirname(dirname(__FILE__)))).'/article/';
        parent::index("my/others_backup");

    }

    //更新博主信息
    public function do_set_blog_info()
    {
        $real_name = substr(trim($this->input->post("real_name",true)),0,50);
        $nick_name = substr(trim($this->input->post("nick_name",true)),0,50);
        $job = substr(trim($this->input->post("job",true)),0,50);
        $email = substr(trim($this->input->post("email",true)),0,50);
        $mobile = substr(trim($this->input->post("mobile",true)),0,50);
        $city = substr(trim($this->input->post("city",true)),0,20);
        $country = substr(trim($this->input->post("country",true)),0,20);
        $motto = substr(trim($this->input->post("motto",true)),0,100);

        try{
            $this->load->model("Tb_blog_info");
            $data = [
                'job'=>$job,
                'real_name'=>$real_name,
                'nick_name'=>$nick_name,
                'email'=>$email,
                'mobile'=>$mobile,
                'city'=>$city,
                'country'=>$country,
                'motto'=>$motto,
                'update_time'=>date("Y-m-d H:i:s")
            ];
            $res = $this->Tb_blog_info->update_info($data);
            if($res > 0){
                $this->success_response("更新成功");
            } else {
                throw new Exception('更新失败');
            }
        }catch(Exception $e){
            $this->error_response($e->getMessage());
        }
    }

    //更新站点信息
    public  function do_set_site_info(){

        $title = substr(trim($this->input->post("title",true)),0,50);
        $url = substr(trim($this->input->post("url",true)),0,50);
        $keywords = substr(trim($this->input->post("keywords",true)),0,255);
        $description = substr(trim($this->input->post("description",true)),0,500);

        $this->load->model('Tb_site_info');

        $data = [
            'title'=>$title,
            'url'=>$url,
            'keywords'=>$keywords,
            'description'=>$description
        ];
        $res  = $this->Tb_site_info->update_info($data);
        if($res > 0 ){
            $this->success_response('更新成功');
        } else {
            $this->error_response("更新失败");
        }

    }

    public function about()
    {

        $this->load->model('about_model');
        $this->_viewData['data']= $this->about_model->getAboutInfo();


        parent::index("admin/others_about");
    }
    public function feedback()
    {

        $this->_viewData['cur_title'] = array('','','','','','','active');
        parent::index("admin/others_feedback");
    }

    public function edit_about()
    {

        $this->load->model('about_model');
        $this->_viewData['data']= $this->about_model->updateAboutInfo();

        parent::index("admin/others_about_success");
    }


    public function backup()
    {
        $this->load->helper('url');
        $this->load->database();
        $this->load->model('Tb_articles');
        $data = $this->Tb_articles->getAllArticles();
        $path = $_POST['backup_path'];
        foreach ($data as $key => $value) {
            $str = 'title:'.$value['title']."\r\ncategory:".$value['category']."\r\ntag:".$value['tag']."\r\ncreate_time:".$value['create_time']."\r\n\r\n============================\r\n\r\n".$value['content'];
            $file = $path.$value['title'].'.txt';

            @file_put_contents($file,$str);
        }
        parent::index("admin/others_backup_success");
    }



    //执行密码修改
    public function do_change_pwd()
    {
        $old_pwd = trim($this->input->post('old_pwd',true));
        $new_pwd = trim($this->input->post('new_pwd',true));
        $new_pwd_re = trim($this->input->post('new_pwd_re',true));
        try{
            if(empty($this->user_info)){
                throw new Exception("请先登录后操作");
            }

            $this->exception_model->check(['old_pwd'=>$old_pwd,'new_pwd'=>$new_pwd,'new_pwd_re'=>$new_pwd_re]);
            if($new_pwd_re !== $new_pwd ){
                throw new Exception("两次输入不一致");
            }

            //验证原密码是否正确
            $uid = $this->user_info['id'];
            $user_info = $this->Tb_admin_user->get_user_info($uid);
            if(!$this->Tb_admin_user->check_password($old_pwd,$user_info)) {
                throw new Exception("原密码不正确");
            }

            $res = $this->Tb_admin_user->update_password($uid,$new_pwd);
            if($res > 0) {
                $this->success_response("修改成功");
            } else {
                throw new Exception("修改失败");
            }


            parent::index("admin/others_change_password_success");

        }catch(Exception $e){
            $this->error_response($e->getMessage());
        }

    }


}
