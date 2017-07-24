<?php

namespace ZapsterStudios\Ally\Commands\Publish;

class PublishExceptions extends Publisher
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
        $this->move('app/Exceptions/Handler.php', app_path('Exceptions/Handler.php'));

        $this->notify('Publishing: Exception File');
    }
}