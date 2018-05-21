websocket
====================

 Websocket 基于[swoole] (http://www.swoole.com/). 实现
 ---------------------

### 使用时候用到的接口

+注册获取用户session_id
>http://{base_url}:{port}/regist/{platform_id}/{$user_id}/{$timeout}
>http://39.104.113.184:37023/regist\?1\1 

+推送消息
>http://{base_url}:{port}/push/{platform_id}/{$user_id}/[{message}]
> http://39.104.113.184:37023/push?user_id=2&platform_id=6&data=134141341sf

>也可以通过POST message 传递，优先前者

+强制T下线
>http://{base_url}:{port}/out/{platform_id}/{$user_id}
