<?php

namespace Ajax\App\Meeting\Session\Saving;

use Ajax\App\Meeting\MeetingComponent;
use Jaxon\Response\AjaxResponse;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\Saving\SavingService;
use Siak\Tontine\Service\Tontine\FundService;
use Siak\Tontine\Validation\Meeting\SavingValidator;
use Stringable;

use function str_replace;
use function trans;
use function trim;

/**
 * @databag meeting.saving
 * @before getFund
 */
class Amount extends MeetingComponent
{
    /**
     * @var SavingValidator
     */
    protected SavingValidator $validator;

    /**
     * The constructor
     *
     * @param LocaleService $localeService
     * @param FundService $fundService
     * @param SavingService $savingService
     */
    public function __construct(private LocaleService $localeService,
        private FundService $fundService, private SavingService $savingService)
    {}

    protected function getFund()
    {
        $fundId = (int)$this->bag('meeting.saving')->get('fund.id', 0);
        $fund = $this->fundService->getFund($fundId, true, true);
        $this->cache()->set('meeting.saving.fund', $fund);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->cache()->get('meeting.session');
        $member = $this->cache()->get('meeting.saving.member');
        $saving = $this->cache()->get('meeting.saving');

        if($session->closed)
        {
            return $this->renderView('pages.meeting.saving.member.closed', [
                'amount' => !$saving ? '' : $this->localeService->formatMoney($saving->amount, true),
            ]);
        }

        // When editing the saving amount, or when there is no saving yet,
        // then show the amount edit form.
        $edit = $this->cache()->get('meeting.saving.edit');
        if($edit || !$saving)
        {
            return $this->renderView('pages.meeting.saving.member.edit', [
                'memberId' => $member->id,
                'amount' => !$saving ? '' : $this->localeService->getMoneyValue($saving->amount),
                'rqAmount' => $this->rq(),
            ]);
        }

        return $this->renderView('pages.meeting.saving.member.show', [
            'memberId' => $member->id,
            'amount' => $this->localeService->formatMoney($saving->amount, false),
            'rqAmount' => $this->rq(),
        ]);
    }

    /**
     * @param int $memberId
     *
     * @return AjaxResponse
     */
    public function edit(int $memberId): AjaxResponse
    {
        if(!($member = $this->savingService->getMember($memberId)))
        {
            $this->notify->warning(trans('tontine.member.errors.not_found'));
            return $this->response;
        }

        $this->cache()->set('meeting.saving.edit', true);
        $this->cache()->set('meeting.saving.member', $member);
        $session = $this->cache()->get('meeting.session');
        $fund = $this->cache()->get('meeting.saving.fund');
        $this->cache()->set('meeting.saving',
            $this->savingService->findSaving($session, $fund, $member));

        return $this->item($member->id)->render();
    }

    /**
     * @param string $amount
     *
     * @return void
     */
    private function saveAmount(string $amount): void
    {
        $session = $this->cache()->get('meeting.session');
        $member = $this->cache()->get('meeting.saving.member');
        $fund = $this->cache()->get('meeting.saving.fund');
        $amount = str_replace(',', '.', trim($amount));

        if($amount === '')
        {
            $this->savingService->deleteMemberSaving($session, $fund, $member);
            $this->notify->success(trans('meeting.messages.deleted'));
            return;
        }

        $values = ['member' => $member->id, 'amount' => $amount, 'fund' => $fund->id];
        $values = $this->validator->validateItem($values);
        $this->savingService->saveSaving($session, $fund, $member, $values['amount']);
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('meeting.messages.saved'));
    }

    /**
     * @di $validator
     *
     * @param int $memberId
     * @param string $amount
     *
     * @return AjaxResponse
     */
    public function save(int $memberId, string $amount): AjaxResponse
    {
        if(!($member = $this->savingService->getMember($memberId)))
        {
            $this->notify->warning(trans('tontine.member.errors.not_found'));
            return $this->response;
        }

        $this->cache()->set('meeting.saving.member', $member);

        $this->saveAmount($amount);

        $session = $this->cache()->get('meeting.session');
        $fund = $this->cache()->get('meeting.saving.fund');
        $this->cache()->set('meeting.saving',
            $this->savingService->findSaving($session, $fund, $member));

        $this->cl(MemberTotal::class)->render();

        return $this->item($member->id)->render();
    }
}
