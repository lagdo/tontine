<?php

namespace Ajax\App\Admin\User\Host;

use Ajax\Base\FuncComponent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Inject;
use Siak\Tontine\Service\Guild\GuildService;
use Siak\Tontine\Service\Guild\UserService;
use Siak\Tontine\Validation\Guild\HostAccessValidator;

use function trans;

#[Before('getInvite')]
#[Before('getGuild')]
#[Databag('user.access')]
class AccessFunc extends FuncComponent
{
    use AccessTrait;

    /**
     * @var HostAccessValidator
     */
    protected HostAccessValidator $validator;

    /**
     * @param UserService $userService
     * @param GuildService $guildService
     */
    public function __construct(private UserService $userService,
        private GuildService $guildService)
    {}

    /**
     * @param int $guildId
     *
     * @return void
     */
    public function guild(int $guildId): void
    {
        $this->cl(GuildAccess::class)->render();
    }

    #[Inject(attr: 'validator')]
    public function saveAccess(array $formValues): void
    {
        $invite = $this->stash()->get('user.access.invite');
        $guild = $this->stash()->get('user.access.guild');
        $access = $this->validator->validateItem($formValues['access'] ?? []);
        $this->userService->saveHostGuildAccess($invite, $guild, $access);

        $this->cl(GuildAccess::class)->render();

        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('meeting.messages.saved'));
    }
}
