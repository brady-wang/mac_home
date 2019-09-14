<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2019/9/14
 * Time: 10:51
 */

$http = new Swoole\Http\Server("0.0.0.0", 8888);


$http->set([
	'document_root' => '/www/swoole', // v4.4.0以下版本, 此处必须为绝对路径
	'enable_static_handler' => true,
]);

$http->on('request', function ($request, $response) {
	if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
		return $response->end();
	}
	var_dump($request->get, $request->post);
	$response->header("Content-Type", "text/html; charset=utf-8");
	$response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
});

$http->start();