<?php

namespace ZapsterStudios\Ally\Commands\Publish;

class PublishProviders extends Publisher
{
    /**
     * Construct the publishable command.
     *
     * @param  string  $command
     * @return void
     */
    public function __construct($command)
    {
        $this->command = $command;
    }

    /**
     * Publish the required files.
     *
     * @return void
     */
    public function publish()
    {
        $this->move('app/Providers/AuthServiceProvider.php', app_path('Providers/AuthServiceProvider.php'));

        $this->notify('Publishing: Provider File');
    }
}