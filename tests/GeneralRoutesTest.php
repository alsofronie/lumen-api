<?php

namespace Tests;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class GeneralRoutesTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    public function testGeneralHeaders()
    {
        $appName = 'HypeVertising';
        $appVersion = '1.0.0';

        list($versionMajor, $versionMinor, $build) = explode('.', $appVersion);

        $this
            ->get('/api/v' . $versionMajor, ['Content-Type' => 'application/json',])
            ->dd()
            ->seeStatusCode(200)
            ->seeJson([
                'name' => $appName,
                'version' => $appVersion
            ])
            ->seeHeader('X-API-VERSION', $appVersion)
            ->seeHeader('X-API-MIME', 'application/json');
    }
}
