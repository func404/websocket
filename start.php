<?php
include_once 'Config.php';
include_once 'Websocket.php';
include_once 'helpers.php';

$webSocketServer = new swoole_websocket_server("127.0.0.1", Config::SOCKET_PORT);

$webSocketServer->on('open', function ($webSocketServer, $request) {
    $websocket = new Websocket($webSocketServer, $request);
    $result = $websocket->login();
    if ($result) {
        return json(0);
    } else {
        $webSocketServer->push($request->fd, json('session_id not exist'));
        $webSocketServer->close($request->fd);
    }
});
// 监听HTTP请求
$webSocketServer->on('Request', function ($request, $respone) use ($webSocketServer) {
    if ($request->server['request_uri'] == '/favicon.ico') {
        $respone->end(null);
    } else {
        $websocket = new Websocket($webSocketServer, $request);
        $data = $websocket->regist();
        return $respone->end($data);
    }
});

// 监听WebSocket消息事件
$webSocketServer->on('message', function ($webSocketServer, $frame) {
    ;
});

// 监听WebSocket连接关闭事件
$webSocketServer->on('close', function ($webSocketServer, $fd) {
    $websocket = new Websocket($webSocketServer);
    $websocket->close();
    $webSocketServer->close($fd);
});

$webSocketServer->set([
    'worker_num' => Config::WORKER_NUM,
    'daemonize' => Config::DAEMONIZE,
    'backlog' => Config::BACKLOG
]);

$webSocketServer->start();