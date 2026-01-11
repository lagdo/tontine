<?php

namespace Ajax\App\Planning\Member;

use Ajax\App\Planning\FuncComponent;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Planning\MemberService;
use Siak\Tontine\Service\LocaleService;

use function trans;

#[Databag('planning.member')]
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
        $this->memberService->enableMember($this->round(), $defId);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.member.messages.enabled'));

        $this->cl(MemberPage::class)->page();
        $this->cl(MemberCount::class)->render();
    }

    public function disable(int $defId): void
    {
        $this->memberService->disableMember($this->round(), $defId);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.member.messages.removed'));

        $this->cl(MemberPage::class)->page();
        $this->cl(MemberCount::class)->render();
    }
}
