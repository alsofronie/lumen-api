<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 05/08/2017
 * Time: 19:26
 */

namespace App\Exceptions;

class ApiException extends \Exception
{
    protected static $codes = [
        1001 => ['http' => 406, 'message' => 'Empty header', 'type' => 'empty_header'],
        1002 => ['http' => 406, 'message' => 'Invalid header', 'type' => 'invalid_header'],
        1003 => ['http' => 403, 'message' => 'Invalid credentials', 'type' => 'authentication'],
        1004 => ['http' => 403, 'message' => 'Invalid credentials', 'type' => 'authentication'],
        1005 => ['http' => 401, 'message' => 'Missing token', 'type' => 'authentication'],
        1006 => ['http' => 403, 'message' => 'Invalid signature', 'type' => 'authentication'],
        1007 => ['http' => 403, 'message' => 'Invalid token', 'type' => 'authentication'],
        1008 => ['http' => 403, 'message' => 'Expired token', 'type' => 'authentication'],
        1009 => ['http' => 403, 'message' => 'Token error', 'type' => 'authentication'],
    ];

    protected $details = null;
    protected $type = null;
    protected $status = 500;

    public function __construct($code, $details = null, $message = null, \Throwable $previous = null)
    {
        $message = $message ? $message : static::messageFromCode($code);
        parent::__construct($message, $code, $previous);

        $this->status = static::statusFromCode($code);
        $this->type = static::typeFromCode($code);
        $this->details = $details;
    }

    protected static function messageFromCode($code)
    {
        if (!isset(static::$codes[$code])) {
            // @codeCoverageIgnoreStart
            return 'error';
            // @codeCoverageIgnoreEnd
        }

        return static::$codes[$code]['message'];
    }

    protected static function statusFromCode($code)
    {
        if (!isset(static::$codes[$code])) {
            // @codeCoverageIgnoreStart
            return 500;
            // @codeCoverageIgnoreEnd
        }

        return static::$codes[$code]['http'];
    }

    protected static function typeFromCode($code)
    {
        if (!isset(static::$codes[$code])) {
            // @codeCoverageIgnoreStart
            return 'unknown';
            // @codeCoverageIgnoreEnd
        }

        return static::$codes[$code]['type'];
    }

    public function getStatusCode()
    {
        return $this->status;
    }

    public function getType()
    {
        return $this->type;
    }

    public function hasDetails()
    {
        return (!empty($this->details));
    }

    public function getDetails()
    {
        return $this->hasDetails() ? $this->details : null;
    }
}
