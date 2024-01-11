<?php

namespace App\Ajax\Web\Meeting\Charge\Libre;

use App\Ajax\CallableSessionClass;
use App\Ajax\Web\Meeting\Charge\LibreFee as Charge;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Charge as ChargeModel;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\Charge\LibreFeeService;
use Siak\Tontine\Service\Meeting\Charge\SettlementService;
use Siak\Tontine\Service\Tontine\ChargeService;

use function filter_var;
use function Jaxon\jq;
use function Jaxon\pm;
use function str_replace;
use function trans;
use function trim;

/**
 * @before getCharge
 */
class Member extends CallableSessionClass
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * The constructor
     *
     * @param SettlementService $settlementService
     * @param ChargeService $chargeService
     * @param LibreFeeService $feeService
     */
    public function __construct(protected SettlementService $settlementService,
        protected ChargeService $chargeService, protected LibreFeeService $feeService)
    {}

    /**
     * @var ChargeModel|null
     */
    protected ?ChargeModel $charge;

    protected function getCharge()
    {
        $chargeId = $this->target()->method() === 'home' ?
            $this->target()->args()[0] : $this->bag('meeting')->get('charge.id');
        $this->charge = $this->chargeService->getCharge($chargeId);
    }

    /**
     * @param int $chargeId
     *
     * @return mixed
     */
    public function home(int $chargeId)
    {
        $this->bag('meeting')->set('charge.id', $chargeId);
        $this->bag('meeting')->set('fee.member.filter', null);

        $html = $this->render('pages.meeting.charge.libre.member.home', [
            'charge' => $this->charge,
            'paid' => $this->charge->is_fee,
        ]);
        $this->response->html('meeting-fees-libre', $html);
        $this->jq('#btn-fee-libre-back')->click($this->cl(Charge::class)->rq()->home());
        $this->jq('#btn-fee-libre-filter')->click($this->rq()->toggleFilter());

        return $this->page(1);
    }

    /**
     * @param int $pageNumber
     *
     * @return mixed
     */
    public function page(int $pageNumber = 0)
    {
        $search = trim($this->bag('meeting')->get('fee.member.search', ''));
        $filter = $this->bag('meeting')->get('fee.member.filter', null);
        $memberCount = $this->feeService->getMemberCount($this->charge,
            $this->session, $search, $filter);
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $memberCount,
            'meeting', 'member.page');
        $members = $this->feeService->getMembers($this->charge, $this->session,
            $search, $filter, $pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $memberCount);
        $settlement = $this->settlementService->getSettlement($this->charge, $this->session);

        $html = $this->render('pages.meeting.charge.libre.member.page', [
            'search' => $search,
            'session' => $this->session,
            'charge' => $this->charge,
            'members' => $members,
            'settlement' => $settlement,
            'pagination' => $pagination,
        ]);
        $this->response->html('meeting-fee-libre-members', $html);

        $memberId = jq()->parent()->attr('data-member-id')->toInt();
        $paid = pm()->checked('check-fee-libre-paid');
        $amount = jq('input', jq()->parent()->parent())->val()->toInt();
        $this->jq('.btn-add-bill')->click($this->rq()->addBill($memberId, $paid));
        $this->jq('.btn-del-bill')->click($this->rq()->delBill($memberId));
        $this->jq('.btn-save-bill')->click($this->rq()->addBill($memberId, $paid, $amount));
        $this->jq('.btn-edit-bill')->click($this->rq()->editBill($memberId));
        $this->jq('#btn-fee-libre-search')
            ->click($this->rq()->search(jq('#txt-fee-member-search')->val()));

        return $this->response;
    }

    public function toggleFilter()
    {
        $filter = $this->bag('meeting')->get('fee.member.filter', null);
        // Switch between null, true and false
        $filter = $filter === null ? true : ($filter === true ? false : null);
        $this->bag('meeting')->set('fee.member.filter', $filter);

        return $this->page();
    }

    public function search(string $search)
    {
        $this->bag('meeting')->set('fee.member.search', trim($search));

        return $this->page();
    }

    private function convertAmount(string $amount): float
    {
        $amount = str_replace(',', '.', trim($amount));
        if($amount !== '' && filter_var($amount, FILTER_VALIDATE_FLOAT) === false)
        {
            throw new MessageException(trans('meeting.errors.amount.invalid', [
                'amount' => $amount,
            ]));
        }
        return $amount === '' ? 0 : (float)$amount;
    }

    /**
     * @param int $memberId
     * @param string $amount
     *
     * @return mixed
     */
    public function addBill(int $memberId, bool $paid, string $amount = '')
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $this->feeService->createBill($this->charge, $this->session, $memberId,
            $paid, $this->convertAmount($amount));

        return $this->page();
    }

    /**
     * @param int $memberId
     *
     * @return mixed
     */
    public function delBill(int $memberId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $this->feeService->deleteBill($this->charge, $this->session, $memberId);

        return $this->page();
    }

    /**
     * @di $localeService
     * @param int $memberId
     *
     * @return mixed
     */
    public function editBill(int $memberId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }
        $bill = $this->feeService->getBill($this->charge, $this->session, $memberId);
        if($bill === null)
        {
            return $this->response;
        }

        $html = $this->render('pages.meeting.charge.libre.member.edit', [
            'id' => $memberId,
            'amount' => $this->localeService->getMoneyValue($bill->bill->amount),
        ]);
        $fieldId = 'member-' . $memberId;
        $this->response->html($fieldId, $html);

        $memberId = jq()->parent()->attr('data-member-id')->toInt();
        $amount = jq('input', jq()->parent()->parent())->val();
        $this->jq('.btn-save-bill', "#$fieldId")->click($this->rq()->saveBill($memberId, $amount));

        return $this->response;
    }

    /**
     * @param int $memberId
     * @param string $amount
     *
     * @return mixed
     */
    public function saveBill(int $memberId, string $amount)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $amount = $this->convertAmount($amount);
        if(!$amount)
        {
            $this->feeService->deleteBill($this->charge, $this->session, $memberId);
            return $this->page();
        }

        $this->feeService->updateBill($this->charge, $this->session, $memberId, $amount);
        return $this->page();
    }
}
