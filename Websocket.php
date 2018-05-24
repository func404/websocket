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
    public function regist($message)
    {   
        $mesarr =  explode('/',$message);
        if (count($mesarr)<1) {
           return false;
        }
        $platform_id = $mesarr[0];
        $user_id=$mesarr[1];
        $token=0;

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
    public function login($session_id=0,$fd)
    {
        // $session_id = 0;
        // $fd = 0;
        // $session_id = 0;
        $platFormUser = get_uid($session_id);
        if ($platFormUser) {
            $platforms = Config::PLATFORMS;
            if (set_fid($session_id, $fd, $platforms[$platFormUser['platform_id']]['timeout'])) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * server
     * 发送push消息
     */
    public function push($data)
    {   
        $platform_id = $user_id = $token = $message = 0;

        $dataArr = [];
        parse_str($data,$dataArr);
        $session_id = getSidByUid($dataArr['user_id'],$dataArr['platform_id']);
        $fdstr = cache_get('f_'.$session_id);
        if ($fdstr) {
              $fdArr = explode('_',$fdstr);
            $fd = $fdArr[1];
            error_log("\nsession_id=>$session_id\n".json_encode($dataArr),3,'./test.txt');
            return $this->ws->push($fd, $dataArr['data']); 
        }else{
            return false;
        }
     
    }

    /**
     * browser
     * 客户端发起关闭   清除缓存信息
     */
    public function close($fd)
    {
       get_sid($fd);
    }

    /**
     * server
     * 服务器端断开
     */
    public function out($message)
    {
        $mesarr =  explode('/',$message);
        if (count($mesarr)<2) {
           return false;  
        }
        $platform_id = $mesarr[0];
        $user_id=$mesarr[1];

        $session_id = cache_get('uid_'.$platform_id.'_'.$user_id);
        if ($session_id) {
           $fd = get_fid($session_id);
            get_sid($fd);
         return $fd;
        }
       return false;
    }

    /**
     * 验证server请求
     */
    private function check($request = '')
    {
        return true;
    }
}
