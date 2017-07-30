<?php

namespace ZapsterStudios\Ally\Commands\Publish;

class PublishExceptions extends Publisher
{
    /**
     * Publish the required files.
     *
     * @return void
     */
    public function publish()
    {
        $this->move('app/Exceptions/Handler.php', app_path(), 'Exceptions/Handler.php');

        $this->notify('Publishing: Exception File');
    }
}
