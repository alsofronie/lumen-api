<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 08/08/2017
 * Time: 15:41
 */

namespace Tests;

use App\Lib\Uuid;

class UuidGeneratorTest extends TestCase
{
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

    public function testCollisions()
    {
        $generated = [];
        for ($i = 0; $i < 10; $i++) {
            $uuid = new Uuid();
            foreach ($generated as $g) {
                static::assertNotEquals($uuid->bin, $g);
            }
            $generated[] = $uuid->bin;
        }
    }
}
