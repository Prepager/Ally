<?php

namespace ZapsterStudios\Ally\Tests;

trait PassportTrait
{
    /**
     * Whether or not the keys was published.
     *
     * @var bool
     */
    protected static $keysPublished = false;

    /**
     * Setup Laravel Passport keys.
     *
     * @return void
     */
    public function loadPassportKeys()
    {
        if (static::$keysPublished) {
            return;
        }

        $this->artisan('passport:keys');
        static::$keysPublished = true;
    }

    /**
     * Setup Laravel Passport clients.
     *
     * @return void
     */
    public function loadPassportClients()
    {
        $this->artisan('passport:client', ['--personal' => true, '--name' => config('app.name').' Personal Access Client']);
        $this->artisan('passport:client', ['--password' => true, '--name' => config('app.name').' Password Grant Client']);
    }
}
