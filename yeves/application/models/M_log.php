<?php

/**
 * Created by PhpStorm.
 * User: brady
 * Date: 2018/3/10
 * Time: 16:07
 */
class M_log extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function cron_log($content)
    {
        $this->db->insert('logs_cron', array(
            'content' => var_export($content, 1),
        ));
    }

}