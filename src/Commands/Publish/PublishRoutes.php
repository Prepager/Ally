<?php

namespace ZapsterStudios\Ally\Commands\Publish;

class PublishRoutes extends Publisher
{
    /**
     * Publish the required files.
     *
     * @return void
     */
    public function publish()
    {
        $this->move('routes/api.php', base_path(), 'routes/api.php');
        $this->move('routes/web.php', base_path(), 'routes/web.php');

        $this->notify('Publishing: Route Files');
    }
}
