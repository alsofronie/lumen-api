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
        if (isset(static::$codes[$code])) {
            return static::$codes[$code]['message'];
        }

        return 'error';
    }

    protected static function statusFromCode($code)
    {
        if (isset(static::$codes[$code])) {
            return static::$codes[$code]['http'];
        }

        return 500;
    }

    protected static function typeFromCode($code)
    {
        if (isset(static::$codes[$code])) {
            return static::$codes[$code]['type'];
        }

        return 500;
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
