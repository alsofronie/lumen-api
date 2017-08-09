<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 08/08/2017
 * Time: 23:05
 */

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;

class ProfileController
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Load any relationships you need for the user
        return $user;
    }
}
