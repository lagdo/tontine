<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Loan;
use Siak\Tontine\Model\Saving;
use Siak\Tontine\Model\Tontine;

class CreateDefaultFunds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fund:create-defaults';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a default savings fund for each tontine';

    /**
     * Execute the console command.
     *
      * @return int
     */
    public function handle()
    {
        DB::transaction(function() {
            foreach(Tontine::cursor() as $tontine)
            {
                // Create a default savings fund, if it doesn't exist yet.
                $fund = $tontine->funds()
                    ->withoutGlobalScope('user')
                    ->updateOrCreate(['title' => ''], ['active' => true]);

                // Update the savings and loans with the default fund id.
                Loan::whereNull('fund_id')
                    ->whereHas('member', function($query) use($tontine) {
                        $query->where('tontine_id', $tontine->id);
                    })
                    ->update(['fund_id' => $fund->id]);
                Saving::whereNull('fund_id')
                    ->whereHas('member', function($query) use($tontine) {
                        $query->where('tontine_id', $tontine->id);
                    })
                    ->update(['fund_id' => $fund->id]);
            }
        });

        return Command::SUCCESS;
    }
}
