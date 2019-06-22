<?php

/**
 * Created by PhpStorm.
 * User: brady
 * Date: 2018/3/6
 * Time: 11:38
 */
class Tb_article_tag extends MY_Model
{
    protected $table = "article_tag";

    public function __construct()
    {
        parent::__construct();
    }

    // 获取文章的标签
    public function get_tags_by_article_id($id)
    {
        $res = $this->db->from($this->table." as a")
            ->select('b.tag_name')
            ->join('tag as b','a.tag_id=b.id')
            ->where(['a.article_id'=>$id])
            ->get()
            ->result_array();
        return $res;
    }

    //插入文章的标签
    public function add_article_tag($article_id,$tag_arr)
    {
        $this->load->model("Tb_tag");
        if(!empty($tag_arr)){
            foreach ($tag_arr as $key => $value) {
                $tag_res = $this->Tb_tag->get_by_tag_name($value);
                $data = [
                    'article_id'=>$article_id,
                    'tag_id'=>$tag_res['id']
                ];
                $this->replace($data);
            }
        }

    }

    //更新文章的标签,删除原有的,添加新的
    public function update_article_tag($article_id,$tag_arr)
    {
        $this->del_article_tag($article_id);
        $this->add_article_tag($article_id,$tag_arr);
    }

    //删除文章的标签
    public function del_article_tag($article_id)
    {
        return $this->delete(['article_id'=>$article_id]);
    }

    //删除标签 关联删除
    public function del_tag($tag_id)
    {
        return $this->delete(['tag_id'=>$tag_id]);
    }



}