<?php

return [
    'key' => '',
    'secret' => '',
    // 定义需要从网关中国转换到user的信息
    'users' => [
        // 以下部分根据实际接口的系统参数定义，如没有在控制台中配置，以下信息可能为null
        'id' => 'x-app-id',
        'request_id' => 'x-app-request-id',
    ]
];
