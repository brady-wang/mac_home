<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2019/3/31
 * Time: 08:37
 */

namespace Core;


use App\Controllers\Test;

class Run
{
	public static function init()
	{

		$arr = ['name','age',"",null,false,true];
		$res = array_filter($arr);
		dump($res);



	}

}