<?php

namespace ZapsterStudios\Ally\Tests;

use DB;
use Ally;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * If the migrations was migrated.
     *
     * @var bool
     */
    protected static $migrated = false;

    /**
     * List of database tables.
     *
     * @var array
     */
    protected static $tables = [];

    /**
     * Setup passport clients.
     *
     * @return void
     */
    public function setUpPassport()
    {
        $this->artisan('passport:client', ['--personal' => true, '--name' => config('app.name').' Personal Access Client']);
        $this->artisan('passport:client', ['--password' => true, '--name' => config('app.name').' Password Grant Client']);
    }

    /**
     * Setup billing plans.
     *
     * @return void
     */
    public function setUpPlans()
    {
        Ally::addPlan('free-plan', 'Free Plan', 0);
        Ally::addPlan('valid-first-plan', 'Valid First Plan', 5);
        Ally::addPlan('valid-second-plan', 'Valid Second Plan', 10);
    }

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

        if (static::$migrated) {
            $this->setUpPassport();

            return;
        }

        $this->artisan('migrate', ['--database' => 'testing']);
        $this->artisan('migrate', [
            '--database' => 'testing',
            '--path' => '../../../../install-stubs/database/migrations',
        ]);

        $this->artisan('migrate', [
            '--database' => 'testing',
            '--path' => '../../laravel/passport/database/migrations',
        ]);

        $this->setUpPassport();
        $this->setUpPlans();

        static::$migrated = true;
        static::$tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    protected function tearDown()
    {
        foreach (static::$tables as $table) {
            if ($table == 'migrations' || fnmatch('oauth*', $table)) {
                continue;
            }

            DB::table($table)->truncate();
        }

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
