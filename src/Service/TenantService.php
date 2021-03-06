<?php

namespace Siak\Tontine\Service;

use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Currency;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Tontine;
use Siak\Tontine\Model\User;

class TenantService
{
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
        // Set the currency in the models
        Currency::$current = $tontine->currency;
        // Set the members count in the Charge model
        Charge::$memberCount = $tontine->members()->count();

        $this->tontine = $tontine;
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
     * @return Currency
     */
    public function currency(): Currency
    {
        return $this->tontine->currency;
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
     * Get a single fund.
     *
     * @param int $fundId    The fund id
     * @param bool $with
     *
     * @return Fund|null
     */
    public function getFund(int $fundId, bool $with = false): ?Fund
    {
        $funds = $this->round->funds();
        if($with)
        {
            $funds->with(['subscriptions.receivables.deposit']);
        }
        return $funds->find($fundId);
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }
}
