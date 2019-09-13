<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2019/9/13
 * Time: 20:33
 */

class TcpServer
{
	const IP = "0.0.0.0";
	const PORT = 9501;

	public $serv;


	//创建Server对象，监听 本机9501端口
	public function __construct()
	{

		$this->serv = new Swoole\Server(self::IP, self::PORT);

		$this->serv->on("Connect",[$this,"onConnect"]);
		$this->serv->on("Receive",[$this,"onReceive"]);
		$this->serv->on("Close",[$this,"Onclose"]);
	}

	/**
	 * 客户端连接触发
	 * @param $serv 服务器信息
	 * @param $fd 客户端标识
	 */
	public function onConnect($serv,$fd)
	{
		echo "客户端连接:".$fd.PHP_EOL;
	}

	/**
	 * 收到客户端信息时候触发
	 * @param $serv 服务器信息
	 * @param $fd 客户端标识
	 * @param $reactor_id 线程ID
	 * @param $data 接受到的数据
	 */
	public function onReceive($serv,$fd,$reactor_id,$data)
	{
		echo "服务器接受到客户端-".$fd."-数据".$data." 线程ID-".$reactor_id.PHP_EOL;
	}

	/**
	 * @param $serv 服务器信息
	 * @param $fd 客户端标识
	 */
	public function onClose($serv,$fd)
	{
		echo "客户端-".$fd."-关闭连接".PHP_EOL;
	}

	/**
	 * @param $config  配置
	 */
	public function set(array $config)
	{
		$this->serv->set($config);
	}

	public function start()
	{
		$this->serv->start();
	}
}

$tcp = new TcpServer();
$tcp->set(array(
	'reactor_num' => 2, //reactor thread num
	'worker_num' => 4,    //worker process num
	'backlog' => 128,   //listen backlog
	'max_request' => 50,
	'dispatch_mode' => 1));
$tcp->start();