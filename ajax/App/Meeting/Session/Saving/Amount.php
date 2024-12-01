<?php

namespace Ajax\App\Meeting\Session\Saving;

use Ajax\App\Meeting\MeetingComponent;
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
        $this->cache->set('meeting.saving.fund', $fund);
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->cache->get('meeting.session');
        $fund = $this->cache->get('meeting.saving.fund');
        $member = $this->cache->get('meeting.saving.member');
        $saving = $this->savingService->findSaving($session, $fund, $member);

        return $this->renderView('pages.meeting.saving.member.edit', [
            'memberId' => $member->id,
            'amount' => !$saving ? '' : $this->localeService->getMoneyValue($saving->amount),
            'rqAmount' => $this->rq(),
        ]);
    }

    /**
     * @param int $memberId
     *
     * @return mixed
     */
    public function edit(int $memberId)
    {
        if(!($member = $this->savingService->getMember($memberId)))
        {
            $this->notify->warning(trans('tontine.member.errors.not_found'));
            return $this->response;
        }

        $this->cache->set('meeting.saving.member', $member);

        return $this->item($member->id)->render();
    }

    /**
     * @di $validator
     *
     * @param int $memberId
     * @param string $amount
     *
     * @return mixed
     */
    public function save(int $memberId, string $amount)
    {
        if(!($member = $this->savingService->getMember($memberId)))
        {
            $this->notify->warning(trans('tontine.member.errors.not_found'));
            return $this->response;
        }

        $session = $this->cache->get('meeting.session');
        $fund = $this->cache->get('meeting.saving.fund');
        $amount = str_replace(',', '.', trim($amount));
        if($amount === '')
        {
            $this->savingService->deleteMemberSaving($session, $fund, $member);

            $this->notify->success(trans('meeting.messages.deleted'));

            return $this->cl(MemberPage::class)->page();
        }

        $values = ['member' => $memberId, 'amount' => $amount, 'fund' => $fund->id];
        $values = $this->validator->validateItem($values);
        $this->savingService->saveSaving($session, $fund, $member, $values['amount']);

        $this->notify->title(trans('common.titles.success'))
            ->success(trans('meeting.messages.saved'));

        $this->cl(MemberTotal::class)->render();

        return $this->cl(MemberPage::class)->page();
    }
}
