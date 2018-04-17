websocket
====================

 Websocket 基于[swoole] (http://www.swoole.com/). 实现
 ---------------------

### 使用时候用到的接口

+注册获取用户session_id
>http://{base_url}:{port}/regist/{platform_id}/{$user_id}/{$timeout}
实例：curl http://39.104.113.184:8889/regist\?1/1

+推送消息
>http://{base_url}:{port}/push/{platform_id}/{$user_id}/[{message}]
实例：curl  http://39.104.113.184:8889/push\?data\=14141\&session_id\=sid_4a8a7a6c85b20a0bf080df0abcf21f32

>也可以通过POST message 传递，优先前者

+强制T下线
>http://{base_url}:{port}/out/{platform_id}/{$user_id}
实例：curl http://39.104.113.184:8889/close\?1/1
