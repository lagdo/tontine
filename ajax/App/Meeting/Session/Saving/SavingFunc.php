<?php

namespace Ajax\App\Meeting\Session\Saving;

use Ajax\App\Meeting\FuncComponent;
use Ajax\App\Meeting\Session\FundTrait;
use Siak\Tontine\Service\Guild\MemberService;
use Siak\Tontine\Service\Meeting\Saving\SavingService;
use Siak\Tontine\Validation\Meeting\SavingValidator;

use function Jaxon\pm;
use function trans;

/**
 * @databag meeting.saving
 * @before getFund
 */
class SavingFunc extends FuncComponent
{
    use FundTrait;

    /**
     * @var string
     */
    protected string $bagId = 'meeting.saving';

    /**
     * @var SavingValidator
     */
    protected SavingValidator $validator;

    /**
     * The constructor
     *
     * @param SavingService $savingService
     * @param MemberService $memberService
     */
    public function __construct(protected SavingService $savingService,
        protected MemberService $memberService)
    {}

    public function edit(int $savingId)
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
            'click' => $this->rq()->update($savingId, pm()->form('saving-form')),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @di $validator
     * @before getFund
     */
    public function update(int $savingId, array $formValues)
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
    public function delete(int $savingId)
    {
        $session = $this->stash()->get('meeting.session');
        $this->savingService->deleteSaving($session, $savingId);

        $this->cl(SavingTotal::class)->render();
        $this->cl(SavingPage::class)->page();
    }
}
