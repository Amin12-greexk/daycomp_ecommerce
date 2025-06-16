<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    public function test_homepage()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}

