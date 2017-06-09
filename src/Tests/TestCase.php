<?php

namespace ZapsterStudios\TeamPay\Tests;

use Tests\CreatesApplication;
use Illuminate\Foundation\Testing\DatabaseTransactions;

abstract class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    use CreatesApplication, DatabaseSetup, DatabaseTransactions;
    
    protected function setUp()
    {
        parent::setUp();
        $this->setupDatabase();
    }
}