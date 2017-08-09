<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 08/08/2017
 * Time: 23:20
 */

namespace Tests;

use App\Lib\JsonWebToken;
use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ProfileTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    public function testFailUnauthenticated()
    {
        $this->json('GET', '/api/v1/profile')
            ->seeStatusCode(401)
            ->seeJson([
                'error' => true,
                'type' => 'authentication'
            ]);
    }

    public function testSuccessProfileAccess()
    {
        $user = factory(User::class)->create([
            'password' => app('hash')->make('secret')
        ]);

        $this->json('POST', '/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'secret'
        ])
            ->seeStatusCode(200)
        ;
        $contents = $this->response->getContent();
        static::assertJson($contents);
        $contents = json_decode($contents, true);
        static::assertArrayHasKey('user', $contents);
        static::assertArrayHasKey('token', $contents);
        static::assertArrayHasKey('value', $contents['token']);
        static::assertEquals($contents['user']['uuid'], $user->uuid);

        $this->json('GET', '/api/v1/profile', [], [
            'Authorization' => 'Bearer ' . $contents['token']['value']
        ])->seeStatusCode(200);
    }

    public function testSuccessWithQueryParam()
    {
        $user = factory(User::class)->create([
            'password' => app('hash')->make('secret'),
        ]);

        $token = JsonWebToken::encode($user);

        $this->json('GET', '/api/v1/profile?_api_token=' . $token)
            ->seeStatusCode(200)
        ;
    }

    public function testFailAuthWithUnknownUser()
    {
        $pirate = factory(User::class)->create();
        $headers = $this->authHeaders($pirate);
        User::where('id', $pirate->id)->delete();

        $this->json('GET', '/api/v1/profile', [], $headers)
            ->seeStatusCode(401)
            ->seeJson([
                'error' => true,
                'status' => 401,
                'code' => 1005
            ])
        ;
    }

    public function testFailUpdate()
    {
        $headers = $this->authHeaders();
        $this->json('PUT', '/api/v1/profile', [], $headers)
            ->seeStatusCode(422)
        ;
    }
}
