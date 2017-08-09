<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 08/08/2017
 * Time: 22:08
 */

namespace App\Http\Controllers\V1;

use App\Exceptions\ApiException;
use App\Lib\JsonWebToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|max:100',
            'password' => 'required|min:6|max:100'
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            throw new ApiException(1003);
        }

        if (!app('hash')->check($request->input('password'), $user->password)) {
            throw new ApiException(1004);
        }

        $now = Carbon::now();
        $expires = Carbon::now()->addHours(12);

        $token = JsonWebToken::encode($user, $now->timestamp, $expires->timestamp);

        return response()->json([
            'type' => 'auth',
            'method' => 'header',
            'prefix' => 'Bearer ',
            'token' => [
                'value' => $token,
                'issued' => $now,
                'expires' => $expires,
            ],
            'user' => $user,
        ]);
    }
}
