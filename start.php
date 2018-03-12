<?php
include_once 'Config.php';
include_once 'Websocket.php';
include_once 'helpers.php';
include_once 'Cache.php';

$webSocketServer = new swoole_websocket_server("0.0.0.0", Config::SOCKET_PORT);

//监听websocket请求
$webSocketServer->on('open', function ($webSocketServer, $request) {
    $session_id =  $request->server['query_string'];
    $websocket = new Websocket($webSocketServer, $request);
   echo "建立连接处："; var_dump($request->fd);
    $result = $websocket->login($session_id,$request->fd);
    if ($result) {
        return $webSocketServer->push($request->fd, json(0));
    } else {
        $webSocketServer->push($request->fd, json('session_id not exist'));
        $webSocketServer->close($request->fd);
    }
});
// 监听HTTP请求
$webSocketServer->on('Request', function ($request, $respone) use ($webSocketServer) {
     $websocket = '';
    if ($request->server['request_uri'] == '/favicon.ico') {
        $respone->end(null);
    } else if($request->server['request_uri'] == '/regist'){
        $websocket = new Websocket($webSocketServer, $request); //建立连接
        if (isset($request->cookie['PHPSESSID'])) {
            $userId = $request->cookie['PHPSESSID'];
        }else{
            $userId = 0;
        }
        $data = $websocket->regist($request->server['query_string'],$userId);
        return $respone->end($data); //向浏览器发送内容
     } else if($request->server['request_uri'] == '/push'){
        $websocket = new Websocket($webSocketServer, $request); //建立连接
        $websocket->push($request->server['query_string']);

    }
});

// 监听WebSocket消息事件
$webSocketServer->on('message', function ($webSocketServer, $frame) {
    echo "接收消息处";
     var_dump($frame);//
});

// 监听WebSocket连接关闭事件
$webSocketServer->on('close', function ($webSocketServer, $fd) {
    // $websocket = new Websocket($webSocketServer);
    // $websocket->close();
    echo "关闭连接处";
    var_dump($fd);
    $webSocketServer->close($fd);
});

$webSocketServer->set([
    'worker_num' => Config::WORKER_NUM,
    'daemonize'=>false,
    // 'daemonize' => Config::DAEMONIZE,
    'backlog' => Config::BACKLOG
]);
// Config::REDIS_PORT
$redis = Cache::getInstance(Config::REDIS_HOST,6379,Config::REDIS_AUTH);
foreach (Config::PLATFORMS as $key => $value) {
  $redis->hset('wlxs_websocket_platforms',$key,json_encode($value));
}

$webSocketServer->start();

