<?php


namespace app\controller;



use core\lib\log;

class index extends \core\base
{
	public function index()
	{
		$model = new \app\model\user;
//		$sql = "select * from user limit 2";
//		$query = $model->query($sql);
//		$res = $query->fetchAll();
//		dump($res);

		$data = $model->get_one('user',"*");
		dump($data);
	}

	public function hello()
	{
		$data = ['name'=>'wang'];
		$title = '标题';
		$this->assign('data',$data);
		$this->assign('title',$title);
		$this->display('index/hello');
	}

	public function config()
	{
		$res = \core\lib\config::get('author');
		dump($res);
	}

	public function log()
	{

		log::log('ddddd','server');

	}
}