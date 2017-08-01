<?php

namespace ZapsterStudios\Ally\Commands;

use DB;
use Illuminate\Console\Command;

class InstallationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ally:install
                            {--force=false}
                            {--testing=false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish ally install-stubs.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (! $this->checkConnection()) {
            $this->error('Incorrect database credentials... SQL connection required for installation!');

            return;
        }

        $this->comment('> Starting Laravel Ally installation.');
        $this->line('');

        $publishers = collect([
            Publish\PublishConfig::class,
            Publish\PublishDatabase::class,
            Publish\PublishEnv::class,
            Publish\PublishExceptions::class,
            Publish\PublishModels::class,
            Publish\PublishRoutes::class,
            Publish\PublishSchedule::class,
        ]);

        $publishers->each(function ($publisher) {
            (new $publisher($this, $this->option('testing')))->publish();
        });

        $this->line('');
        $this->comment('> Running migrations.');
        $this->call('migrate');

        $this->line('');
        $this->comment('> Installing Passport.');
        $this->call('passport:install');

        $this->line('');
        $this->comment('> Compleated Laravel Ally installation.');
    }

    /**
     * Check the SQL connection.
     *
     * @return bool
     */
    private function checkConnection()
    {
        try {
            DB::select('SELECT 1');

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
