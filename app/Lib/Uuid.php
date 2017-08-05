<?php

namespace App\Lib;

class Uuid
{
    protected static $randomGenerator = null;

    protected $uuid;

    public function __construct($uuid = null)
    {
        $this->uuid = $uuid ? $uuid : static::create();
    }

    public function __toString()
    {
        return '' . $this->uuid;
    }

    public static function create()
    {
        $uuid = call_user_func(static::getRandomGenerator(), 16);
        $uuid[8] = chr(ord($uuid[8]) & 63 | 128);
        $uuid[6] = chr(ord($uuid[6]) & 63 | 64);

        return $uuid;
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
