## aliyun gateway backend auth

### Installation

```shell script
composer require cblink/laravel-aliyun-gateway-backend-auth
```

### Configuration

you need to copy `config/aliyun-gateway.php` file to config folder。Here are some examples

##### aliyun-gateway.php
```php
<?php

return [
    // aliyun gateway signature key 
    'key' => '',
    // aliyun gateway signature secret 
    'secret' => '',
    // 定义需要从网关中国转换到user的信息，暂时只支持header方式
    // 格式  [ auth()->{key} => {header key} ]
    'users' => [
        // 以下部分根据实际接口的系统参数定义，如没有在控制台中配置，以下信息可能为null
        'id' => 'x-app-id',
        'client_ip' => 'x-app-client-ip',
        'domain' => 'x-app-domain',
        'request_id' => 'x-app-request-id',
        'ua' => 'x-app-client-ua',
    ]
];
```

This Laravel auth driver. You can easily set your auth driver to aliyun-gateway-api

##### auth.php

```php
<?php

return [
    // ... 
    'guards' => [
        // 
        'custom' => [
            'driver' => 'aliyun-gateway-api'
        ]
    ]
];
```

