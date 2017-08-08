<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 08/08/2017
 * Time: 14:55
 */

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use Closure;
use Illuminate\Http\Request;

class JsonHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $headers = $this->getHeaders($request);
        $mime = env('APP_MIME');
        foreach ($headers as $header => $value) {
            if (empty($value)) {
                throw new ApiException(1001, [
                    'header'   => $header,
                    'expected' => $mime,
                ]);
            }

            if (!str_contains($value, ['*/*', $mime])) {
                throw new ApiException(1002, [
                    'header' => $header,
                    'expected' => $mime,
                ]);
            }
        }

        return $next($request);
    }

    protected function getHeaders(Request $request)
    {
        $headers = [];
        $method = $request->method();
        $headers['accept'] = $request->header('Accept');
        if (!in_array($method, ['GET', 'HEAD'])) {
            $headers['content-type'] = $request->header('Content-Type');
        }

        return $headers;
    }
}
