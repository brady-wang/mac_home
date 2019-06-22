<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2018/11/17
 * Time: 08:43
 */

//创建websocket服务器对象，监听0.0.0.0:9502端口
$ws = new swoole_websocket_server("0.0.0.0", 9502);

//监听WebSocket连接打开事件
$ws->on('open', function ($ws, $request) {
	var_dump($request->fd);
	$ws->push($request->fd, "hello, welcome\n");
	swoole_timer_after(5000, function() use($ws,$request){
		$ws->push($request->fd, "hello, welcome\n");
	});

});

//监听WebSocket消息事件
$ws->on('message', function ($ws, $frame) {
	echo "Message: {$frame->data}\n";

	$ws->push($frame->fd, "server: {$frame->data}");
});

//监听WebSocket连接关闭事件
$ws->on('close', function ($ws, $fd) {
	echo "client-{$fd} is closed\n";
});

$ws->start();