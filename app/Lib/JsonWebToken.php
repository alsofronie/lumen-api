<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 08/08/2017
 * Time: 22:14
 */

namespace App\Lib;

use App\Exceptions\ApiException;
use App\Models\User;
use Firebase\JWT\JWT;

class JsonWebToken
{
    public static function encode(User $user, $iat = null, $exp = null)
    {
        $iat = $iat ? $iat : time();
        $exp = $exp ? $exp : ($iat + 12 * 60 * 60); // 12 hours
        $payload = [
            'iss' => env('JWT_ISS', url('/')),
            'aud' => env('JWT_AUD', '*'),
            'iat' => $iat,
            'nbf' => ($iat - 1),
            'exp' => $exp,
            'uid' => $user->uuid,
        ];

        $key = static::getJwtKey();
        $alg = static::getJwtAlg();

        return JWT::encode($payload, $key, $alg);
    }

    public static function decode($token)
    {
        $key = static::getJwtKey();
        $alg = static::getJwtAlg();

        $payload = JWT::decode($token, $key, [$alg]);

        return User::find(hex2bin($payload->uid));
    }

    protected static function getJwtKey()
    {
        $key = env('JWT_KEY');
        if (!$key) {
            $key = env('APP_KEY');
        }

        // @codeCoverageIgnoreStart
        if (!$key) {
            throw new ApiException(899, null, 'Configuration error');
        }
        // @codeCoverageIgnoreEnd

        return $key;
    }

    protected static function getJwtAlg()
    {
        return env('JWT_ALG', 'HS256');
    }
}
