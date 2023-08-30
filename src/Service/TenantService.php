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
    }

    /**
     * @param Round $round
     *
     * @return void
     */
    public function setRound(Round $round): void
    {
        $this->round = $round;
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
        return $this->round->sessions()->find($sessionId);
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    public function getSessions(Session $session): Collection
    {
        return $this->round()->sessions()->where('start_at', '<=', $session->start_at)
            ->orderBy('start_at', 'asc')->get();
    }

    /**
     * @param Session $session
     * @param bool $withCurrent
     *
     * @return Collection
     */
    public function getPreviousSessions(Session $session, bool $withCurrent = true): Collection
    {
        $op = $withCurrent ? '<=' : '<';
        return $this->round()->sessions()->where('start_at', $op, $session->start_at)
            ->orderBy('start_at', 'asc')->pluck('id');
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
        $pools = $this->round->pools();
        if($with)
        {
            $pools->with(['subscriptions.receivables.deposit']);
        }
        return $pools->find($poolId);
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }
}
