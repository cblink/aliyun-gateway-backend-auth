<?php
namespace Cblink\AliyunGateway\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * Class RequestValidate
 * @package Cblink\Service\Auth
 */
class RequestValidate
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * @var AuthUser
     */
    protected $user;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var array
     */
    protected $userKeys;

    /**
     * GatewayGuard constructor.
     * @param Request $request
     * @param $key
     * @param $secret
     * @param $userKeys
     */
    public function __construct(Request $request, string $key,  string $secret, array $userKeys)
    {
        $this->request = $request;
        $this->key = $key;
        $this->secret = $secret;
        $this->userKeys = $userKeys;
    }

    /**
     * @return bool
     */
    public function check()
    {
        if ($this->request->header('x-ca-proxy-signature')){
            // 获取到待验证的签名
            $signature = $this->request->header('x-ca-proxy-signature');

            // 获取需要验证的头信息
            $headers = Arr::only(
                $this->request->headers->all(),
                explode(",", $this->request->header('x-ca-proxy-signature-headers'))
            );

            ksort($headers);

            $headerString = "";

            foreach ($headers as $key => $val){
                $headerString = $key.':'.$val[0]."\n";
            }

            // 获取所有参数
            $params = $this->sortByArray($this->request->query());

            // 获取延签的url
            $url = empty($params) ?
                $this->request->getPathInfo() :
                $this->request->getPathInfo() . '?' . http_build_query($params);

            $md5Content = base64_encode(md5($this->request->getContent(), true));

            // 待签名字符串
            $signString = $this->request->getMethod() . "\n" .
                ($this->request->getContent() ?: $md5Content) . "\n" .
                (!empty($headerString) ? $headerString : "\n") .
                urldecode($url);

            // 生成待验证的签名
            $matchSignature = base64_encode(hash_hmac('SHA256', $signString, $this->secret, true));

            if ($matchSignature == $signature){
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $array
     * @return array
     */
    protected function sortByArray(array $array = []) : array
    {
        foreach ($array as $key => $val){
            if (is_array($val)){
                $array[$key] = $this->sortByArray($val);
            }
        }
        ksort($array);
        return $array;
    }

    /**
     * @return AuthUser
     */
    public function user()
    {
        $user = array_map(function($val){
            return $this->request->header($val);
        }, $this->userKeys);

        return new AuthUser($user);
    }
}
