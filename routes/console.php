<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('annotations:mkdir', function () {
    $path = storage_path('annotations');
    file_exists($path) || mkdir($path, 0755, false);
})->purpose('Create the cache dir for the Jaxon classes annotations');

Artisan::command('fund:create-defaults', function () {
    DB::transaction(function() {
        foreach(DB::table('tontines')->cursor() as $tontine)
        {
            // Create a default savings fund, if it doesn't exist yet.
            $fund = DB::table('funds')->where('tontine_id', $tontine->id)
                ->updateOrCreate(['title' => ''], ['active' => true]);

            // Update the savings and loans with the default fund id.
            DB::table('loans')
                ->whereExists(function($query) use($tontine) {
                    $query->select(DB::raw(1))
                        ->from('members')
                        ->whereColumn('members.id', 'loans.member_id')
                        ->where('members.tontine_id', $tontine->id);
                })
                ->whereNull('loans.fund_id')
                ->update(['fund_id' => $fund->id]);
            DB::table('savings')
                ->whereExists(function($query) use($tontine) {
                    $query->select(DB::raw(1))
                        ->from('members')
                        ->whereColumn('members.id', 'savings.member_id')
                        ->where('members.tontine_id', $tontine->id);
                })
                ->whereNull('savings.fund_id')
                ->update(['fund_id' => $fund->id]);
        }
    });
})->purpose('Create a default savings fund for each tontine');

Artisan::command('fund:delete-defaults', function () {
    DB::transaction(function() {
        foreach(DB::table('funds')->where('title', '')->cursor() as $fund)
        {
            // Update the savings and loans with the default fund id.
            DB::table('loans')->where('fund_id', $fund->id)->update(['fund_id' => null]);
            DB::table('savings')->where('fund_id', $fund->id)->update(['fund_id' => null]);
        }

        // Delete the funds.
        DB::table('funds')->where('title', '')->delete();
    });
})->purpose('Delete the default savings fund for all tontines');

Artisan::command('closing:copy-to-table', function () {
    DB::transaction(function() {
        foreach(DB::table('tontines')->cursor() as $tontine)
        {
            $defaultFund = DB::table('funds')
                ->where('tontine_id', $tontine->id)
                ->where('title', '')
                ->first();
            $closings = $tontine->properties['closings'] ?? [];
            foreach($closings as $sessionId => $funds)
            {
                foreach($funds as $fundId => $profitAmount)
                {
                    DB::table('closings')->updateOrCreate([
                        'type' => 'r', // Closing::TYPE_ROUND,
                        'session_id' => $sessionId,
                        'fund_id' => $fundId ?: $defaultFund->id,
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
})->purpose('Copy the closings from the tontine attributes to their own table');

Artisan::command('closing:copy-to-attrs', function () {
    DB::transaction(function() {
        foreach(DB::table('closings')
            ->where('type', 's' /*self::TYPE_SAVINGS*/)
            ->cursor() as $closing)
        {
            $tontineId = DB::table('funds')->find($closing->fund_id)->tontine_id;
            $property = DB::table('properties')
                ->where('owner_type', 'Siak\\Tontine\\Model\\Tontine')
                ->where('owner_id', $tontineId)
                ->first();
            $content = json_decode($property->content);
            $content['closings'][$closing->session_id][$closing->fund_id] = $closing->profit;
            DB::table('properties')
                ->where('owner_type', 'Siak\\Tontine\\Model\\Tontine')
                ->where('owner_id', $tontineId)
                ->update(['content' => json_encode($content)]);
        }
    });
})->purpose('Copy the closings from their own table to the tontine attributes');

Artisan::command('round:update-funds', function () {
    DB::transaction(function() {
        $query = DB::table('rounds')
            ->select([
                'rounds.*',
                'p.content',
                'default_fund_id' => DB::table('fund_defs')
                    ->join('funds', 'funds.def_id', '=', 'fund_defs.id')
                    ->selectRaw('max(fund_defs.id)')
                    ->whereColumn('fund_defs.guild_id', '=', 'rounds.guild_id')
                    ->where('title', ''),
            ])
            ->join(DB::raw('properties as p'), 'p.owner_id', '=', 'rounds.guild_id')
            ->where('p.owner_type', 'Siak\\Tontine\\Model\\Guild');
        foreach($query->cursor() as $round)
        {
            $properties = json_decode($round->content, true);
            // Set the default fund option value.
            $hasDefault = DB::table('funds')
                ->where('round_id', $round->id)
                ->where('def_id', $round->default_fund_id)
                ->exists();
            $properties['savings']['fund']['default'] = $hasDefault;
            DB::table('properties')
                ->where('owner_id', $round->guild_id)
                ->where('owner_type', 'Siak\\Tontine\\Model\\Guild')
                ->update(['content' => json_encode($properties)]);

            // Find the first and last sessions.
            $sessions = DB::table('sessions')
                ->where('round_id', $round->id)
                ->orderBy('start_at', 'asc')
                ->get();
            $startSessionId = $sessions->first()->id;
            $endSessionId = $sessions->last()->id;
            // Create the funds for pools with deposits lendable.
            DB::table('pools')
                ->where('round_id', $round->id)
                ->whereExists(function($query) {
                    $query->select(DB::raw(1))
                        ->from(DB::raw('pool_defs as pd'))
                        ->whereColumn('pd.id', '=', 'pools.def_id')
                        ->where('pd.properties->deposit->lendable');
                })
                ->get()
                ->each(fn($pool) =>
                    DB::table('funds')->updateOrCreate([
                        'pool_id' => $pool->id,
                    ], [
                        'def_id' => $round->default_fund_id,
                        'round_id' => $round->id,
                        'start_sid' => $startSessionId,
                        'end_sid' => $endSessionId,
                        'interest_sid' => $endSessionId,
                    ]));
        }
    });
})->purpose('Save the default fund id in the round properties');
