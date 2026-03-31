<?php

namespace Ajax\Base\Guild;

use Siak\Tontine\Model\Guild as GuildModel;

use function view;

trait ComponentTrait
{
    /**
     * @return void
     */
    protected function getCurrentGuild(): void
    {
        $guildId = $this->bag('tenant')->get('guild.id', 0);
        $guild = $guildId <= 0 ? null : $this->tenantService->getGuild($guildId);
        if($guild === null)
        {
            // Go back to the Admin section.
        }

        $this->tenantService->setGuild($guild);
        $this->stash()->set('tenant.guild', $guild);
        view()->share('currentGuild', $guild);
    }

    /**
     * @return GuildModel
     */
    protected function guild(): GuildModel
    {
        return $this->stash()->get('tenant.guild');
    }
}
