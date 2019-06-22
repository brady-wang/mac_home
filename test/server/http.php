<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2018/11/17
 * Time: 08:41
 */

$http = new swoole_http_server("192.168.33.30", 9501);

$http->set([
	'document_root' => '/www/test/html',
	'enable_static_handler' => true,
]);

$http->on('request', function ($request, $response) {
	var_dump($request->get, $request->post);
	$response->header("Content-Type", "text/html; charset=utf-8");
	$response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
});

$http->start();