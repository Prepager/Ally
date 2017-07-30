<?php

namespace ZapsterStudios\Ally\Tests;

trait EnvTrait
{
    /**
     * Determine if the env file should be loaded.
     *
     * @var bool
     */
    protected $usesEnv = false;

    /**
     * Whether or not the env was loaded.
     *
     * @var bool
     */
    protected static $envLoaded = false;

    /**
     * Load env file.
     *
     * @return void
     */
    public function loadEnv()
    {
        if (! $this->usesEnv || static::$envLoaded) {
            return;
        }

        $location = realpath(__DIR__.'/../..');
        if (file_exists($location.'/.env')) {
            $dotenv = new \Dotenv\Dotenv($location);
            $dotenv->load();
        }

        static::$envLoaded = true;
    }
}
