<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Recurring\Services\RecurringService;

class RunRecurringRules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recurring:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(RecurringService $service)
    {
        $service->runDueRecurrings();

        return Command::SUCCESS;
    }
}
