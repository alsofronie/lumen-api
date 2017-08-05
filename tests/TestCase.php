<?php

namespace Tests;

abstract class TestCase extends \Laravel\Lumen\Testing\TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    protected function dd()
    {
        static::assertNotNull($this->response);
        static::assertNotNull($this->response->getContent());
        print_r($this->response->getContent());
        return $this;
    }
}
