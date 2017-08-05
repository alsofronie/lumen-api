<?php

namespace App\Http\Controllers\V1;

class HomeController extends BaseController
{
    public function index()
    {
        return [
            'name' => env('APP_NAME'),
            'version' => env('APP_VERSION')
        ];
    }
}
