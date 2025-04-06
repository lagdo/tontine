<?php

namespace Siak\Tontine\Service;

use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Guild;
use Siak\Tontine\Model\User;

class TenantService
{
    /**
     * @var User|null
     */
    protected ?User $user = null;

    /**
     * @var Guild|null
     */
    protected ?Guild $guild = null;

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
     * @param Guild $guild
     *
     * @return void
     */
    public function setGuild(Guild $guild): void
    {
        $this->guild = $guild;
        // Set the currency for locales.
        $this->localeService->setCurrency($guild->currency_code);
        // Save as latest guild id if it has changed.
        $guildId = $this->user?->properties['latest']['guild'] ?? 0;
        if(!$this->user || $guildId === $guild->id)
        {
            return;
        }

        $properties = $this->user->properties;
        $properties['latest']['guild'] = $guild->id;
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
     * @return void
     */
    public function resetRound(): void
    {
        $this->round = null;
    }

    /**
     * @return User|null
     */
    public function user(): ?User
    {
        return $this->user;
    }

    /**
     * @return Guild|null
     */
    public function guild(): ?Guild
    {
        return $this->guild;
    }

    /**
     * @return Round|null
     */
    public function round(): ?Round
    {
        return $this->round;
    }

    /**
     * @param int $roundId    The round id
     *
     * @return Round|null
     */
    public function getRound(int $roundId): ?Round
    {
        return $this->guild->rounds()->find($roundId);
    }

    /**
     * @return Round|null
     */
    public function getFirstRound(): ?Round
    {
        return $this->guild->rounds()->first();
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return bool
     */
    private function userIsGuest(): bool
    {
        return $this->guild !== null && $this->guild->isGuest;
    }

    /**
     * @return array
     */
    private function getHostAccess(): array
    {
        if(!$this->guild || !$this->user)
        {
            return [];
        }
        $userInvite = $this->guild->invites()
            ->where('guest_id', $this->user->id)
            ->first();
        if(!$userInvite)
        {
            return [];
        }

        return $userInvite->permission->access;
    }

    /**
     * Check guest user access to a menu entry in a section
     *
     * @param string $section
     * @param string $entry
     * @param bool $return
     *
     * @return bool
     * @throws MessageException
     */
    public function checkHostAccess(string $section, string $entry, bool $return = false): bool
    {
        if(!$this->userIsGuest())
        {
            return true;
        }

        $guestAccess = $this->getHostAccess();
        if(!($guestAccess[$section][$entry] ?? false))
        {
            if($return)
            {
                return false;
            }
            throw new MessageException(trans('tontine.invite.errors.access_denied'));
        }
        return true;
    }
}
