<?php

/**
 * Created by PhpStorm.
 * User: brady
 * Date: 2018/3/6
 * Time: 11:38
 */
class Tb_tag extends MY_Model
{
    protected $table = "tag";

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all_tags(){
        return $res = $this->db->from($this->table)->get()->result_array();
    }

    /**
     * @desc 添加标签
     * @param $tag_arr
     */
    public function random_color_tag($tag_arr)
    {
        $tag_arr = array_unique($tag_arr);
        if(!empty($tag_arr)){
            //获取所有标签信息
            $all_tags =  $this->Tb_tag->get_all_tags();
            if(!empty($all_tags)){
                foreach ($all_tags as $key => $value) {
                    $all_tags_name[] = $value['tag_name'];
                }
            } else {
                $all_tags_name = [];
            }

            //判断用户输入的标签是否存在，如果不存在，创建该标签，并随机选择一个颜色
            $random_color = array('tagc1','tagc2','tagc3','tagc4','tagc5');
            foreach ($tag_arr as $key => $value) {
                if(!in_array($value, $all_tags_name)){
                    $color = array_rand($random_color);
                    $data = [
                        'tag_name'=>$value,
                        'tag_button_type'=>$random_color[$color]
                    ];
                    $this->replace($data);
                }
            }
        }


    }

    public function get_by_tag_name($tag_name)
    {
        $res = $this->db->from($this->table)
            ->where(['tag_name'=>$tag_name])
            ->get()
            ->row_array();
        return $res;
    }

    public function get_recent_tag($number)
    {
        $res = $this->db->from($this->table)
            ->select('id,tag_name')
            ->order_by('id desc')
            ->limit($number)
            ->get()
            ->result_array();
        return $res;
    }

    public function get_tag_by_id($id)
    {
        $res = $this->db->from($this->table)
            ->where(['id'=>$id])
            ->get()
            ->row_array();
        return $res;
    }

    public function delete_tag($id)
    {
        $this->db->delete($this->table,['id'=>$id]);
        $this->load->model("Tb_article_tag");
        $this->Tb_article_tag->del_tag($id);
    }

//    public function getTagInfo(){
//        $this->load->database();
//        $sql="select tag.tag_name, article.tag_id, count(article.article_id) as article_num, tag.tag_button_type from article_tag as article join tag as tag where article.tag_id = tag.id group by tag.tag_name";
//        $data=$this->db->query($sql)->result_array();
//        return $data;
//    }
//    public function getArticlesDuring($offset,$row){
//        $this->load->database();
//        $sql="select * from articles limit {$offset},{$row}";
//        $data['data'] = $this->db->query($sql)->result_array();
//        return $data;
//    }
//    public function getTagByTagid($tag_id){
//        $this->load->database();
//        $sql="select * from tag  where id = {$tag_id}";
//        $data = $this->db->query($sql)->result_array();
//        return $data;
//    }
//
//    public function getTagidOfArticle($article_id){
//        $this->load->database();
//        $sql="select a.tag_id from article_tag as a join tag as b where a.tag_id = b.id and a.article_id = {$article_id}";
//        $data = $this->db->query($sql)->result_array();
//        return $data;
//    }
    public function getTagDuring($offset,$row){
        $this->load->database();
        $sql="select * from tag order by id DESC";
        $data = $this->db->query($sql)->result_array();
        return $data;
    }


}