<?php
	/**
	 * 入口文件
	 * 1 定义常量
	 * 2 加载函数库
	 * 3 启动框架
	 */

define('ROOT',realpath('./'));
define("CORE",ROOT.'/core');
define("APP",ROOT.'/app');

define("MODULE",'app');

//debug模式
define("DEBUG",true);

include "vendor/autoload.php";

if(DEBUG){

	$whoops = new \Whoops\Run;
	$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
	$whoops->register();

	ini_set('display_errors',"On");
} else {
	ini_set("display_errors","Off");
}


//加载公共帮助函数
include CORE.'/common/base_helper.php';


//启动框架
include CORE.'/base.php';
spl_autoload_register('\core\base::load');


\core\base::run();