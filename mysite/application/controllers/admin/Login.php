<?php

/**
 * Created by PhpStorm.
 * User: brady
 * Date: 2018/10/18
 * Time: 10:28
 */
class Login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function index()
    {
        $this->load->view("admin/login");
    }

    public function do_login()
    {
        $username = trim($this->input->post('username', true));
        $password = trim($this->input->post('password', true));

        try {
            if (empty($username)) {
                throw new Exception("用户名不能为空");
            }
            if (empty($password)) {
                throw new Exception("密码不能为空");
            }
            $this->load->model("tb_admin_user");
            $user_info = $this->tb_admin_user->check_user_exists($username);
            if (!$user_info) {
                throw new Exception("用户不存在");
            }

            $check_password = $this->tb_admin_user->check_password($password, $user_info);
            if (!$check_password) {
                throw new Exception("密码错误");
            } else {
                $publicDomain = get_public_domain();
                unset($user_info['password']);
                set_cookie("userInfo", json_encode($user_info), 0, $publicDomain);
                success_response("登陆成功");
            }

        } catch (Exception $e) {
            error_response($e->getMessage());
        }
    }

    public function logout()
    {
        delete_cookie("userInfo", get_public_domain());
        redirect(base_url('/admin/login'));
    }
}