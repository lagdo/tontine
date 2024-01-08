<?php

namespace App\Ajax\Web\Meeting\Saving;

use App\Ajax\CallableSessionClass;
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
 * @before getFundId
 */
class Member extends CallableSessionClass
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
     * @var int
     */
    private $fundId = 0;

    /**
     * The constructor
     *
     * @param FundService $fundService
     * @param SavingService $savingService
     */
    public function __construct(private FundService $fundService,
        private SavingService $savingService)
    {}

    protected function getFundId()
    {
        $this->fundId = (int)($this->target()->method() === 'home' ?
            $this->target()->args()[0] :
            $this->bag('meeting.saving')->get('fund.id', 0));
    }

    /**
     * @param int $fundId
     *
     * @return mixed
     */
    public function home(int $fundId)
    {
        $fund = $fundId > 0 ? $this->fundService->getFund($fundId) : null;
        $fundId = !$fund ? 0 : $fund->id;
        $this->bag('meeting.saving')->set('fund.id', $fundId);
        $this->bag('meeting.saving')->set('member.filter', null);

        $html = $this->render('pages.meeting.saving.member.home', [
            'fund' => $fund,
        ]);
        $this->response->html('meeting-savings', $html);
        $this->jq('#btn-saving-back')->click($this->cl(Saving::class)->rq()->home());
        $this->jq('#btn-saving-filter')->click($this->rq()->toggleFilter());

        return $this->page(1);
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
        $memberCount = $this->savingService->getMemberCount($this->session, $this->fundId,
            $search, $filter);
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $memberCount,
            'meeting', 'member.page');
        $members = $this->savingService->getMembers($this->session, $this->fundId,
            $search, $filter, $pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $memberCount);

        $savingCount = $this->savingService->getSavingCount($this->session, $this->fundId);
        $savingSum = $this->savingService->getSavingSum($this->session, $this->fundId);
        $html = $this->render('pages.meeting.saving.member.page', [
            'search' => $search,
            'session' => $this->session,
            'members' => $members,
            'savingCount' => $savingCount,
            'savingSum' => $savingSum,
            'pagination' => $pagination,
        ]);
        $this->response->html('meeting-saving-members', $html);

        $memberId = jq()->parent()->attr('data-member-id')->toInt();
        $amount = jq('input', jq()->parent()->parent())->val()->toInt();
        $this->jq('.btn-save-saving')->click($this->rq()->saveSaving($memberId, $amount));
        $this->jq('.btn-edit-saving')->click($this->rq()->editSaving($memberId));
        $this->jq('#btn-saving-search')
            ->click($this->rq()->search(jq('#txt-fee-member-search')->val()));

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
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }
        $saving = $this->savingService->findSaving($this->session, $this->fundId, $memberId);
        $amount = !$saving ? '' : $this->localeService->getMoneyValue($saving->amount);

        $html = $this->render('pages.meeting.saving.member.edit', [
            'memberId' => $memberId,
            'amount' => $amount,
        ]);
        $fieldId = 'saving-member-' . $memberId;
        $this->response->html($fieldId, $html);

        $memberId = jq()->parent()->attr('data-member-id')->toInt();
        $amount = jq('input', jq()->parent()->parent())->val();
        $this->jq('.btn-save-saving', "#$fieldId")->click($this->rq()->saveSaving($memberId, $amount));

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
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $amount = str_replace(',', '.', trim($amount));
        if($amount === '')
        {
            $this->savingService->deleteSaving($this->session, 0, $memberId);

            $this->notify->success(trans('meeting.messages.deleted'));
            return $this->page();
        }

        $values = ['member' => $memberId, 'amount' => $amount, 'fund' => $this->fundId];
        $values = $this->validator->validateItem($values);
        $amount = $values['amount'];
        $this->savingService->saveSaving($this->session, $this->fundId, $memberId, $amount);

        $this->notify->success(trans('meeting.messages.saved'));
        return $this->page();
    }
}
