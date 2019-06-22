<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2019/3/29
 * Time: 22:24
 */

namespace App\Controllers;


class UserController
{
	public $name;
	const PI=3.14;

	public function __construct()
	{
		$this->name = '222';
	}

	public function getName()
	{
		return $this->name;
	}

	public function __destruct()
	{
		// TODO: Implement __destruct() method.
	}
}

