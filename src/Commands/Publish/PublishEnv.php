<?php

namespace ZapsterStudios\Ally\Commands\Publish;

class PublishEnv extends Publisher
{
    /**
     * Publish the required files.
     *
     * @return void
     */
    public function publish()
    {
        $this->append('../.env.example', base_path(), '.env.example', PHP_EOL.PHP_EOL);
        $this->append('../.env.example', base_path(), '.env', PHP_EOL.PHP_EOL);

        $this->notify('Publishing: Env File');
    }
}
