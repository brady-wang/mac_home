<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2019/3/31
 * Time: 12:25
 */

trait Test{
	public $name = 'wang';

	public function getName()
	{
		echo $this->name;
	}
}

class User
{
	use Test;
	public function get()
	{
		echo "get";
	}
}

$a = new User();
$a->getName();