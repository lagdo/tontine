<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Service\Planning\DataSyncService;

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
        Fund::unguard();
        foreach(Round::cursor() as $round)
        {
            // Set the default fund option value.
            $hasDefault = Fund::where('round_id', $round->id)
                ->where('def_id', $round->guild->default_fund->id)
                ->exists();
            $properties = $round->properties;
            $properties['savings']['fund']['default'] = $hasDefault;
            $round->saveProperties($properties);

            $syncService = app()->make(DataSyncService::class);
            // Create the funds for pools with deposits lendable.
            $round->pools()
                ->whereHas('def', fn($q) => $q->depositLendable())
                ->get()
                ->each(fn($pool) => $syncService->savePoolFund($round, $pool));
        }
        Fund::reguard();
    });
})->purpose('Save the default fund id in the round properties');
