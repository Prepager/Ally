<?php

namespace ZapsterStudios\Ally\Tests;

use Tests\CreatesApplication;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseSetup, DatabaseTransactions;

    protected function setUp()
    {
        parent::setUp();
        $this->setupDatabase();
    }
}
