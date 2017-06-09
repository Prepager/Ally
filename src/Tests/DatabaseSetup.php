<?php

/**
 * Thanks to Adam for part of the code from:
 * https://gist.github.com/adamwathan/dd46a8501097942a771925c02bac0111
 */

namespace ZapsterStudios\TeamPay\Tests;

use Illuminate\Contracts\Console\Kernel;

trait DatabaseSetup
{
    protected static $migrated = false;

    public function setupDatabase()
    {
        if ($this->isInMemory()) {
            $this->setupInMemoryDatabase();
        } else {
            $this->setupTestDatabase();
        }
    }

    protected function isInMemory()
    {
        return config('database.connections')[config('database.default')]['database'] == ':memory:';
    }

    protected function setupInMemoryDatabase()
    {
        $this->artisan('migrate');
        $this->artisan('passport:install');
        $this->app[Kernel::class]->setArtisan(null);
    }

    protected function setupTestDatabase()
    {
        if (! static::$migrated) {
            $this->artisan('migrate:refresh');
            $this->artisan('passport:install');
            $this->app[Kernel::class]->setArtisan(null);
            static::$migrated = true;
        }
    }
}