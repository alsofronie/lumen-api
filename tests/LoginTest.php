<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 08/08/2017
 * Time: 22:35
 */

namespace Tests;

use App\Lib\JsonWebToken;
use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class LoginTest extends TestCase
{
    use DatabaseTransactions, DatabaseMigrations;

    public function testFailValidation()
    {
        $this->json('POST', '/api/v1/auth/login', [
            'email' => 'invalid_email',
            'password' => 'password',
        ])
            ->seeStatusCode(422)
            ->seeJson([
                'error' => true,
                'type' => 'validation',
                'details' => [
                    'email' => ['email']
                ]
            ])
        ;
    }

    public function testFailValidationWithPassword()
    {
        $this->json('POST', '/api/v1/auth/login', [
            'email' => 'valid@email.dom',
            'password' => 'passw',  // min 6
        ])
            ->seeStatusCode(422)
            ->seeJson([
                'error' => true,
                'type' => 'validation',
                'details' => [
                    'password' => ['min:6']
                ]
            ])
        ;
    }

    public function testFailWithWrongCredentials()
    {
        $user = factory(User::class)->create([
            'password' => app('hash')->make('secret-1'),
        ]);

        $this->json('POST', '/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'secret',
        ])
            ->seeStatusCode(403)
            ->seeJson([
                'error' => true,
                'type' => 'authentication',
            ])
        ;
    }

    public function testFailWithWrongEmail()
    {
        $user = factory(User::class)->create([
            'email' => 'admin@localhost.dom',
            'password' => app('hash')->make('secret'),
        ]);

        $this->json('POST', '/api/v1/auth/login', [
            'email' => 'a' . $user->email,
            'password' => 'secret',
        ])
            ->seeStatusCode(403)
            ->seeJson([
                'error' => true,
                'type' => 'authentication',
            ])
        ;
    }

    public function testSuccessAuth()
    {
        $user = factory(User::class)->create([
            'password' => app('hash')->make('secret'),
        ]);

        $this->json('POST', '/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'secret',
        ])
            ->seeStatusCode(200)
            ->seeJson([
                'type' => 'auth',
                'method' => 'header',
                'prefix' => 'Bearer ',
            ])
        ;
    }
}
