<?php

namespace Ajax\App\Meeting\Session\Saving;

use Ajax\App\Meeting\MeetingComponent;
use Siak\Tontine\Service\Meeting\Saving\SavingService;
use Siak\Tontine\Service\Tontine\FundService;
use Siak\Tontine\Service\Tontine\MemberService;
use Siak\Tontine\Validation\Meeting\SavingValidator;
use Stringable;

use function Jaxon\pm;
use function trans;

/**
 * @databag meeting.saving
 */
class Saving extends MeetingComponent
{
    /**
     * @var SavingValidator
     */
    protected SavingValidator $validator;

    /**
     * The constructor
     *
     * @param SavingService $savingService
     * @param FundService $fundService
     * @param MemberService $memberService
     */
    public function __construct(protected SavingService $savingService,
        protected FundService $fundService, protected MemberService $memberService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.saving.home', [
            'session' => $this->stash()->get('meeting.session'),
            'fundId' => (int)$this->bag('meeting.saving')->get('fund.id', 0),
            'funds' => $this->fundService->getFundList()->prepend('', 0),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->fund((int)$this->bag('meeting.saving')->get('fund.id', 0));
    }

    protected function getFund()
    {
        $fund = null;
        $fundId = $this->bag('meeting.saving')->get('fund.id', 0);
        if($fundId > 0 && ($fund = $this->fundService->getFund($fundId, true, true)) === null)
        {
            $this->bag('meeting.saving')->set('fund.id', 0);
        }
        $this->stash()->set('meeting.saving.fund', $fund);
    }

    public function fund(int $fundId)
    {
        $this->bag('meeting.saving')->set('fund.id', $fundId);
        $this->bag('meeting.saving')->set('page', 1);
        $this->getFund();

        $this->cl(SavingPage::class)->page();
    }

    public function editSaving(int $savingId)
    {
        $session = $this->stash()->get('meeting.session');
        $saving = $this->savingService->getSaving($session, $savingId);
        $title = trans('meeting.saving.titles.edit');
        $content = $this->renderView('pages.meeting.saving.edit', [
            'saving' => $saving,
            'members' => $this->memberService->getMemberList(),
            'funds' => $this->fundService->getFundList(),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->updateSaving($savingId, pm()->form('saving-form')),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @di $validator
     * @before getFund
     */
    public function updateSaving(int $savingId, array $formValues)
    {
        $session = $this->stash()->get('meeting.session');
        if(!($saving = $this->savingService->getSaving($session, $savingId)))
        {
            $this->alert()->warning(trans('meeting.saving.errors.not_found'));
            return;
        }

        $values = $this->validator->validateItem($formValues);
        if(!($member = $this->savingService->getMember($values['member'])))
        {
            $this->alert()->warning(trans('tontine.member.errors.not_found'));
            return;
        }
        if(!($fund = $this->fundService->getFund($values['fund'], true, true)))
        {
            $this->alert()->warning(trans('tontine.fund.errors.not_found'));
            return;
        }

        $this->savingService->updateSaving($session, $fund, $member, $saving, $values['amount']);

        $this->modal()->hide();

        $this->cl(SavingTotal::class)->render();
        $this->cl(SavingPage::class)->page();
    }

    /**
     * @before getFund
     */
    public function deleteSaving(int $savingId)
    {
        $session = $this->stash()->get('meeting.session');
        $this->savingService->deleteSaving($session, $savingId);

        $this->cl(SavingTotal::class)->render();
        $this->cl(SavingPage::class)->page();
    }
}
