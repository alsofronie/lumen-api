<?php

namespace Tests;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class GeneralRoutesTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    public function testXApiHeaders()
    {
        $appName = env('APP_NAME');
        $appVersion = env('APP_VERSION');
        $versionMajor = 1;

        $this
            ->get('/api/v' . $versionMajor, [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])
            ->seeStatusCode(200)
            ->seeJson([
                'name' => $appName,
                'version' => $appVersion
            ])
            ->seeHeader('X-API-VERSION', $appVersion)
            ->seeHeader('X-API-MIME', 'application/json');
    }

    public function testFailJsonAcceptHeaders()
    {
        $this->get('/api/v1', ['Accept' => 'text/html'])
            ->seeStatusCode(406)
            ->seeJson([
                'error' => true,
                'type' => 'invalid_header'
            ])
        ;
    }

    public function testFailJsonEmptyAcceptHeader()
    {
        $this->get('/api/v1', ['Accept' => ''])
            ->seeStatusCode(406)
            ->seeJson([
                'error' => true,
                'type' => 'empty_header'
            ])
        ;
    }

    public function testAcceptJsonAcceptHeaders()
    {
        $this->get('/api/v1', ['Accept' => 'application/json'])
            ->seeStatusCode(200)
            ->dontSeeJson([
                'error' => true,
            ]);
        ;
    }


}
