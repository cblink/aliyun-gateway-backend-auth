<?php
namespace Cblink\AliyunGateway\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

/**
 * Class GatewayAuthServiceProvider
 * @package Cblink\Service\Auth
 */
class GatewayAuthServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $key = $this->app->config->get('aliyun-gateway.key', '');
        $secret = $this->app->config->get('aliyun-gateway.secret', '');
        $userKeys = $this->app->config->get('aliyun-gateway.users', []);
        Auth::extend('gateway-api', function($app) use($key, $secret, $userKeys){
            return new GatewayGuard($app->request, $key,  $secret, $userKeys);
        });
    }
}
