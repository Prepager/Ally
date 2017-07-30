<?php

namespace ZapsterStudios\Ally\Tests;

use Ally;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use EnvTrait;
    use CashierTrait;
    use PassportTrait;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->withFactories(__DIR__.'/../src/Database/Factories');
        $this->withFactories(__DIR__.'/../install-stubs/database/factories');

        $this->artisan('migrate');
        $this->artisan('migrate', [
            '--path' => '../../../../install-stubs/database/migrations',
        ]);

        $this->artisan('migrate', [
            '--path' => '../../laravel/passport/database/migrations',
        ]);

        $this->loadEnv();

        $this->loadCashierPlans();
        $this->loadCashierSettings();

        $this->loadPassportKeys();
        $this->loadPassportClients();
    }

    public function tearDown()
    {
        parent::tearDown();
        
    }

    /**
     * Define the package service providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            'ZapsterStudios\Ally\AllyServiceProvider',

            'Laravel\Cashier\CashierServiceProvider',
            'Intervention\Image\ImageServiceProvider',
            'Laravel\Passport\PassportServiceProvider',
        ];
    }

    /**
     * Define the package aliases.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Image' => 'Intervention\Image\Facades\Image',
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('auth.guards.api.driver', 'passport');
    }
}
