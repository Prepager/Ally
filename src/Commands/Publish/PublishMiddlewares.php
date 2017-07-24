<?php

namespace ZapsterStudios\Ally\Commands\Publish;

class PublishMiddlewares extends Publisher
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
        $this->move('app/Http/Kernel.php', app_path('Http/Kernel.php'));

        $this->notify('Publishing: Middleware File');
    }
}