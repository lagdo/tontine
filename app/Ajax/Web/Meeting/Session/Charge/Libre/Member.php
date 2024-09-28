<?php

namespace App\Ajax\Web\Meeting\Session\Charge\Libre;

use App\Ajax\ChargeCallable;
use App\Ajax\Web\Meeting\Session\Charge\LibreFee as Charge;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Charge as ChargeModel;
use Siak\Tontine\Service\LocaleService;

use function filter_var;
use function Jaxon\jq;
use function Jaxon\pm;
use function str_replace;
use function trans;
use function trim;

class Member extends ChargeCallable
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @var ChargeModel|null
     */
    protected ?ChargeModel $charge;

    /**
     * @param int $chargeId
     *
     * @return mixed
     */
    public function home(int $chargeId)
    {
        $this->bag('meeting')->set('charge.id', $chargeId);
        $this->bag('meeting')->set('fee.member.filter', null);
        $this->bag('meeting')->set('fee.member.search', '');

        $html = $this->renderView('pages.meeting.charge.libre.member.home', [
            'charge' => $this->charge,
            'paid' => $this->charge->is_fee,
        ]);
        $this->response->html('meeting-fees-libre', $html);

        $this->response->jq('#btn-fee-libre-back')->click($this->rq(Charge::class)->home());
        $this->response->jq('#btn-fee-libre-filter')->click($this->rq()->toggleFilter());
        $this->response->jq('#btn-fee-libre-search')
            ->click($this->rq()->search(jq('#txt-fee-member-search')->val()));

        return $this->page(1);
    }

    private function showTotal()
    {
        $settlement = $this->settlementService->getSettlementCount($this->charge, $this->session);
        $settlementCount = $settlement->total ?? 0;
        $settlementAmount = $settlement->amount ?? 0;

        $html = $this->renderView('pages.meeting.charge.libre.member.total', [
            'settlementCount' => $settlementCount,
            'settlementAmount' => $settlementAmount,
        ]);
        $this->response->html('member-libre-settlements-total', $html);
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
        $memberCount = $this->billService->getMemberCount($this->charge,
            $this->session, $search, $filter);
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $memberCount,
            'meeting', 'member.page');
        $members = $this->billService->getMembers($this->charge, $this->session,
            $search, $filter, $pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $memberCount);

        $this->showTotal();

        $html = $this->renderView('pages.meeting.charge.libre.member.page', [
            'session' => $this->session,
            'charge' => $this->charge,
            'members' => $members,
        ]);
        $this->response->html('meeting-fee-libre-members', $html);
        $this->response->js()->makeTableResponsive('meeting-fee-libre-members');

        $memberId = jq()->parent()->attr('data-member-id')->toInt();
        $paid = pm()->checked('check-fee-libre-paid');
        $amount = jq('input', jq()->parent()->parent())->val()->toInt();
        $this->response->jq('.btn-add-bill')->click($this->rq()->addBill($memberId, $paid));
        $this->response->jq('.btn-del-bill')->click($this->rq()->delBill($memberId));
        $this->response->jq('.btn-save-bill')->click($this->rq()->addBill($memberId, $paid, $amount));
        $this->response->jq('.btn-edit-bill')->click($this->rq()->editBill($memberId));

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
     * @before checkChargeEdit
     * @param int $memberId
     * @param string $amount
     *
     * @return mixed
     */
    public function addBill(int $memberId, bool $paid, string $amount = '')
    {
        $this->billService->createBill($this->charge, $this->session, $memberId,
            $paid, $this->convertAmount($amount));

        return $this->page();
    }

    /**
     * @before checkChargeEdit
     * @param int $memberId
     *
     * @return mixed
     */
    public function delBill(int $memberId)
    {
        $this->billService->deleteBill($this->charge, $this->session, $memberId);

        return $this->page();
    }

    /**
     * @di $localeService
     * @before checkChargeEdit
     * @param int $memberId
     *
     * @return mixed
     */
    public function editBill(int $memberId)
    {
        $bill = $this->billService->getMemberBill($this->charge, $this->session, $memberId);
        if($bill === null)
        {
            return $this->response;
        }

        $html = $this->renderView('pages.meeting.charge.libre.member.edit', [
            'id' => $memberId,
            'amount' => $this->localeService->getMoneyValue($bill->bill->amount),
        ]);
        $fieldId = 'member-' . $memberId;
        $this->response->html($fieldId, $html);

        $memberId = jq()->parent()->attr('data-member-id')->toInt();
        $amount = jq('input', jq()->parent()->parent())->val();
        $this->response->jq('.btn-save-bill', "#$fieldId")->click($this->rq()->saveBill($memberId, $amount));

        return $this->response;
    }

    /**
     * @before checkChargeEdit
     * @param int $memberId
     * @param string $amount
     *
     * @return mixed
     */
    public function saveBill(int $memberId, string $amount)
    {
        $amount = $this->convertAmount($amount);
        if(!$amount)
        {
            $this->billService->deleteBill($this->charge, $this->session, $memberId);
            return $this->page();
        }

        $this->billService->updateBill($this->charge, $this->session, $memberId, $amount);
        return $this->page();
    }
}
