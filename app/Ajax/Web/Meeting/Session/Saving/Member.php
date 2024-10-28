<?php

namespace App\Ajax\Web\Meeting\Session\Saving;

use App\Ajax\Cache;
use App\Ajax\Web\Meeting\MeetingComponent;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\Saving\SavingService;
use Siak\Tontine\Service\Tontine\FundService;
use Siak\Tontine\Validation\Meeting\SavingValidator;

use function Jaxon\jaxon;
use function str_replace;
use function trans;
use function trim;

/**
 * @databag meeting.saving
 * @before getFund
 */
class Member extends MeetingComponent
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

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
        $fundId = (int)($this->target()->method() === 'home' ?
            $this->target()->args()[0] :
            $this->bag('meeting.saving')->get('fund.id', 0));
        $fund = $this->fundService->getFund($fundId, true, true);
        Cache::set('meeting.saving.fund', $fund);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return (string)$this->renderView('pages.meeting.saving.member.home', [
            'fund' => Cache::get('meeting.saving.fund'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(MemberPage::class)->page();
    }

    /**
     * @param int $fundId
     *
     * @return mixed
     */
    public function home(int $fundId)
    {
        $this->bag('meeting.saving')->set('fund.id', $fundId);
        $this->bag('meeting.saving')->set('member.filter', null);
        $this->bag('meeting.saving')->set('member.search', '');
        $this->bag('meeting.saving')->set('member.page', 1);

        return $this->render();
    }

    public function search(string $search)
    {
        $this->bag('meeting.saving')->set('member.search', trim($search));
        $this->bag('meeting.saving')->set('member.page', 1);

        return $this->cl(MemberPage::class)->page();
    }

    public function toggleFilter()
    {
        $filter = $this->bag('meeting.saving')->get('member.filter', null);
        // Switch between null, true and false
        $filter = $filter === null ? true : ($filter === true ? false : null);
        $this->bag('meeting.saving')->set('member.filter', $filter);

        return $this->cl(MemberPage::class)->page();
    }

    /**
     * @di $localeService
     * @param int $memberId
     *
     * @return mixed
     */
    public function editSaving(int $memberId)
    {
        if(!($member = $this->savingService->getMember($memberId)))
        {
            $this->notify->warning(trans('tontine.member.errors.not_found'));
            return $this->response;
        }

        $session = Cache::get('meeting.session');
        $fund = Cache::get('meeting.saving.fund');
        $saving = $this->savingService->findSaving($session, $fund, $member);
        $amount = !$saving ? '' : $this->localeService->getMoneyValue($saving->amount);

        $html = $this->renderView('pages.meeting.saving.member.edit', [
            'memberId' => $memberId,
            'amount' => $amount,
        ]);
        jaxon()->getResponse()->html("saving-member-$memberId", $html);

        return $this->response;
    }

    /**
     * @di $validator
     *
     * @param int $memberId
     * @param string $amount
     *
     * @return mixed
     */
    public function saveSaving(int $memberId, string $amount)
    {
        if(!($member = $this->savingService->getMember($memberId)))
        {
            $this->notify->warning(trans('tontine.member.errors.not_found'));
            return $this->response;
        }

        $session = Cache::get('meeting.session');
        $fund = Cache::get('meeting.saving.fund');
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
