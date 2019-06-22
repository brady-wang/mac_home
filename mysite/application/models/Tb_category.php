<?php

/**
 * Created by PhpStorm.
 * User: brady
 * Date: 2018/2/2
 * Time: 11:23
 */
class Tb_category extends MY_Model
{
    protected $table = "category";

    public function __construct()
    {
        parent::__construct();
    }

    public function getAllCategory(){
        $this->load->database();
        $sql="select * from category order by id asc ";
        $data_tmp =$this->db->query($sql)->result_array();
        foreach ($data_tmp as $value) {
            $category_id = $value['id'];
            $data["$category_id"]['id'] = $value['id'];
            $data["$category_id"]['category'] = $value['category'];
            $data["$category_id"]['category_order'] = $value['category_order'];
            $data["$category_id"]['create_time'] = $value['create_time'];
        }
        return $data;
    }


    public function getAllArticles($category_id){
        $this->load->database();
        $sql="select * from articles where category={$category_id}";
        $data = $this->db->query($sql)->result_array();
        return $data;
    }
    public function getCategory($category_id){
        $this->load->database();
        $sql="select * from category where id={$category_id}";
        $data =$this->db->query($sql)->result_array();
        return $data;
    }
    public function getCategoryDuring($offset,$row){
        $this->load->database();
        $sql="select * from category order by id DESC limit {$offset},{$row}";
        $data = $this->db->query($sql)->result_array();
        return $data;
    }

    //检测分类是否存在
    public function check_exists($category)
    {
        $res = $this->db->from($this->table)
            ->select("id,category")
            ->where(['category'=>$category])
            ->get()
            ->row_array();
        return $res;
    }

    //获取分类列表
    public function get_list()
    {
        $res = $this->db->from($this->table)->get()->result_array();
        return $res;
    }

    //批量获取分类名称
    public function get_cate_names($cate_ids)
    {
        $res = $this->db->from($this->table)->where_in('id', $cate_ids)->get()->result_array();
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

    //分类新增
    public function add_category($category)
    {
        $data['create_time'] = date("Y-m-d H:i:s", time());
        $data['category'] = $category;
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    //分类编辑
    public function edit_category($id,$category)
    {
        $this->db->update($this->table,['category'=>$category],['id'=>$id]);
        return $this->db->affected_rows();
    }

    public function del_category($id)
    {
        if(!in_array($id,config_item("not_allow_del_category"))){
            return $aff_rows = $this->delete(['id'=>$id]);
        } else {
            return 0;
        }
    }
}