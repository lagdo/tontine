<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Closing;
use Siak\Tontine\Model\Tontine;

class CopyClosingsToTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'closing:copy-to-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy the closings from the tontine attributes to their own table';

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
                $closings = $tontine->properties['closings'] ?? [];
                foreach($closings as $sessionId => $funds)
                {
                    foreach($funds as $fundId => $profitAmount)
                    {
                        Closing::updateOrCreate([
                            'type' => Closing::TYPE_ROUND,
                            'session_id' => $sessionId,
                            'fund_id' => $fundId ?: $tontine->default_fund->id,
                        ], [
                            'options' => [
                                'profit' => [
                                    'amount' => $profitAmount,
                                ],
                            ],
                        ]);
                    }
                }
            }
        });

        return Command::SUCCESS;
    }
}
