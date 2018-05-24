<?php
include_once 'Config.php';
include_once 'Websocket.php';
include_once 'helpers.php';
include_once 'Cache.php';
$webSocketServer = new swoole_websocket_server("0.0.0.0", Config::SOCKET_PORT);
#$webSocketServer = new swoole_websocket_server("0.0.0.0", Config::SOCKET_PORT,SWOOLE_BASE,SWOOLE_SOCK_TCP | SWOOLE_SSL);
#$webSocketServer->set([
#'ssl_cert_file'=>'/etc/nginx/ssl/cert/214603950180146.pem',
#'ssl_key_file'=>'/etc/nginx/ssl/cert/214603950180146.key'
#]);

//监听websocket请求
$webSocketServer->on('open', function ($webSocketServer, $request) {
    $session_id =  $request->server['query_string'];
    $websocket = new Websocket($webSocketServer, $request);
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
        $data = $websocket->regist($request->server['query_string']);
        return $respone->end($data); //向浏览器发送内容
     } else if($request->server['request_uri'] == '/push'){
        $websocket = new Websocket($webSocketServer, $request); //建立连接
          $result =  $websocket->push($request->server['query_string']);
         error_log("\n".var_dump($result)."\n",3,'test.txt');
     }else if ($request->server['request_uri'] == '/close') {
        $websocket = new Websocket($webSocketServer, $request);
       $fd =  $websocket->out($request->server['query_string']);
       $webSocketServer->close($fd);
     }
});

// 监听WebSocket消息事件
$webSocketServer->on('message', function ($webSocketServer, $frame) {
    //不处理接收的消息
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
    'daemonize'=>false,
    'daemonize' => Config::DAEMONIZE,
    'backlog' => Config::BACKLOG
]);
// Config::REDIS_PORT
$redis = Cache::getInstance(Config::REDIS_HOST,Config::REDIS_PORT,Config::REDIS_AUTH);
foreach (Config::PLATFORMS as $key => $value) {
  $redis->hset('wlxs_websocket_platforms',$key,json_encode($value));
}

$webSocketServer->start();

