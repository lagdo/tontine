<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Siak\Tontine\Model\Round;

class CheckSessionDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:check-dates {round1Id} {round2Id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check duplicated dates in tontine sessions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $round1Id = $this->argument('round1Id');
        if(!($round1 = Round::find($round1Id)))
        {
            return Command::FAILURE;
        }
        // Search the 2nd round in the same tontine as the first one.
        $round2Id = $this->argument('round2Id');
        if(!($round2 = $round1->tontine->rounds()->find($round2Id)))
        {
            return Command::FAILURE;
        }
        // Compare the sessions dates
        $round1Sessions = $round1->sessions()
            ->with(['loans', 'auctions', 'savings', 'disbursements'])
            ->orderBy('start_at')->get();
        $round2Sessions = $round2->sessions;
        $duplicates = [];
        foreach($round1Sessions as $round1Session)
        {
            $round1Session->savings()->update(['fund_id' => 1]);
            $round2Session = $round2Sessions->first(fn($session) =>
                $round1Session->start_at->format('Y-m-d') === $session->start_at->format('Y-m-d'));
            if(($round2Session))
            {
                $duplicates[] = $round1Session->id;
                $this->info("Sessions {$round1Session->title} and {$round2Session->title} " .
                    "are on the same date {$round1Session->start_at}.");
                $this->info("Counters: loans {$round1Session->loans->count()}, auctions " .
                    "{$round1Session->auctions->count()}, savings {$round1Session->savings->count()}, " .
                    "disbursements {$round1Session->disbursements->count()}.");
                $round1Session->loans()->update(['session_id' => $round2Session->id]);
                $round1Session->auctions()->update(['session_id' => $round2Session->id]);
                $round1Session->savings()->update(['session_id' => $round2Session->id]);
                $round1Session->disbursements()->update(['session_id' => $round2Session->id]);
                $round1Session->session_bills()->delete();
                $round1Session->libre_bills()->delete();
            }
        }
        if(count($duplicates) > 0)
        {
            $round1->sessions()->whereIn('id', $duplicates)->delete();
        }
        $this->info(count($duplicates) . " duplicate(s) found.");

        return Command::SUCCESS;
    }
}
