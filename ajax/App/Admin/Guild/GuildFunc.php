<?php

namespace Ajax\App\Admin\Guild;

use Ajax\App\Page\MainTitle;
use Ajax\App\Page\Sidebar\AdminMenu;
use Ajax\FuncComponent;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Tontine\GuildService;
use Siak\Tontine\Service\Tontine\MemberService;
use Siak\Tontine\Validation\Guild\GuildValidator;

use function Jaxon\pm;
use function collect;
use function trans;

/**
 * @databag tontine
 */
class GuildFunc extends FuncComponent
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @var MemberService
     */
    protected MemberService $memberService;

    /**
     * @var GuildValidator
     */
    protected GuildValidator $validator;

    /**
     * @param GuildService $guildService
     */
    public function __construct(private GuildService $guildService)
    {}

    /**
     * @di $localeService
     */
    public function add()
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
            'click' => $this->rq()->create(pm()->form('guild-form')),
        ]];

        $this->modal()->hide();
        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @di $validator
     */
    public function create(array $formValues)
    {
        $values = $this->validator->validateItem($formValues);
        $this->guildService->createGuild($values);

        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.messages.created'));
        $this->cl(GuildPage::class)->page(); // Back to current page

        $this->cl(MainTitle::class)->render();
        $this->cl(AdminMenu::class)->render();
    }

    /**
     * @di $localeService
     */
    public function edit(int $guildId)
    {
        $guild = $this->guildService->getGuild($guildId);

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
            'click' => $this->rq()->update($guild->id, pm()->form('guild-form')),
        ]];

        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @di $validator
     */
    public function update(int $guildId, array $formValues)
    {
        $values = $this->validator->validateItem($formValues);
        $this->guildService->updateGuild($guildId, $values);

        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.messages.updated'));
        $this->cl(GuildPage::class)->page(); // Back to current page
    }

    public function delete(int $guildId)
    {
        $this->guildService->deleteGuild($guildId);

        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.messages.deleted'));
        $this->cl(GuildPage::class)->page(); // Back to current page

        $this->cl(MainTitle::class)->render();
        $this->cl(AdminMenu::class)->render();
    }
}
