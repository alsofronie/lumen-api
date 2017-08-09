<?php

namespace App\Providers;

use App\Exceptions\ApiException;
use App\Lib\JsonWebToken;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {
            $token = $this->getToken($request);
            if ($token) {
                try {
                    return JsonWebToken::decode($token);
                } catch (SignatureInvalidException $ex) {
                    throw new ApiException(1006, null, null, $ex);
                } catch (BeforeValidException $ex) {
                    throw new ApiException(1007, null, null, $ex);
                } catch (ExpiredException $ex) {
                    throw new ApiException(1008, null, null, $ex);
                } catch (\Exception $ex) {
                    throw new ApiException(1009, null, null, $ex);
                }
            }

            throw new ApiException(1005);
        });
    }

    protected function getToken(Request $request)
    {
        $header = $request->header('Authorization');
        if ($header && starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }

        if ($request->has('_api_token')) {
            return $request->input('_api_token');
        }

        return null;
    }
}
