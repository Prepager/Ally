<?php

namespace ZapsterStudios\Ally\Commands\Publish;

class PublishDatabase extends Publisher
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
        $this->move('database/factories/ModelFactory.php', database_path('factories/ModelFactory.php'));

        $this->move('database/migrations/2014_10_12_000000_create_users_table.php', database_path('migrations/2014_10_12_000000_create_users_table.php'));
        $this->move('database/migrations/2017_06_08_165123_create_teams_table.php', database_path('migrations/2017_06_08_165123_create_teams_table.php'));

        $this->notify('Publishing: Database Files');
    }
}