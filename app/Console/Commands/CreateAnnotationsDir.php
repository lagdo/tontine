<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use function file_exists;
use function mkdir;
use function storage_path;

class CreateAnnotationsDir extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'annotations:mkdir';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the cache dir for the Jaxon classes annotations';

    /**
     * Execute the console command.
     *
      * @return int
     */
    public function handle()
    {
        $path = storage_path('annotations');
        file_exists($path) || mkdir($path, 0755, false);

        return Command::SUCCESS;
    }
}
