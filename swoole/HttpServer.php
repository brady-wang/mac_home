<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2019/9/13
 * Time: 21:00
 */

class HttpServer
{
	public $http_serv;

	const PORT = 9501;
	const IP = "0.0.0.0";

	public $static_handel_switch = true;
	public $document_root = '/www/swoole';

	public function __construct()
	{
		$this->http_serv = new Swoole\Http\Server(self::IP,self::PORT);

		$this->http_serv->on("request",[$this,"onRequest"]);

		if($this->static_handel_switch == true){
			$this->enableStaticHandel($this->document_root);
		}
	}


	/**
	 * 接受到客户端请求
	 * @param $request 请求
	 * @param $response 响应
	 */
	public function onRequest($request,$response)
	{


		//var_dump($request->server['request_uri']);
		if($request->server['request_uri'] != "/favicon.ico"){


			$headers = [
				'Content-Type'=>"text/html; charset=utf-8"
			];

			$this->setHeader($headers,$response);

			$response->end("<h1>hello swoole ".rand(100,999)."</h1>");
		}


	}

	public function setHeader(array $headers ,$response)
	{
		foreach($headers as $key=>$header){
			$response->header($key,$header);
		}
	}

	/**
	 * 启动
	 */
	public function start()
	{
		$this->http_serv->start();
	}

	public function enableStaticHandel($document_root)
	{
		$this->http_serv->set(
			[
				'document_root' =>  $document_root, // v4.4.0以下版本, 此处必须为绝对路径
				'enable_static_handler' => true,
			]
		);
	}
}

$http = new HttpServer();
$http->start();