<?php

/**
 * Created by PhpStorm.
 * User: brady
 * Date: 2018/2/2
 * Time: 12:04
 */
class Daily extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->model("Tb_daily_sentence");
        $list  = $this->Tb_daily_sentence->get_all();
        $this->_viewData['active'] = __CLASS__;
        $this->_viewData['list'] = $list;
        parent::index('index/daily');

    }
}