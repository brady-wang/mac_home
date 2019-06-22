<?php
/**
 * Created by PhpStorm.
 * User: brady
 * Date: 2018/3/10
 * Time: 16:01
 */

class Cron extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        ignore_user_abort();
        set_time_limit(0);

        /*Force run this script in CLI. Added by Terry.*/
        if(!$this->input->is_cli_request()){
            echo 'Please run this script in CLI.';
            exit;
        }
        $this->load->model('O_cron');
        $this->load->model('O_pcntl');
    }

    //获取每日一句
    public function get_daily_sentense()
    {
        $this->O_cron->get_daily_sentense();
    }

    //初始化文章索引
    public function init_article_index($add = 'false')
    {
        $this->O_cron->init_article_index($add);
    }
}