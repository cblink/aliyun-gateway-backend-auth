<?php
namespace Cblink\AliyunGateway\Auth;

use Illuminate\Auth\GenericUser;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * Class GatewayGuard
 * @package App\Test
 */
class GatewayGuard implements Guard
{
    use GuardHelpers;

    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

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
     * @var Authenticatable
     */
    protected $user;

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
     * @inheritDoc
     */
    /**
     * @return GenericUser|\Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if (!is_null($this->user)){
            return $this->user;
        }

        $user = null;

        if ($this->validate()){
            $user = array_map(function($val){
                return $this->request->header($val);
            }, $this->userKeys);

            $user = new GenericUser($user);
        }

        return $this->user = $user;
    }

    /**
     * @inheritDoc
     */
    public function validate(array $credentials = [])
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

            $content = "";

            if (!empty($this->request->getContent()) && $this->request->getContent() != '[]') {
                $content = $this->md5Content($this->request->getContent());
            }

            // 待签名字符串
            $signString = $this->request->getMethod() . "\n" .
                $content . "\n" .
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
     * @param $content
     * @return string
     */
    public function md5Content($content)
    {
        return base64_encode(md5($content, true));
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
}
