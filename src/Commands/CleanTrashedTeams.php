<?php

namespace ZapsterStudios\Ally\Commands;

use Ally;
use App\Team;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanTrashedTeams extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'teams:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete trashed teams after grace periode.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (Ally::$skipDeletionGracePeriod) {
            return;
        }

        $time = Carbon::now()->subDays(Ally::$gracePeriodDays);
        $teams = Team::where('deleted_at', '<=', $time)->withTrashed();

        $teams->each(function ($team) {
            $team->forceDelete();
        });
    }
}
