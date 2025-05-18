<?php

namespace Ajax\App\Meeting\Session\Saving;

use Ajax\App\Meeting\Session\FuncComponent;
use Siak\Tontine\Model\Member as MemberModel;
use Siak\Tontine\Service\Meeting\Saving\SavingService;
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
    use FundTrait;

    /**
     * @var SavingValidator
     */
    protected SavingValidator $validator;

    /**
     * The constructor
     *
     * @param SavingService $savingService
     */
    public function __construct(protected SavingService $savingService)
    {}

    /**
     * @param int $memberId
     *
     * @return void
     */
    public function edit(int $memberId)
    {
        $round = $this->stash()->get('tenant.round');
        if(!($member = $this->savingService->getMember($round, $memberId)))
        {
            $this->alert()->warning(trans('tontine.member.errors.not_found'));
            return;
        }

        $this->stash()->set('meeting.saving.edit', true);
        $this->stash()->set('meeting.saving.member', $member);
        $session = $this->stash()->get('meeting.session');
        $fund = $this->getStashedFund();
        $this->stash()->set('meeting.saving',
            $this->savingService->findSaving($session, $fund, $member));

        $this->cl(Amount::class)->item($member->id)->render();
    }

    /**
     * @param MemberModel $member
     * @param string $amount
     *
     * @return void
     */
    private function saveAmount(MemberModel $member, string $amount): void
    {
        $session = $this->stash()->get('meeting.session');
        $fund = $this->getStashedFund();
        $amount = str_replace(',', '.', trim($amount));
        if($amount === '' || $amount === '0')
        {
            $this->savingService->deleteMemberSaving($session, $fund, $member);
            $this->alert()->success(trans('meeting.messages.deleted'));
            return;
        }

        $values = [
            'fund' => $fund->id,
            'member' => $member->id,
            'amount' => $amount,
        ];
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
        $round = $this->stash()->get('tenant.round');
        if(!($member = $this->savingService->getMember($round, $memberId)))
        {
            $this->alert()->warning(trans('tontine.member.errors.not_found'));
            return;
        }

        $this->stash()->set('meeting.saving.member', $member);

        $this->saveAmount($member, $amount);

        $session = $this->stash()->get('meeting.session');

        $this->getFund(); // Refresh the modified data.
        $fund = $this->getStashedFund();
        $this->stash()->set('meeting.saving',
            $this->savingService->findSaving($session, $fund, $member));

        $this->cl(MemberTotal::class)->render();
        $this->cl(Amount::class)->item($member->id)->render();
    }

    /**
     * @di $validator
     *
     * @param string $amount
     *
     * @return void
     */
    public function saveStartAmount(string $amount)
    {
        $session = $this->stash()->get('meeting.session');
        $fund = $this->getStashedFund();
        // The start amount can be set only on the start session.
        if($fund->start_sid !== $session->id)
        {
            return;
        }

        $values = $this->validator->validateOptions(['amount' => $amount]);
        $this->savingService->saveFundStartAmount($fund, $values['amount']);

        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('meeting.saving.messages.amount_saved'));
    }

    /**
     * @di $validator
     *
     * @param string $amount
     *
     * @return void
     */
    public function saveEndAmount(string $amount)
    {
        $session = $this->stash()->get('meeting.session');
        $fund = $this->getStashedFund();
        // The end amount can be set only on the end session.
        if($fund->end_sid !== $session->id)
        {
            return;
        }

        $values = $this->validator->validateOptions(['amount' => $amount]);
        $this->savingService->saveFundEndAmount($fund, $values['amount']);

        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('meeting.saving.messages.amount_saved'));
    }

    /**
     * @di $validator
     *
      * @param string $amount
     *
     * @return void
     */
    public function saveProfitAmount(string $amount)
    {
        $session = $this->stash()->get('meeting.session');
        $fund = $this->getStashedFund();
        // The profit amount can be set only on the end session.
        if($fund->end_sid !== $session->id)
        {
            return;
        }

        $values = $this->validator->validateOptions(['amount' => $amount]);
        $this->savingService->saveFundProfitAmount($fund, $values['amount']);

        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('meeting.saving.messages.amount_saved'));
    }
}
