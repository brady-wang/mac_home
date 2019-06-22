<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2018/12/4
 * Time: 12:20
 */
namespace core\lib;

use core\lib\config;

class log
{
	/**
	 * 1 日志存储方式
	 *
	 * 2 写日志
	 *
	 */

	static $class;

	static  public function init()
	{
		//确定存储方式
		$drive = config::get('drive','log');
		$class = '\core\lib\drive\log\\'.$drive;
		self::$class = new $class();
	}

	static public function log($message,$file='log')
	{

		self::$class->log($message,$file);
	}


}