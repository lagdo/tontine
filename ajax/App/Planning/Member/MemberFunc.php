<?php

namespace Ajax\App\Planning\Member;

use Ajax\App\Planning\FuncComponent;
use Siak\Tontine\Service\Planning\MemberService;
use Siak\Tontine\Service\LocaleService;

use function trans;

/**
 * @databag planning.member
 */
class MemberFunc extends FuncComponent
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    public function __construct(private MemberService $memberService)
    {}

    public function enable(int $defId): void
    {
        $round = $this->stash()->get('tenant.round');
        $this->memberService->enableMember($round, $defId);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.member.messages.enabled'));

        $this->cl(MemberPage::class)->page();
        $this->cl(MemberCount::class)->render();
    }

    public function disable(int $defId): void
    {
        $round = $this->stash()->get('tenant.round');
        $this->memberService->disableMember($round, $defId);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.member.messages.removed'));

        $this->cl(MemberPage::class)->page();
        $this->cl(MemberCount::class)->render();
    }
}
