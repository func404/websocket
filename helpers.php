<?php
/**
 * 帮助函数
 *
 * @author Du Xin <erntoo@126.com>
 * @todo 自定义帮助函数
 * @version
 */
date_default_timezone_set('PRC'); // 设置中国时区

if (! function_exists('cache_set')) {

    /**
     *
     * @param string $key            
     * @param string $value            
     * @param int|\Datetime $expire
     *            有效时长:以分钟为单位,或者有效期至： 默认为0 不过期 ;
     */
    function cache_set($key, $value, $expire = 3600)
    {
        $cache = Cache::getInstance(Config::REDIS_HOST, Config::REDIS_PORT, Config::REDIS_AUTH);
       error_log("\n".Config::REDIS_PORT."\n".$key."\n".$value."\n".gettype($cache)."\n",3,"/data/www/websocket/test.txt"); 
     if ($expire) {
            return $cache->set(Config::CACHE_PREFIX . $key, $value, $expire);
        } else {
            return $cache->set(Config::CACHE_PREFIX . $key, $value);
        }
    }
}

if (! function_exists('cache_get')) {

    /**
     * 获取值
     *
     * @param string $key            
     * @return boolean
     */
    function cache_get($key)
    {
        $cache = Cache::getInstance(Config::REDIS_HOST, Config::REDIS_PORT, Config::REDIS_AUTH);
        return $cache->get(Config::CACHE_PREFIX . $key);
    }
}

if (! function_exists('cache_flush')) {

    /**
     * 注意安全!!!!!!!
     * 会清除所有cache
     *
     * @param string $key            
     * @param
     *            boolean 清空所有 cache
     */
    function cache_flush()
    {
        $cache = Cache::getInstance(Config::REDIS_HOST, Config::REDIS_PORT, Config::REDIS_AUTH);
        return (bool) \Redis::flush();
    }
}

if (! function_exists('cache_has')) {

    /**
     *
     * @param string $key            
     * @param string $prefix            
     * @return boolean
     */
    function cache_has($key)
    {
        $cache = Cache::getInstance(Config::REDIS_HOST, Config::REDIS_PORT, Config::REDIS_AUTH);
        return $cache->has(Config::CACHE_PREFIX . $key);
    }
}

if (! function_exists('cache_unset')) {

    /**
     * 根据key清除缓存
     *
     * @param string $key            
     * @return boolean
     */
    function cache_unset($key)
    {
        $cache = Cache::getInstance(Config::REDIS_HOST, Config::REDIS_PORT, Config::REDIS_AUTH);
        return $cache->del(Config::CACHE_PREFIX.$key);
    }
}

if (! function_exists('set_sid')) {

    function set_sid($platform_id, $user_id, $timeout = 0)
    {
        $userId = 'uid_' . $platform_id . '_' . $user_id; // 保存用户ID
        $sessionId = 'sid_' . md5($userId . rand(0, 99999999999)); // 保存用户session ID
    
         if (cache_get($userId)) {
           cache_unset(cache_get($userId)); 
         }
        if (cache_set($userId, $sessionId, $timeout) && cache_set($sessionId, $userId, $timeout)) {
            return $sessionId;
        } else {
            return false;
        }
    }
}

if (! function_exists('getSidByUid')) {
     function getSidByUid($user_id,$platform_id)
    {
       return cache_get('uid_' . $platform_id . '_' . $user_id);
    }
}

if (! function_exists('get_sid')) {

    function get_sid($fd)
    {
       $fdstr = cache_get('fd_'.$fd);
       if (!$fdstr) {
          return false;
       }
       $session_id = explode('f_',$fdstr)[1];
       $uid = cache_get($session_id);
       cache_unset('fd_'.$fd);  //删除客户端编号
       cache_unset($uid);//删除uid
       cache_unset($session_id);//删除session_id
       cache_unset('f_'.$session_id);//删除
    }
}

if (! function_exists('get_uid')) {

    function get_uid($session_id)
    {
        $userId = cache_get($session_id);
        if ($userId) {
            $arr = explode('_', $userId);
            return [
                'platform_id' => $arr[1],
                'user_id' => $arr[2]
            ];
        } else {
            return false;
        }
    }
}

if (! function_exists('set_fid')) {

    function set_fid($session_id, $fd, $timeout)
    {
        $fd = 'fd_' . $fd;
        $fsession_id = 'f_'.$session_id;
        if (cache_get($fsession_id)) {
           cache_unset(cache_get($fsession_id)); 
         }
        if (cache_set($fd, $fsession_id, $timeout) && cache_set($fsession_id, $fd, $timeout)) {
            return $fsession_id;
        } else {
            return false;
        }
    }
}

if (! function_exists('get_fid')) {

    function get_fid($session_id)
    {
        $fds = cache_get('f_' . $session_id);
        if ($fds) {
            $arr = explode('_', $fds);
            return $arr[1];
        } else {
            return false;
        }
    }
}

if (! function_exists('json')) {

    function json($message = '',$data='')
    {
        if ($message) {
            return json_encode([
                'code' => 1,
                'message' => $message
            ]);
        } else {
            return json_encode([
                'code' => 0,
                'message' => 'success',
                'data' => $data
            ]);
        }
    }
}
