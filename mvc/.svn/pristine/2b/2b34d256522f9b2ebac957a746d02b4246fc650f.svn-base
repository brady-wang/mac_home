<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2018/12/4
 * Time: 08:49
 */

namespace core;





class base
{
	public static $classMap = array();

	public  $assign = array();

	//启动框架
	static  public function run() {
		//初始化日志类
		\core\lib\log::init();
		//路由设置
		$route = new \core\lib\route();
		$controller  = $route->controller;
		$action = $route->action;
		$controller_file = ROOT.'/'.MODULE.'/controller/'.$controller.".php";
		$controller_class = "\\".MODULE."\controller\\".$controller;
		if(is_file($controller_file)){
			include ($controller_file);
			$contr = new $controller_class();
			$contr->$action();

		} else {
			throw new \Exception("控制器不存在");
		}

	}

	//自动加载类 new \core\route();
	static public function load($class)
	{

		$class  = str_replace('\\','/',$class);
		$file_name = ROOT.'/'.$class.'.php';
		if(isset(self::$classMap[$class])){
			return true;
		} else {
			if(file_exists($file_name)){
				include $file_name;
				self::$classMap[$class] = $class;

			} else {
				return false;
			}
		}


	}

	//加载试图
	public function display($file)
	{
		$file = ROOT.'/'.MODULE."/views/".$file.".php";

		if(file_exists($file)){
			extract($this->assign);
			include $file;
		} else{
			throw new \Exception("不存在的视图文件:".$file);
		}
	}

	//赋值变量
	public  function assign($key,$value)
	{
		$this->assign[$key] = $value;
	}
}