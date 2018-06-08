<?php

final class Config
{

    const SOCKET_PORT = 37023;

    const WORKER_NUM = 8;

    const DAEMONIZE = true;

    const BACKLOG = 128;

    const CACHE_PREFIX = 'WLXXX_';

    const REDIS_HOST = '127.0.0.1';

    const REDIS_PORT = '6380';

    const REDIS_AUTH = '!@#qweASD2018';

    const IP_ALLOWS = [
        '127.0.0.1'
    ];

    const PLATFORMS = [
        1 => [
            /* 运行访问的IP范围，如果为空则不限制 */
            'ip_allows' => [],
            /*访问注册 注销接口时候需要传递的 token,如果为空则 不做限制*/
            'token' => '',
            /*默认的session_id 有效期 单位s*/
            'timeout' => 3600,
            /*平台创建时间*/
            'created_at' => 1322222232,
            /*失效时间 默认为0 不失效*/
            'expire_at' => 0,
            /* 已注册的 session_id 数量*/
            'current_clients' => '',
            /*当前连接的客户端数量*/
            'current_connections' => 25
        ]
    ];
}
