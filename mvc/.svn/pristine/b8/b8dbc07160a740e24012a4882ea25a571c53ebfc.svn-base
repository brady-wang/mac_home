<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2018/12/4
 * Time: 09:00
 */
namespace core\lib;
use \core\lib\config;
class route
{
	public $controller;
	public $action;

	public function __construct()
	{
		//xx.com/index/index/id/1/name/wang
		/**
		 * 1 隐藏index.php
		 * 2 获取url 参数部分
		 * 3 返回对应得控制器和方法
		 */

		if(isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] != '/'){
			$uri_arr = explode('/',trim($_SERVER['REQUEST_URI'],'/'));

			if(isset($uri_arr[0])){
				$this->controller = $uri_arr[0];
				unset($uri_arr[0]);
			} else {
				$this->controller = config::get('default_controller');
			}

			if(isset($uri_arr[1])){
				$this->action = $uri_arr[1];
				unset($uri_arr[1]);
			} else {
				$this->action = config::get('default_action');
			}

		} else {
			$this->controller = config::get('default_controller');;
			$this->action = config::get('default_action');
		}

		//id/1/name/wang
		$count = count($uri_arr);
		if($count >0) {
			$i = 2;
			while($i <= $count){
				if(isset($uri_arr[$i+1])){
					$_GET[$uri_arr[$i]] = $uri_arr[$i+1];
				}

				$i = $i + 2;
			}
		}


	}
}