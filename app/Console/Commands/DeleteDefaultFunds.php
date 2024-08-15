<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Loan;
use Siak\Tontine\Model\Saving;

class DeleteDefaultFunds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fund:delete-defaults';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete the default savings fund for all tontines';

    /**
     * Execute the console command.
     *
      * @return int
     */
    public function handle()
    {
        DB::transaction(function() {
            foreach(Fund::where('title', '')->cursor() as $fund)
            {
                // Update the savings and loans with the default fund id.
                Loan::where('fund_id', $fund->id)->update(['fund_id' => null]);
                Saving::where('fund_id', $fund->id)->update(['fund_id' => null]);

                // Delete the fund.
                $fund->delete();
            }
        });

        return Command::SUCCESS;
    }
}
