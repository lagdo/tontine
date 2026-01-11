<?php

namespace Ajax\App\Admin\Guild;

use Ajax\Base\FuncComponent;
use Ajax\Page\Header\GuildHeader;
use Ajax\Page\Header\MenuFunc;
use Ajax\Page\Sidebar\AdminMenu;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Inject;
use Siak\Tontine\Model\Guild as GuildModel;
use Siak\Tontine\Service\Guild\GuildService;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Validation\Guild\GuildValidator;

use function je;
use function collect;
use function trans;

#[Databag('admin')]
class GuildFunc extends FuncComponent
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @var GuildValidator
     */
    protected GuildValidator $validator;

    /**
     * @param GuildService $guildService
     */
    public function __construct(private GuildService $guildService)
    {}

    #[Inject(attr: 'localeService')]
    public function add(): void
    {
        $title = trans('tontine.titles.add');
        $content = $this->renderView('pages.admin.guild.add', [
            'countries' => $this->localeService->getCountries(),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->create(je('guild-form')->rd()->form()),
        ]];

        $this->modal()->hide();
        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @param GuildModel $guild
     *
     * @return void
     */
    private function guildCreated(GuildModel $guild): void
    {
        $user = $this->tenantService->user();
        if(!$this->tenantService->guild() ||
            $this->guildService->getGuildCount($user) === 0)
        {
            $this->cl(MenuFunc::class)->setCurrentGuild($guild);
        }
    }

    #[Inject(attr: 'validator')]
    public function create(array $formValues): void
    {
        $values = $this->validator->validateItem($formValues);
        $user = $this->tenantService->user();
        $guild = $this->guildService->createGuild($user, $values);

        $this->guildCreated($guild);
        $this->cl(GuildHeader::class)->render();
        $this->cl(AdminMenu::class)->render();

        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.messages.created'));
        $this->cl(GuildPage::class)->page(); // Back to current page
    }

    #[Inject(attr: 'localeService')]
    public function edit(int $guildId): void
    {
        $user = $this->tenantService->user();
        $guild = $this->guildService->getGuild($user, $guildId);

        $title = trans('tontine.titles.edit');
        [, $currencies] = $this->localeService->getNamesFromGuilds(collect([$guild]));
        $content = $this->renderView('pages.admin.guild.edit', [
            'guild' => $guild,
            'countries' => $this->localeService->getCountries(),
            'currencies' => $currencies
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->update($guild->id, je('guild-form')->rd()->form()),
        ]];

        $this->modal()->show($title, $content, $buttons);
    }

    #[Inject(attr: 'validator')]
    public function update(int $guildId, array $formValues): void
    {
        $values = $this->validator->validateItem($formValues);
        $user = $this->tenantService->user();
        $this->guildService->updateGuild($user, $guildId, $values);

        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.messages.updated'));
        $this->cl(GuildPage::class)->page(); // Back to current page
    }

    /**
     * @return void
     */
    private function guildDeleted(int $guildId): void
    {
        $user = $this->tenantService->user();
        $currentGuild = $this->tenantService->guild();
        if($currentGuild !== null && $currentGuild->id === $guildId &&
            ($firstGuild = $this->guildService->getFirstGuild($user)) !== null)
        {
            $this->cl(MenuFunc::class)->setCurrentGuild($firstGuild);
        }
    }

    public function delete(int $guildId): void
    {
        $user = $this->tenantService->user();
        $this->guildService->deleteGuild($user, $guildId);

        $this->guildDeleted($guildId);
        $this->cl(GuildHeader::class)->render();
        $this->cl(AdminMenu::class)->render();

        $this->cl(GuildPage::class)->page(); // Back to current page
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.messages.deleted'));
    }
}
