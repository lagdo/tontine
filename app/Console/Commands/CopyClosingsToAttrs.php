<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Closing;

class CopyClosingsToAttrs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'closing:copy-to-attrs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy the closings from their own table to the tontine attributes';

    /**
     * Execute the console command.
     *
      * @return int
     */
    public function handle()
    {
        DB::transaction(function() {
            foreach(Closing::savings()->with('fund.tontine')->cursor() as $closing)
            {
                $tontine = $closing->fund->tontine;
                $properties = $tontine->properties;
                $properties['closings'][$closing->session_id][$closing->fund_id] = $closing->profit;
                $tontine->saveProperties($properties);
            }
        });

        return Command::SUCCESS;
    }
}
