<?php

namespace ZapsterStudios\Ally\Commands\Publish;

class PublishSchedule extends Publisher
{
    /**
     * Publish the required files.
     *
     * @return void
     */
    public function publish()
    {
        $this->move('app/Console/Kernel.php', app_path(), 'Console/Kernel.php');

        $this->notify('Publishing: Schedule File');
    }
}
