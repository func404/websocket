
websocket
====================

 Websocket 基于[swoole] (http://www.swoole.com/). 实现
 ---------------------
 
#### +注册获取用户session_id
>
>http://{base_url}:{port}/regist/{platform_id}/{$user_id}/{$timeout}

#### +推送消息
>http://{base_url}:{port}/push/{platform_id}/{$user_id}/[{message}]
>也可以通过POST message 传递，优先前者

#### +强制T下线
>http://{base_url}:{port}/out/{platform_id}/{$user_id}

+ Candy.
+ Gum.
+ Booze.