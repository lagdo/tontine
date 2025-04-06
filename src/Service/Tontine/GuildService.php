<?php

namespace Siak\Tontine\Service\Tontine;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Guild;
use Siak\Tontine\Service\TenantService;

use function config;
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
     * @param int $page
     *
     * @return Collection
     */
    public function getGuilds(int $page = 0): Collection
    {
        return $this->tenantService->user()->guilds()
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * Get the number of guilds in the selected round.
     *
     * @return int
     */
    public function getGuildCount(): int
    {
        return $this->tenantService->user()->guilds()->count();
    }

    /**
     * Get a single guild.
     *
     * @param int $guildId    The guild id
     *
     * @return Guild|null
     */
    public function getGuild(int $guildId): ?Guild
    {
        return $this->tenantService->user()->guilds()->find($guildId);
    }

    /**
     * @return Builder|Relation
     */
    public function getGuestGuildsQuery(): Builder|Relation
    {
        return Guild::whereHas('invites', function(Builder $query) {
            $query->where('guest_id', $this->tenantService->user()->id);
        });
    }

    /**
     * Check if the user has guest guilds
     *
     * @return bool
     */
    public function hasGuestGuilds(): bool
    {
        return $this->getGuestGuildsQuery()->exists();
    }

    /**
     * Get a paginated list of guest guilds.
     *
     * @param int $page
     *
     * @return Collection
     */
    public function getGuestGuilds(int $page = 0): Collection
    {
        return $this->getGuestGuildsQuery()
            ->with(['user'])
            ->orderBy('guilds.id')
            ->page($page, $this->tenantService->getLimit())
            ->get()
            ->each(fn($guild) => $guild->isGuest = true);
    }

    /**
     * Get the number of guest guilds.
     *
     * @return int
     */
    public function getGuestGuildCount(): int
    {
        return $this->getGuestGuildsQuery()->count();
    }

    /**
     * Get a single guild.
     *
     * @param int $guildId    The guild id
     *
     * @return Guild|null
     */
    public function getGuestGuild(int $guildId): ?Guild
    {
        return tap($this->getGuestGuildsQuery()->find($guildId), function($guild) {
            if($guild !== null)
            {
                $guild->isGuest = true;
            }
        });
    }

    /**
     * Get a single guild.
     *
     * @param int $guildId    The guild id
     *
     * @return Guild|null
     */
    public function getUserOrGuestGuild(int $guildId): ?Guild
    {
        return $this->getGuild($guildId) ?? $this->getGuestGuild($guildId);
    }

    /**
     * @return Guild|null
     */
    public function getFirstGuild(): ?Guild
    {
        return $this->getGuilds()->first() ?? $this->getGuestGuilds()->first();
    }

    /**
     * Add a new guild.
     *
     * @param array $values
     *
     * @return bool
     */
    public function createGuild(array $values): bool
    {
        DB::transaction(function() use($values) {
            $guild = $this->tenantService->user()->guilds()->create($values);
            // Also create the default savings fund for the new guild.
            $guild->funds()->create(['title' => '', 'active' => true]);
        });
        return true;
    }

    /**
     * Update a guild.
     *
     * @param int $id
     * @param array $values
     *
     * @return bool
     */
    public function updateGuild(int $id, array $values): bool
    {
        return $this->tenantService->user()->guilds()->where('id', $id)->update($values);
    }

    /**
     * Delete a guild.
     *
     * @param int $id
     *
     * @return void
     */
    public function deleteGuild(int $id)
    {
        $guild = $this->tenantService->user()->guilds()->find($id);
        if(!$guild)
        {
            return;
        }
        DB::transaction(function() use($guild) {
            $guild->funds()->withoutGlobalScope('user')->delete();
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
     * @return array
     */
    public function getGuildOptions(): array
    {
        return $this->tenantService->guild()->properties;
    }

    /**
     * Get the report template name
     *
     * @return string
     */
    public function getReportTemplate(): string
    {
        $options = $this->getGuildOptions();
        return $options['reports']['template'] ?? config('tontine.templates.report', 'default');
    }

    /**
     * Save the guild options
     *
     * @param array $options
     *
     * @return void
     */
    public function saveGuildOptions(array $options)
    {
        $guild = $this->tenantService->guild();
        $properties = $guild->properties;
        $properties['reports'] = $options['reports'];
        $guild->saveProperties($properties);
    }
}
