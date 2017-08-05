<?php

namespace App\Http\Middleware;

use Closure;

class AppendApiHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request)
            ->withHeaders([
                'X-API-VERSION' => env('APP_VERSION'),
                'X-API-MIME' => 'application/json'
            ])
        ;
    }
}
