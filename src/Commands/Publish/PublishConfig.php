<?php

namespace ZapsterStudios\Ally\Commands\Publish;

class PublishConfig extends Publisher
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
        $this->move('config/auth.php', config_path('auth.php'));
        $this->move('config/services.php', config_path('services.php'));

        $this->notify('Publishing: Config Files');
    }
}
