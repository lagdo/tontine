<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Siak\Tontine\Model\Pool;

class ConvertPoolProperties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pools:convert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert pool properties';

    /**
     * Execute the console command.
     *
      * @return int
     */
    public function handle()
    {
        $pools = Pool::get();
        foreach($pools as $pool)
        {
            $currProperties = $pool->properties;
            $nextProperties = ['deposit' => [], 'remit' => []];
            $nextProperties['deposit']['fixed'] = $currProperties['deposit']['fixed'] ?? true;
            $nextProperties['deposit']['lendable'] = $currProperties['remit']['lendable'] ?? false;
            $nextProperties['remit']['planned'] = $currProperties['remit']['planned'] ?? true;
            $nextProperties['remit']['auction'] = $currProperties['remit']['auction'] ?? false;

            $currProperties['deposit'] = $nextProperties['deposit'];
            $currProperties['remit'] = $nextProperties['remit'];
            $pool->properties = $currProperties;
            $pool->save();
        }

        return Command::SUCCESS;
    }
}
