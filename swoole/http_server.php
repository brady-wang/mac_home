<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2019/9/13
 * Time: 15:00
 */

$http = new Swoole\Http\Server("0.0.0.0", 9501);

$http->on('request', function ($request, $response) {
	var_dump($request->get, $request->post);
	$response->header("Content-Type", "text/html; charset=utf-8");
	$response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
});

$http->start();