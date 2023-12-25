<?php

namespace Siak\Tontine\Service;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Tontine;
use Siak\Tontine\Model\User;

class TenantService
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @var User|null
     */
    protected ?User $user = null;

    /**
     * @var Tontine|null
     */
    protected ?Tontine $tontine = null;

    /**
     * @var Round|null
     */
    protected ?Round $round = null;

    /**
     * @var int
     */
    protected int $limit = 10;

    /**
     * @param LocaleService $localeService
     */
    public function __construct(LocaleService $localeService)
    {
        $this->localeService = $localeService;
    }

    /**
     * @param User $user
     *
     * @return void
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @param Tontine $tontine
     *
     * @return void
     */
    public function setTontine(Tontine $tontine): void
    {
        $this->tontine = $tontine;
        // Set the currency for locales.
        $this->localeService->setCurrency($tontine->currency_code);
        // Save as latest tontine id if it has changed.
        $tontineId = $this->user->properties['latest']['tontine'] ?? 0;
        if($tontineId === $tontine->id)
        {
            return;
        }
        $properties = $this->user->properties;
        $properties['latest']['tontine'] = $tontine->id;
        $this->user->saveProperties($properties);
    }

    /**
     * @param Round $round
     *
     * @return void
     */
    public function setRound(Round $round): void
    {
        $this->round = $round;
        // Save as latest round id if it has changed.
        $roundId = $this->user->properties['latest']['round'] ?? 0;
        if($roundId === $round->id)
        {
            return;
        }
        $properties = $this->user->properties;
        $properties['latest']['round'] = $round->id;
        $this->user->saveProperties($properties);
    }

    /**
     * @return User|null
     */
    public function user(): ?User
    {
        return $this->user;
    }

    /**
     * @return Tontine|null
     */
    public function tontine(): ?Tontine
    {
        return $this->tontine;
    }

    /**
     * @return Round|null
     */
    public function round(): ?Round
    {
        return $this->round;
    }

    /**
     * Get a single round.
     *
     * @param int $roundId    The round id
     *
     * @return Round|null
     */
    public function getRound(int $roundId): ?Round
    {
        return $this->tontine->rounds()->find($roundId);
    }

    /**
     * @return mixed
     */
    public function sessions()
    {
        return Session::whereIn('round_id', $this->tontine->rounds->pluck('id'));
    }

    /**
     * Get a single session.
     *
     * @param int $sessionId    The session id
     *
     * @return Session|null
     */
    public function getSession(int $sessionId): ?Session
    {
        return $this->round->sessions->firstWhere('id', '===', $sessionId);
    }

    /**
     * @param Session $lastSession
     * @param bool $withCurrent
     * @param bool $orderAsc
     *
     * @return Collection
     */
    public function getSessions(?Session $lastSession = null,
        bool $withCurrent = true, bool $orderAsc = true): Collection
    {
        $sessions = $this->round()->sessions()
            ->orderBy('start_at', $orderAsc ? 'asc' : 'desc')
            ->get();

        return $lastSession === null ? $sessions :
            $sessions->filter(function($session) use($lastSession, $withCurrent) {
                return $withCurrent ? $session->start_at <= $lastSession->start_at :
                    $session->start_at < $lastSession->start_at;
            });
    }

    /**
     * @param Session $session
     * @param bool $withCurrent
     * @param bool $orderAsc
     *
     * @return Collection
     */
    public function getSessionIds(?Session $lastSession = null,
        bool $withCurrent = true, bool $orderAsc = true): Collection
    {
        return $this->getSessions($lastSession, $withCurrent, $orderAsc)->pluck('id');
    }

    /**
     * Get the number of sessions enabled for a pool.
     *
     * @param Pool $pool
     *
     * @return int
     */
    public function countEnabledSessions(Pool $pool): int
    {
        return $this->round->sessions->count() - $pool->disabledSessions->count();
    }

    /**
     * Get the sessions enabled for a pool.
     *
     * @param Pool $pool
     *
     * @return Collection
     */
    public function getEnabledSessions(Pool $pool): Collection
    {
        return $this->getSessions()->filter(function($session) use($pool) {
            return $session->enabled($pool);
        });
    }

    /**
     * Get a single pool.
     *
     * @param int $poolId    The pool id
     * @param bool $with
     *
     * @return Pool|null
     */
    public function getPool(int $poolId, bool $with = false): ?Pool
    {
        $query = $this->tontine->pools();
        if($with)
        {
            $query->with(['subscriptions.receivables.deposit']);
        }
        return $query->find($poolId);
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }
}
