<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 08/08/2017
 * Time: 15:41
 */

namespace Tests;

use App\Lib\Uuid;
use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class UuidGeneratorTest extends TestCase
{
    use DatabaseTransactions, DatabaseMigrations;

    public function testFormat()
    {
        $uuid = new Uuid();
        static::assertTrue(ctype_print($uuid->str));
        static::assertFalse(ctype_print($uuid->bin));

        static::assertEquals(32, strlen($uuid->str));
        static::assertEquals(36, strlen($uuid->uuid));
        static::assertRegExp('/^[0-9a-z]{32}$/', $uuid->str);
        static::assertRegExp('/^[0-9a-z]{8}-[0-9a-z]{4}-[0-9a-z]{4}-[0-9a-z]{4}-[0-9a-z]{12}$/', $uuid->uuid);
    }

    public function testCreatingUserWithBinaryUuidTrait()
    {
        $user = factory(User::class)->create();

        static::assertNotNull($user->id);
        static::assertFalse(ctype_print($user->id));
        static::assertEquals(16, strlen($user->id));
    }

    public function testQueryingUserWithBinaryUuidTrait()
    {
        $user = factory(User::class)->create();
        $retrieved = User::find($user->id);
        static::assertNotNull($retrieved);
        static::assertEquals($user->id, $retrieved->id);
        static::assertEquals($user->name, $retrieved->name);
        static::assertEquals($user->email, $retrieved->email);
    }

    public function testSerializingUserWithBinaryTrait()
    {
        $user = factory(User::class)->create();
        $serialized = serialize($user);
        $unserialized = unserialize($serialized);
        static::assertEquals($user->id, $unserialized->id);
        static::assertEquals($user->name, $unserialized->name);
        static::assertEquals($user->email, $unserialized->email);

        $retrieved = User::find($user->id);
        static::assertNotNull($retrieved);
        static::assertEquals($user->id, $retrieved->id);
        static::assertEquals($user->name, $retrieved->name);
        static::assertEquals($user->email, $retrieved->email);
    }
}
