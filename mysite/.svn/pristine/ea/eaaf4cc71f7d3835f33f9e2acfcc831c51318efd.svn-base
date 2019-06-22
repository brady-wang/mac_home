<?php

/**
 * Created by PhpStorm.
 * User: brady
 * Date: 2018/3/10
 * Time: 16:02
 */
class O_cron extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("M_log");
    }

    //获取每日一句
    public function get_daily_sentense()
    {
        $this->load->model("Tb_daily_sentence");
        $res = $this->Tb_daily_sentence->get_sentence();
        if(!empty($res)){
            $msg = "获取每日一句成功";
        } else {
            $msg = "获取每日一句失败";
        }

        var_dump($msg);
        $this->M_log->cron_log($msg);

    }

    public function init_article_index($add)
    {
        $this->load->model("O_es");
        $this->load->model("Tb_articles");
        $this->O_es->init(config_item("db")['db'],'articles');
        $res = $this->Tb_articles->get_list(1,1000,'*','desc');
        if($add == 'true'){
            $this->O_es->add_index();
        }

        $this->O_es->del_index();
        foreach($res as $v){

            $this->O_es->add_doc($v['id'],$v);
            var_dump($msg = "更新article文章索引");
        }
        $msg = "更新article文章索引";
        var_dump($msg);
        $this->M_log->cron_log($msg);
    }
}