<?php

/**
 * Created by PhpStorm.
 * User: brady
 * Date: 2018/2/3
 * Time: 15:11
 */
class Tb_admin_action_log extends MY_Model
{
    protected $table = 'admin_action_log';

    public function __construct()
    {
        parent::__construct();
    }

    public function add_log($data)
    {
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $data['create_time'] = date("Y-m-d H:i:s", time());
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }
}