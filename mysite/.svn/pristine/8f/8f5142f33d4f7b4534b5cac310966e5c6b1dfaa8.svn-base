<?php

/**
 * Created by PhpStorm.
 * User: brady
 * Date: 2018/2/2
 * Time: 11:23
 */
class Tb_admin_user extends MY_Model
{
    protected $table = 'user';
    protected $salt = "wer@#$$@#423sdSDFs";

    public function __construct()
    {
        parent::__construct();
    }

    //检测密码是否正确
    public function check_password($password, $user_info)
    {
        return $user_info['password'] === md5($this->salt . $password) ? true : false;

    }

    //检查用户是否存在
    public function check_user_exists($username)
    {
        if (empty($username)) {
            return false;
        }
        return $res = $this->db->from($this->table)
            ->select(['id','username','face','password'])
            ->where(['username' => $username])
            ->get()
            ->row_array();

    }

    //获取用户信息
    public function get_user_info($uid)
    {
        return $this->db->from($this->table)->where(['id' => $uid])->get()->row_array();
    }

    //批量获取作者信息
    public function get_author_names($author_ids)
    {
        $res = $this->db->from($this->table)->where_in('id', $author_ids)->select(['id', 'nick_name'])->get()->result_array();
        if (!empty($res)) {
            $new_res = [];
            foreach ($res as $k => $v) {
                $new_res[$v['id']] = $v;
            }
        } else {
            $new_res = array();
        }
        return $new_res;
    }

    //更新密码
    public function update_password($admin_id, $password)
    {
        $new_pwd = md5($this->salt . $password);
        $this->db->update($this->table, ['password' => $new_pwd], ['id' => $admin_id]);
        return $this->db->affected_rows();
    }

    //更新管理员信息
    public function update_admin_info($admin_id, $data)
    {
        $this->db->update($this->table, $data, ['id' => $admin_id]);
        return $this->db->affected_rows();
    }

    public function delete_user($id)
    {
        $this->db->update($this->table,['is_del'=>1,'delete_time'=>date("Y-m-d H:i:s",time())],['id'=>$id]);
        return $this->db->affected_rows();
    }




}