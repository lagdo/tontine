<?php

namespace Siak\Tontine\Service\Guild;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\FundDef;
use Siak\Tontine\Model\Guild;
use Siak\Tontine\Model\User;
use Siak\Tontine\Service\TenantService;

use function in_array;
use function tap;

class GuildService
{
    /**
     * @param TenantService $tenantService
     */
    public function __construct(protected TenantService $tenantService)
    {}

    /**
     * Get a paginated list of guilds in the selected round.
     *
     * @param User $user
     * @param int $page
     *
     * @return Collection
     */
    public function getGuilds(User $user, int $page = 0): Collection
    {
        return $user->guilds()
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * Get the number of guilds in the selected round.
     *
     * @param User $user
     *
     * @return int
     */
    public function getGuildCount(User $user): int
    {
        return $user->guilds()->count();
    }

    /**
     * Get a single guild.
     *
     * @param User $user
     * @param int $guildId    The guild id
     *
     * @return Guild|null
     */
    public function getGuild(User $user, int $guildId): ?Guild
    {
        return $user->guilds()->find($guildId);
    }

    /**
     * @param User $user
     *
     * @return Builder|Relation
     */
    public function getGuestGuildsQuery(User $user): Builder|Relation
    {
        return Guild::whereHas('invites', fn(Builder $query) =>
            $query->where('guest_id', $user->id));
    }

    /**
     * Check if the user has guest guilds
     *
     * @param User $user
     *
     * @return bool
     */
    public function hasGuestGuilds(User $user): bool
    {
        return $this->getGuestGuildsQuery($user)->exists();
    }

    /**
     * Get a paginated list of guest guilds.
     *
     * @param User $user
     * @param int $page
     *
     * @return Collection
     */
    public function getGuestGuilds(User $user, int $page = 0): Collection
    {
        return $this->getGuestGuildsQuery($user)
            ->with(['user'])
            ->orderBy('guilds.id')
            ->page($page, $this->tenantService->getLimit())
            ->get()
            ->each(fn($guild) => $guild->isGuest = true);
    }

    /**
     * Get the number of guest guilds.
     *
     * @param User $user
     *
     * @return int
     */
    public function getGuestGuildCount(User $user): int
    {
        return $this->getGuestGuildsQuery($user)->count();
    }

    /**
     * Get a single guild.
     *
     * @param User $user
     * @param int $guildId    The guild id
     *
     * @return Guild|null
     */
    public function getGuestGuild(User $user, int $guildId): ?Guild
    {
        return tap($this->getGuestGuildsQuery($user)->find($guildId),
            fn($guild) => $guild !== null && $guild->isGuest = true);
    }

    /**
     * Get a single guild.
     *
     * @param User $user
     * @param int $guildId    The guild id
     *
     * @return Guild|null
     */
    public function getUserOrGuestGuild(User $user, int $guildId): ?Guild
    {
        return $this->getGuild($user, $guildId) ??
            $this->getGuestGuild($user, $guildId);
    }

    /**
     * @param User $user
     *
     * @return Guild|null
     */
    public function getFirstGuild(User $user): ?Guild
    {
        return $this->getGuilds($user)->first() ??
            $this->getGuestGuilds($user)->first();
    }

    /**
     * Add a new guild.
     *
     * @param User $user
     * @param array $values
     *
     * @return Guild|null
     */
    public function createGuild(User $user, array $values): ?Guild
    {
        return DB::transaction(function() use($user, $values) {
            $guild = $user->guilds()->create($values);

            // Also create the default savings fund for the new guild.
            $guild->funds()->create([
                'type' => FundDef::TYPE_AUTO,
                'title' => '',
                'active' => true,
            ]);

            return $guild;
        });
    }

    /**
     * Update a guild.
     *
     * @param User $user
     * @param int $guildId
     * @param array $values
     *
     * @return bool
     */
    public function updateGuild(User $user, int $guildId, array $values): bool
    {
        return $user->guilds()->where('id', $guildId)->update($values);
    }

    /**
     * Delete a guild.
     *
     * @param User $user
     * @param int $guildId
     *
     * @return void
     */
    public function deleteGuild(User $user, int $guildId)
    {
        if(!($guild = $user->guilds()->find($guildId)))
        {
            return;
        }

        DB::transaction(function() use($guild) {
            $guild->funds()->delete();
            $guild->members()->delete();
            $guild->rounds()->delete();
            $guild->charges()->delete();
            $guild->categories()->delete();
            $guild->invites()->detach();
            $guild->delete();
        });
    }

    /**
     * Get the guild options
     *
     * @param Guild $guild
     *
     * @return array
     */
    public function getGuildOptions(Guild $guild): array
    {
        return $guild->properties;
    }

    /**
     * Get the report template name
     *
     * @return string
     */
    public function getReportTemplate(Guild $guild): string
    {
        $options = $this->getGuildOptions($guild);
        $template = $options['reports']['template'] ?? 'raptor';
        return in_array($template, ['raptor', 'legacy']) ? $template : 'raptor';
    }

    /**
     * Save the guild options
     *
     * @param Guild $guild
     * @param array $options
     *
     * @return void
     */
    public function saveGuildOptions(Guild $guild, array $options)
    {
        $properties = $guild->properties;
        $properties['reports'] = $options['reports'];
        $guild->saveProperties($properties);
    }
}
