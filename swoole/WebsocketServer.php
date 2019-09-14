<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2019/9/14
 * Time: 09:41
 */

class WebsocketServer
{

	public $serv;

	const IP = '0.0.0.0';
	const PORT = 9502;

	public function __construct()
	{
		$this->serv = new Swoole\WebSocket\Server(self::IP,self::PORT);

		$this->serv->set([
			'reactor_num' => 2, //reactor thread num
			'worker_num' => 4,    //worker process num
			'task_worker_num' => 4,    //worker process num
			'backlog' => 128,   //listen backlog
		]);

		$this->serv->on('Open',[$this,"onOpen"]);
		$this->serv->on('Message',[$this,"onMessage"]);
		$this->serv->on('Close',[$this,"onClose"]);

		$this->serv->on("Task",[$this,"onTask"]);
		$this->serv->on("Finish",[$this,"onFinish"]);
	}

	public function onOpen($server,$request)
	{
		echo "客户端{$request->fd}连接成功".PHP_EOL;
	}

	public function onMessage($server,$frame)
	{
		echo "服务端接收到客户端-{$frame->fd}-数据".PHP_EOL;
		print_r($frame->data);

		$data = [
			'id'=>1,
			'name'=>'brady'
		];
		$server->task($data);

		$server->push($frame->fd," 服务器已经接收到你的数据了".json_encode($frame->data));
	}

	public function onClose($server,$fd)
	{
		echo "客户端断开链接-".$fd.PHP_EOL;
	}

	public function start()
	{
		$this->serv->start();
	}

	public function onTask($server,$task_id,$worker_id,$data)
	{
		echo "接收到投递 ".$task_id." {$worker_id} 数据为".json_encode($data).PHP_EOL;
		//耗时操作
		sleep(19);
		return $task_id." 执行成功";
	}

	public function onFinish($server,$task_id,$res)
	{

		echo  $res;
	}
}

$ws = new WebsocketServer();
$ws->start();