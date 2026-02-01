<?php

namespace Siak\Tontine\Service;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Guild;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\User;

use function is_array;

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
     * @return Builder|Relation
     */
    private function getGuildQuery(): Builder|Relation
    {
        return Guild::query()->where(fn(Builder $guild) =>
            $guild->where('user_id', $this->user->id)
                ->orWhereHas('invites', fn(Builder $invite) =>
                    $invite->where('guest_id', $this->user->id)));
    }

    /**
     * @return Collection
     */
    public function getGuilds(): Collection
    {
        return $this->getGuildQuery()->pluck('name', 'id');
    }

    /**
     * @param int $guildId    The guild id
     *
     * @return Guild|null
     */
    public function getGuild(int $guildId): ?Guild
    {
        return $this->getGuildQuery()->find($guildId);
    }

    /**
     * @return int
     */
    public function getLatestGuildId(): int
    {
        return $this->user?->properties['latest']['guild'] ?? 0;
    }

    /**
     * @return Guild|null
     */
    public function getLatestGuild(): Guild|null
    {
        $guildId = $this->getLatestGuildId();
        return $guildId > 0 ? $this->getGuild($guildId) : null;
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
        if($this->user === null || $this->getLatestGuildId() === $guild->id)
        {
            return;
        }

        $properties = $this->user->properties;
        $properties['latest']['guild'] = $guild->id;
        $this->user->saveProperties($properties);
    }

    /**
     * Get a list of rounds for the dropdown select.
     *
     * @return Collection
     */
    public function getRounds(): Collection
    {
        // Only rounds with at least 2 sessions are selectable.
        return $this->guild->rounds()
            ->join('sessions', 'sessions.round_id', '=', 'rounds.id')
            ->select('rounds.title', 'rounds.id', DB::raw('count(sessions.id)'))
            ->groupBy('rounds.title', 'rounds.id')
            ->havingRaw('count(sessions.id) > ?', [1])
            ->pluck('title', 'id');
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
    public function getLatestRoundId(): int
    {
        return $this->user?->properties['latest']['round'][$this->guild?->id ?? 0] ?? 0;
    }

    /**
     * @return Round|null
     */
    public function getLatestRound(): Round|null
    {
        $roundId = $this->getLatestRoundId();
        return $roundId > 0 ? $this->getRound($roundId) : null;
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
        if($this->user === null || $this->getLatestRoundId() === $round->id)
        {
            return;
        }

        $properties = $this->user->properties;
        if(!is_array($properties['latest']['round'] ?? []))
        {
            // Discard any previous value which is not an array.
            $properties['latest']['round'] = [];
        }
        $properties['latest']['round'][$this->guild->id] = $round->id;
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

        return $userInvite->options->access;
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

    /**
     * @param int $roundId
     * 
     * @return Round|null
     */
    public function getRoundById(int $roundId): Round|null
    {
        return $this->guild->rounds()->find($roundId);
    }

    /**
     * @param int $sessionId
     *
     * @return Session|null
     */
    public function getSessionById(int $sessionId): Session|null
    {
        $session = $this->guild->sessions()->with('round')->find($sessionId);
        if($session !== null)
        {
            $this->setRound($session->round);
        }
        return $session;
    }
}
