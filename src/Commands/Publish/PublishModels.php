<?php

namespace ZapsterStudios\Ally\Commands\Publish;

class PublishModels extends Publisher
{
    /**
     * Publish the required files.
     *
     * @return void
     */
    public function publish()
    {
        $this->move('app/Team.php', app_path(), 'Team.php');
        $this->move('app/User.php', app_path(), 'User.php');

        $this->notify('Publishing: Model Files');
    }
}
