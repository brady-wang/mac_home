<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2019/3/31
 * Time: 08:37
 */

namespace App\Controllers;


class Test
{
	public function test_array_filter()
	{
		$arr = ['name','age',"",null,false,true];
		$res = array_filter($arr);
		dump($res);

	}
}