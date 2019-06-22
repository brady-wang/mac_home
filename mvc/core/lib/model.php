<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2018/12/4
 * Time: 09:59
 */

namespace core\lib;
use core\lib\config;


class model extends \Medoo\Medoo
{
	public function __construct()
	{
		$database = config::all('config_site');
//		$dsn = $database['dsn'];
//		$username = $database['username'];
//		$passwd = $database['passwd'];
//		try{
//			parent::__construct($dsn, $username, $passwd);
//		} catch (\PDOException $e){
//			dump($e->getMessage());
//		}

		try{
			parent::__construct($database);
		} catch (\Exception $e){
			dump($e->getMessage());
		}

	}

	public function get_one($table,$field)
	{
		$data = $this->select("user","*");
		return $data;
	}
}