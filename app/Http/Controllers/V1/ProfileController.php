<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 08/08/2017
 * Time: 23:05
 */

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;

class ProfileController extends BaseController
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Load any relationships you need for the user
        return $user;
    }

    public function update(Request $request)
    {
        // add logic here to change more than the password
        $this->validate($request, [
            'password' => 'required|min:6'
        ]);
        $user = $request->user();
        $user->password = app('hash')->make($request->input('password'));
        $user->save();

        return $user;
    }
}
