<?php

/**
 * https://www.cnblogs.com/weafer/archive/2011/09/21/2184059.html
 * @author duxin
 *
 */
class Cache
{

    private static $handler = null;

    private static $_instance = null;

    private function __construct($host = '127.0.0.1', $port = 6379, $auth = '', $persistent = false)
    {
        $func = $persistent ? 'pconnect' : 'connect'; // 长链接
        self::$handler = new \Redis();
        self::$handler->$func($host, $port);
        
        if ('' != $auth) {
            self::$handler->auth($auth);
        }
        return self::$handler;
    }

    /**
     *
     * @return RedisPackage|null 对象
     */
    public static function getInstance($host = '127.0.0.1', $port = 6379, $auth = '', $persistent = false)
    {
        if (! (self::$_instance instanceof self)) {
            self::$_instance = new self($host, $port, $auth, $persistent);
        }
        return self::$_instance::$handler;
    }

    /**
     * 禁止外部克隆
     */
    public function __clone()
    {
        trigger_error('Clone is not allow!', E_USER_ERROR);
    }
}
