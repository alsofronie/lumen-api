<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 08/08/2017
 * Time: 22:28
 */

namespace Tests;

use App\Lib\JsonWebToken;
use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class JwtTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    public function testGeneral()
    {
        $user = factory(User::class)->create();
        $jwt = JsonWebToken::encode($user);
        static::assertNotNull($jwt);

        $retrieved = JsonWebToken::decode($jwt);
        static::assertEquals($retrieved->id, $user->id);
        static::assertEquals($retrieved->email, $user->email);
    }
}
