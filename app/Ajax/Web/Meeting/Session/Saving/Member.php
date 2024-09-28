<?php

namespace App\Ajax\Web\Meeting\Session\Saving;

use App\Ajax\OpenedSessionCallable;
use Siak\Tontine\Model\Fund as FundModel;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\Saving\SavingService;
use Siak\Tontine\Service\Tontine\FundService;
use Siak\Tontine\Validation\Meeting\SavingValidator;

use function Jaxon\jq;
use function str_replace;
use function trans;
use function trim;

/**
 * @databag meeting.saving
 * @before getFund
 */
class Member extends OpenedSessionCallable
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
     * @var FundModel|null
     */
    protected ?FundModel $fund = null;

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
        $this->fund = $this->fundService->getFund($fundId, true, true);
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

        $html = $this->renderView('pages.meeting.saving.member.home', [
            'fund' => $this->fund,
        ]);
        $this->response->html('meeting-savings', $html);

        $this->response->jq('#btn-saving-back')->click($this->rq(Saving::class)->home());
        $this->response->jq('#btn-saving-filter')->click($this->rq()->toggleFilter());
        $this->response->jq('#btn-saving-search')
            ->click($this->rq()->search(jq('#txt-fee-member-search')->val()));

        return $this->page(1);
    }

    private function showTotal()
    {
        $savingCount = $this->savingService->getSavingCount($this->session, $this->fund);
        $savingTotal = $this->savingService->getSavingTotal($this->session, $this->fund);
        $html = $this->renderView('pages.meeting.saving.total', [
            'savingCount' => $savingCount,
            'savingTotal' => $savingTotal,
        ]);
        $this->response->html('meeting-saving-members-total', $html);
    }

    /**
     * @param int $pageNumber
     *
     * @return mixed
     */
    public function page(int $pageNumber = 0)
    {
        $search = trim($this->bag('meeting.saving')->get('member.search', ''));
        $filter = $this->bag('meeting.saving')->get('member.filter', null);
        $memberCount = $this->savingService->getMemberCount($this->session, $this->fund,
            $search, $filter);
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $memberCount,
            'meeting', 'member.page');
        $members = $this->savingService->getMembers($this->session, $this->fund,
            $search, $filter, $pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $memberCount);

        $this->showTotal();

        $html = $this->renderView('pages.meeting.saving.member.page', [
            'session' => $this->session,
            'members' => $members,
        ]);
        $this->response->html('meeting-saving-members', $html);
        $this->response->js()->makeTableResponsive('meeting-saving-members');

        $memberId = jq()->parent()->attr('data-member-id')->toInt();
        $amount = jq('input', jq()->parent()->parent())->val()->toInt();
        $this->response->jq('.btn-save-saving')->click($this->rq()->saveSaving($memberId, $amount));
        $this->response->jq('.btn-edit-saving')->click($this->rq()->editSaving($memberId));

        return $this->response;
    }

    public function search(string $search)
    {
        $this->bag('meeting.saving')->set('member.search', trim($search));

        return $this->page();
    }

    public function toggleFilter()
    {
        $filter = $this->bag('meeting.saving')->get('member.filter', null);
        // Switch between null, true and false
        $filter = $filter === null ? true : ($filter === true ? false : null);
        $this->bag('meeting.saving')->set('member.filter', $filter);

        return $this->page();
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

        $saving = $this->savingService->findSaving($this->session, $this->fund, $member);
        $amount = !$saving ? '' : $this->localeService->getMoneyValue($saving->amount);

        $html = $this->renderView('pages.meeting.saving.member.edit', [
            'memberId' => $memberId,
            'amount' => $amount,
        ]);
        $fieldId = 'saving-member-' . $memberId;
        $this->response->html($fieldId, $html);

        $memberId = jq()->parent()->attr('data-member-id')->toInt();
        $amount = jq('input', jq()->parent()->parent())->val();
        $this->response->jq('.btn-save-saving', "#$fieldId")->click($this->rq()->saveSaving($memberId, $amount));

        return $this->response;
    }

    /**
     * @di $validator
     * @after showBalanceAmounts
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

        $amount = str_replace(',', '.', trim($amount));
        if($amount === '')
        {
            $this->savingService->deleteMemberSaving($this->session, $this->fund, $member);

            $this->notify->success(trans('meeting.messages.deleted'));
            return $this->page();
        }

        $values = ['member' => $memberId, 'amount' => $amount, 'fund' => $this->fund->id];
        $values = $this->validator->validateItem($values);
        $amount = $values['amount'];
        $this->savingService->saveSaving($this->session, $this->fund, $member, $amount);

        $this->notify->title(trans('common.titles.success'))->success(trans('meeting.messages.saved'));
        return $this->page();
    }
}
