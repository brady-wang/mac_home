<?php
namespace app\index\controller;
use think\Config;
use think\Env;

class Index
{
    public function index()

    {
    	dump($_ENV);
        dump(Env::get("name"));
    }
}
