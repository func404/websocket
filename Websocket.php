<?php

class Websocket
{

    private $ws;

    private $request;

    private $parameters;

    public function __construct($webSocketServer = null, $request = null)
    {
        $this->ws = $webSocketServer;
        $this->request = $request;
    }

    /**
     * server
     * 注册获取session_id , 服务器发起
     */
    public function regist()
    {
        $platform_id = $user_id = $token = 0;
        
        if (! $this->check()) {
            return json('Access deny!'); /* 禁止访问 */
        }
        $sessionId = set_sid($platform_id, $user_id);
        return json(0, [
            'session_id' => $sessionId /* !!! 不带前缀 */
        ]);
    }

    /**
     * webScokect 登录操作，浏览器发起
     *
     * @param string $session_id            
     *
     */
    public function login()
    {
        $session_id = 0;
        
        $fd = 0;
        $session_id = 0;
        $platFormUser = get_uid($session_id);
        if ($platFormUser) {
            $platforms = Config::PLATFORM;
            if (set_fid($session_id, $fd, $platforms[$platFormUser['platform_id']]['timeout'])) {
                return 0;
            } else {
                return 1;
            }
        } else {
            return 'FAILED';
        }
    }

    /**
     * server
     * 发送push消息
     */
    public function push()
    {
        $platform_id = $user_id = $token = $message = 0;
        
        ;
    }

    /**
     * browser
     * 客户端发起关闭
     */
    public function close()
    {
        ;
    }

    /**
     * server
     * 服务器端断开
     */
    public function out()
    {
        $platform_id = $user_id = $token = 0;
        
        // 断开 close
        // 清除缓存
    }

    /**
     * 验证server请求
     */
    private function check($request = '')
    {
        ;
    }
}