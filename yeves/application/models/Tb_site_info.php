<?php

/**
 * Created by PhpStorm.
 * User: brady
 * Date: 2018/2/2
 * Time: 11:23
 */
class Tb_site_info extends MY_Model
{
    protected $table = "site_info";

    public function __construct()
    {
        parent::__construct();
    }



    public function info()
    {
        $res = $this->db->from($this->table)->get()->row_array();
        return $res;
    }

    public function update_info($data)
    {
        $data['update_time'] = date('Y-m-d H:i:s',time());
        $info = $this->info();
        if(empty($info)){
            $this->db->insert($this->table,$data);
        } else {
            $this->db->update($this->table,$data,['id'=>$info['id']]);
        }
        return $this->db->affected_rows();
    }


}