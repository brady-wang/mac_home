<?php

/**
 * Created by PhpStorm.
 * User: brady
 * Date: 2018/2/2
 * Time: 11:23
 */
class Tb_friend_ship extends MY_Model
{
    protected $table = "friend_ship";

    public function __construct(){
        parent::__construct();
    }
    public function getAllFriend(){
        $this->load->database();
        $sql="select * from ".$this->table. " order by id desc ";
        $data_tmp =$this->db->query($sql)->result_array();
        foreach ($data_tmp as $value) {
            $friendship_id = $value['id'];
            $data["$friendship_id"]['id'] = $value['id'];
            $data["$friendship_id"]['link'] = $value['link'];
            $data["$friendship_id"]['link_name'] = $value['link_name'];
            $data["$friendship_id"]['link_order'] = $value['link_order'];
        }
        return $data;
    }

    public function getFriendship($link_id){
        $this->load->database();
        $sql="select * from ".$this->table. "  where id={$link_id}";
        $data =$this->db->query($sql)->result_array();
        return $data;
    }
    public function getFriendshipDuring($offset,$row){
        $this->load->database();
        $sql="select * from ".$this->table. "  order by id DESC limit {$offset},{$row}";
        $data = $this->db->query($sql)->result_array();
        return $data;
    }


}