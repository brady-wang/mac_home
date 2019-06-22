<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2018/12/4
 * Time: 10:35
 */

namespace core\lib;
class config
{
	static public $conf = array();
	static public function get($name,$file = "config_base")
	{
		/**
		 * 1 判断配置文件是否存在
		 * 2 判断配置是否存在
		 * 3 缓存配置
		 */

		$file_name = $file;

		if(isset(self::$conf[$file_name])){
			return self::$conf[$file_name][$name];
		} else {
			$file = ROOT."/core/config/".$file.".php";
			if(is_file($file)){
				$conf = include $file;
				if(isset($conf[$name])){
					self::$conf[$file_name] = $conf;
					return $conf[$name];
				} else {
					throw new \Exception("找不到配置项".$name);
				}
			} else {
				throw new \Exception("找不到配置文件".$file);
			}
		}



	}


	static public function all($file)
	{
		/**
		 * 1 判断配置文件是否存在
		 * 2 判断配置是否存在
		 * 3 缓存配置
		 */

		$file_name = $file;

		if(isset(self::$conf[$file_name])){
			return self::$conf[$file_name];
		} else {
			$file = ROOT."/core/config/".$file.".php";
			if(is_file($file)){
				$conf = include $file;
				self::$conf[$file_name] = $conf;
				return $conf;
			} else {
				throw new \Exception("找不到配置文件".$file);
			}
		}



	}



}