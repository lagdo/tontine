<?php

namespace Ajax\App\Meeting\Session\Saving;

use Ajax\App\Meeting\FuncComponent;
use Siak\Tontine\Service\Meeting\Saving\SavingService;
use Siak\Tontine\Service\Tontine\FundService;
use Siak\Tontine\Validation\Meeting\SavingValidator;

use function str_replace;
use function trans;
use function trim;

/**
 * @databag meeting.saving
 * @before getFund
 */
class AmountFunc extends FuncComponent
{
    /**
     * @var SavingValidator
     */
    protected SavingValidator $validator;

    /**
     * The constructor
     *
     * @param FundService $fundService
     * @param SavingService $savingService
     */
    public function __construct(private FundService $fundService,
        private SavingService $savingService)
    {}

    protected function getFund()
    {
        $fundId = (int)$this->bag('meeting.saving')->get('fund.id', 0);
        $fund = $this->fundService->getFund($fundId, true, true);
        $this->stash()->set('meeting.saving.fund', $fund);
    }

    /**
     * @param int $memberId
     *
     * @return void
     */
    public function edit(int $memberId)
    {
        if(!($member = $this->savingService->getMember($memberId)))
        {
            $this->alert()->warning(trans('tontine.member.errors.not_found'));
            return;
        }

        $this->stash()->set('meeting.saving.edit', true);
        $this->stash()->set('meeting.saving.member', $member);
        $session = $this->stash()->get('meeting.session');
        $fund = $this->stash()->get('meeting.saving.fund');
        $this->stash()->set('meeting.saving',
            $this->savingService->findSaving($session, $fund, $member));

        $this->cl(Amount::class)->item($member->id)->render();
    }

    /**
     * @param string $amount
     *
     * @return void
     */
    private function saveAmount(string $amount): void
    {
        $session = $this->stash()->get('meeting.session');
        $member = $this->stash()->get('meeting.saving.member');
        $fund = $this->stash()->get('meeting.saving.fund');
        $amount = str_replace(',', '.', trim($amount));

        if($amount === '')
        {
            $this->savingService->deleteMemberSaving($session, $fund, $member);
            $this->alert()->success(trans('meeting.messages.deleted'));
            return;
        }

        $values = ['member' => $member->id, 'amount' => $amount, 'fund' => $fund->id];
        $values = $this->validator->validateItem($values);
        $this->savingService->saveSaving($session, $fund, $member, $values['amount']);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('meeting.messages.saved'));
    }

    /**
     * @di $validator
     *
     * @param int $memberId
     * @param string $amount
     *
     * @return void
     */
    public function save(int $memberId, string $amount)
    {
        if(!($member = $this->savingService->getMember($memberId)))
        {
            $this->alert()->warning(trans('tontine.member.errors.not_found'));
            return;
        }

        $this->stash()->set('meeting.saving.member', $member);

        $this->saveAmount($amount);

        $session = $this->stash()->get('meeting.session');
        $fund = $this->stash()->get('meeting.saving.fund');
        $this->stash()->set('meeting.saving',
            $this->savingService->findSaving($session, $fund, $member));

        $this->cl(MemberTotal::class)->render();
        $this->cl(Amount::class)->item($member->id)->render();
    }
}
