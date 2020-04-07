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
        Auth::viaRequest('gateway-api', function($request) use($key, $secret, $userKeys){
            // 进行参数验证
            $validate = new RequestValidate($request, $key,  $secret, $userKeys);

            if ($validate->check()){
                return $validate->user();
            }

            return null;
        });
    }
}
