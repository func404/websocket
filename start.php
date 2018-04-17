<?php
include_once 'Config.php';
include_once 'Websocket.php';
include_once 'helpers.php';
include_once 'Cache.php';

$webSocketServer = new swoole_websocket_server("0.0.0.0", Config::SOCKET_PORT);
$webSocketServer->set([
'ssl_cert_file'=>'',
'ssl_key_file'=>''
]);

// 监听websocket请求
$webSocketServer->on('open', function ($webSocketServer, $request) {
    $session_id = $request->server['query_string'];
    $websocket = new Websocket($webSocketServer, $request);
    $result = $websocket->login($session_id, $request->fd);
    if ($result) {
        return $webSocketServer->push($request->fd, json(0));
    } else {
        $webSocketServer->push($request->fd, json('session_id not exist'));
        $webSocketServer->close($request->fd);
    }
});
// 监听HTTP请求
$webSocketServer->on('Request', function ($request, $response) use ($webSocketServer) {
    $websocket = '';
    if ($request->server['request_uri'] == '/favicon.ico') {
        $response->end(null);
    } else {
        $uri = $request->server['request_uri'];
        $routes = explode('/', $uri);
        $fun = $routes[0];
        array_shift($routes);
        $websocket = new Websocket($webSocketServer, $request);
        return call_user_func_array([
            $websocket,
            $fun
        ], $routes);
    }
});

// 监听WebSocket消息事件
$webSocketServer->on('message', function ($webSocketServer, $frame) {
    // 不处理接收的消息
});

// 监听WebSocket连接关闭事件
$webSocketServer->on('close', function ($webSocketServer, $fd) {
    echo "连接关闭时";
    var_dump($fd);
    $websocket = new Websocket($webSocketServer);
    $websocket->close($fd);
    // $webSocketServer->close($fd);
});

$webSocketServer->set([
    'worker_num' => Config::WORKER_NUM,
    'daemonize' => false,
    'daemonize' => Config::DAEMONIZE,
    'backlog' => Config::BACKLOG
]);
// Config::REDIS_PORT
$redis = Cache::getInstance(Config::REDIS_HOST, Config::REDIS_PORT, Config::REDIS_AUTH);
foreach (Config::PLATFORMS as $key => $value) {
    $redis->hset('wlxs_websocket_platforms', $key, json_encode($value));
}

$webSocketServer->start();

