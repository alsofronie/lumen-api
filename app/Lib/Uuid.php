<?php

namespace App\Lib;

/**
 * A simple UUID4 only class generator.
 * Heavily inspired from webpatser/laravel-uuid
 * @see https://github.com/webpatser/laravel-uuid
 *
 * @package App\Lib
 */
class Uuid
{
    protected static $randomGenerator = null;

    protected $uuid;

    public function __construct($uuid = null)
    {
        if ($uuid && ctype_print($uuid)) {
            $uuid = hex2bin($uuid);
        }
        $this->uuid = $uuid ? $uuid : static::create();
    }

    public function __toString()
    {
        return bin2hex($this->uuid);
    }

    public static function create()
    {
        $uuid = call_user_func(static::getRandomGenerator(), 16);
        $uuid[8] = chr(ord($uuid[8]) & 63 | 128);
        $uuid[6] = chr(ord($uuid[6]) & 63 | 64);

        return $uuid;
    }

    public function __get($key)
    {
        if ($key === 'bin') {
            return $this->uuid;
        } elseif ($key === 'str') {
            return bin2hex($this->uuid);
        } elseif ($key === 'uuid') {
            $u = bin2hex($this->uuid);
            return substr($u, 0, 8)
                . '-' . substr($u, 8, 4)
                . '-' . substr($u, 12, 4)
                . '-' . substr($u, 16, 4)
                . '-' . substr($u, 20)
            ;
        }

        throw new \InvalidArgumentException('Property ' . $key . ' not found');
    }

    protected static function getRandomGenerator()
    {
        if (null === static::$randomGenerator) {
            $generator = null;
            if (function_exists('random_bytes')) {
                static::$randomGenerator = static function ($bytes) {
                    return random_bytes($bytes);
                };
            } elseif (function_exists('openssl_random_pseudo_bytes')) {
                static::$randomGenerator = static function ($bytes) {
                    return openssl_random_pseudo_bytes($bytes);
                };
            } elseif (function_exists('mcrypt_create_iv')) {
                static::$randomGenerator = static function ($bytes) {
                    return mcrypt_create_iv($bytes, MCRYPT_DEV_URANDOM);
                };
            } else {
                static::$randomGenerator = static function ($bytes) {
                    $randBytes = "";
                    for ($i = 0; $i < $bytes; $i++) {
                        $randBytes .= chr(mt_rand(0, 255));
                    }

                    return $randBytes;
                };
            }
        }

        return static::$randomGenerator;
    }
}
