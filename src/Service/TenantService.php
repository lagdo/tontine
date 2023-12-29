<?php

namespace Siak\Tontine\Service;

use Siak\Tontine\Model\Round;
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
     * @param LocaleService $localeService
     */
    public function __construct(protected LocaleService $localeService)
    {}

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
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }
}
