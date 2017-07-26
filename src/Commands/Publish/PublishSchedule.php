<?php

namespace ZapsterStudios\Ally\Commands\Publish;

class PublishSchedule extends Publisher
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
        $this->move('app/Console/Kernel.php', app_path('Console/Kernel.php'));

        $this->notify('Publishing: Schedule File');
    }
}
