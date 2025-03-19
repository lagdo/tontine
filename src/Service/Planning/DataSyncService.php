<?php

namespace Siak\Tontine\Service\Planning;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Tontine;

use function trans;

class DataSyncService
{
    /**
     * @param string $table
     * @param string $relation
     *
     * @return Closure
     */
    private function filter(string $table, string $relation): Closure
    {
        return function($query) use($relation, $table) {
            $query->select(DB::raw(1))
                ->from($relation)
                ->whereColumn("$relation.session_id", "$table.session_id")
                ->whereColumn("$relation.pool_id", 'subscriptions.pool_id');
        };
    }

    /**
     * @param string $table
     *
     * @return Closure
     */
    private function filters(string $table): Closure
    {
        return function($query) use($table) {
            $query->orWhereNotExists($this->filter($table, 'v_pool_session'))
                ->orWhereExists($this->filter($table, 'pool_session_disabled'));
        };
    }

    /**
     * @param Pool $pool
     * @param bool $filter
     *
     * @return void
     * @throws MessageException
     */
    public function syncPool(Pool $pool, bool $filter): void
    {
        // Check for existing remitments.
        $payables = DB::table('payables')
            ->join('subscriptions', 'payables.subscription_id', '=', 'subscriptions.id')
            ->where('subscriptions.pool_id', $pool->id)
            ->when($filter, fn($query) => $query->where($this->filters('payables')))
            ->select('payables.id')
            ->distinct()
            ->pluck('id');
        if($payables->count() > 0 &&
            DB::table('remitments')->whereIn('payable_id', $payables)->count() > 0)
        {
            throw new MessageException(trans('tontine.errors.action') .
                '<br/>' . trans('tontine.pool.errors.payments'));
        }
        // Check for existing deposits.
        $receivables = DB::table('receivables')
            ->join('subscriptions', 'receivables.subscription_id', '=', 'subscriptions.id')
            ->where('subscriptions.pool_id', $pool->id)
            ->when($filter, fn($query) => $query->where($this->filters('receivables')))
            ->select('receivables.id')
            ->distinct()
            ->pluck('id');
        if($receivables->count() > 0 &&
            DB::table('deposits')->whereIn('receivable_id', $receivables)->count() > 0)
        {
            throw new MessageException(trans('tontine.errors.action') .
                '<br/>' . trans('tontine.pool.errors.payments'));
        }
        // Detach the payables from their sessions.
        if($payables->count() > 0)
        {
            DB::table('payables')->whereIn('id', $payables)->update(['session_id' => null]);
        }
        // Delete the receivables.
        if($receivables->count() > 0)
        {
            DB::table('receivables')->whereIn('id', $receivables)->delete();
        }
    }

    /**
     * @param Round $round
     * @param Session $session
     *
     * @return void
     */
    private function disableSessionOnPools(Round $round, Session $session)
    {
        // Disable this session on all planned pools
        $round->pools()
            ->remitPlanned()
            ->get()
            ->each(function($pool) use($session) {
                $pool->disabled_sessions()->attach($session->id);
            });
    }

    /**
     * Called after a new session is created
     *
     * @param Round $round
     * @param Session $session
     *
     * @return void
     */
    public function onNewSession(Round $round, Session $session)
    {
        $this->disableSessionOnPools($round, $session);
    }

    /**
     * Called before a session is deleted
     *
     * @param Session $session
     *
     * @return void
     */
    public function onDeleteSession(Session $session)
    {
        if($session->payables()->paid()->exists() ||
            $session->receivables()->paid()->exists() ||
            $session->session_bills()->paid()->exists())
        {
            throw new MessageException(trans('tontine.errors.action') .
                '<br/>' . trans('tontine.session.errors.delete'));
        }

        // Delete related data that may have been automatically created.
        $session->payables()->update(['session_id' => null]);
        $session->receivables()->delete();
        $session->session_bills()->delete();
        $session->disabled_pools()->delete();
    }

    /**
     * Find the prev session.
     *
     * @param Tontine $tontine
     * @param Session $session
     *
     * @return Session|null
     */
    private function getPrevSession(Tontine $tontine, Session $session): ?Session
    {
        return $tontine->sessions()->active()
            ->where('start_at', '<', $session->start_at)
            ->orderBy('start_at', 'desc')
            ->first();
    }

    /**
     * Find the next session.
     *
     * @param Tontine $tontine
     * @param Session $session
     *
     * @return Session|null
     */
    private function getNextSession(Tontine $tontine, Session $session): ?Session
    {
        return $tontine->sessions()->active()
            ->where('start_at', '>', $session->start_at)
            ->orderBy('start_at', 'asc')
            ->first();
    }

    /**
     * Called before a session is updated
     *
     * @param Tontine $tontine
     * @param Session $session
     * @param array $values
     *
     * @return void
     */
    public function onUpdateSession(Tontine $tontine, Session $session, array $values): void
    {
        // Check that the sessions date sorting is not modified.
        $date = Carbon::createFromFormat('Y-m-d', $values['date']);
        $prevSession = $this->getPrevSession($tontine, $session);
        $nextSession = $this->getNextSession($tontine, $session);
        if(($prevSession !== null && $prevSession->start_at->startOfDay()->gte($date)) ||
            ($nextSession !== null && $nextSession->start_at->startOfDay()->lte($date)))
        {
            throw new MessageException(trans('tontine.errors.action') .
                '<br/>' . trans('tontine.session.errors.sorting'));
        }
    }
}
