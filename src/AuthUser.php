<?php
namespace Cblink\AliyunGateway\Auth;

use Illuminate\Config\Repository;
use InvalidArgumentException;

/**
 * Class User
 * @property $id
 * @package Cblink\Service\Auth
 */
class AuthUser extends Repository
{
    /**
     * @return mixed
     */
    public function id()
    {
        return $this->offsetGet(__METHOD__);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if ($value = $this->offsetGet($key)){
            return $value;
        }
        throw new InvalidArgumentException('gateway user "' . $key . '" is not defined');
    }
}
