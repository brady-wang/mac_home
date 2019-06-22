<?php

/**
 * Created by PhpStorm.
 * User: brady
 * Date: 2018/2/26
 * Time: 14:30
 */
class Tb_daily_sentence extends MY_Model
{
    protected $table = "daily_sentence";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取每日一句
     */
    public function get_sentence()
    {
        $time = time();
        $res = $this->db->from($this->table)
            ->where(['dateline'=>date("Y-m-d",$time)])
            ->get()
            ->row_array();
        if(empty($res)){
            $res = $this->get_sentence_api();
        }
        return $res;
    }

    /**
     * 通过api获取每日一句
     */
    public function get_sentence_api()
    {
        $url = "http://open.iciba.com/dsapi/";
        $res = curl_get($url);
        if(!empty($res)){
            //插入数据库
            $data = [
                'content'=>$res['content'],
                'note'=>$res['note'],
                'translation'=>$res['translation'],
                'picture'=>$res['picture'],
                'picture2'=>$res['picture2'],
                'dateline'=>$res['dateline']
            ];
            $this->db->insert($this->table,$data);
        }
        return $res;
    }

    public function get_all()
    {
        $res = $this->db->from($this->table)
            ->select('id,content,translation,dateline,note')
            ->order_by('id desc')
            ->get()
            ->result_array();
        return $res;
    }
}