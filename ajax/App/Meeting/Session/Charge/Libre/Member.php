<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\App\Meeting\Session\Charge\ChargeComponent;
use Ajax\App\Meeting\Session\Charge\LibreFee;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Service\LocaleService;
use Stringable;

use function filter_var;
use function str_replace;
use function trans;
use function trim;

class Member extends ChargeComponent
{
    /**
     * @var string
     */
    protected $overrides = LibreFee::class;

    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $charge = $this->cache->get('meeting.session.charge');

        return $this->renderView('pages.meeting.charge.libre.member.home', [
            'charge' => $charge,
            'paid' => $charge->is_fee,
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
     * @param int $chargeId
     *
     * @return mixed
     */
    public function charge(int $chargeId)
    {
        $this->bag('meeting')->set('fee.member.filter', null);
        $this->bag('meeting')->set('fee.member.search', '');
        $this->bag('meeting')->set('fee.member.page', 1);

        return $this->render();
    }

    private function showTotal()
    {
        $session = $this->cache->get('meeting.session');
        $charge = $this->cache->get('meeting.session.charge');
        $settlement = $this->settlementService->getSettlementCount($charge, $session);

        $this->cache->set('meeting.session.settlement.count', $settlement->total ?? 0);
        $this->cache->set('meeting.session.settlement.amount', $settlement->amount ?? 0);

        $this->cl(MemberTotal::class)->render();
    }

    public function toggleFilter()
    {
        $filter = $this->bag('meeting')->get('fee.member.filter', null);
        // Switch between null, true and false
        $filter = $filter === null ? true : ($filter === true ? false : null);
        $this->bag('meeting')->set('fee.member.filter', $filter);

        return $this->cl(MemberPage::class)->page();
    }

    public function search(string $search)
    {
        $this->bag('meeting')->set('fee.member.search', trim($search));

        return $this->cl(MemberPage::class)->page();
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
        $session = $this->cache->get('meeting.session');
        $charge = $this->cache->get('meeting.session.charge');
        $this->billService
            ->createBill($charge, $session, $memberId, $paid, $this->convertAmount($amount));

        $this->showTotal();

        return $this->cl(MemberPage::class)->page();
    }

    /**
     * @before checkChargeEdit
     * @param int $memberId
     *
     * @return mixed
     */
    public function delBill(int $memberId)
    {
        $session = $this->cache->get('meeting.session');
        $charge = $this->cache->get('meeting.session.charge');
        $this->billService->deleteBill($charge, $session, $memberId);

        $this->showTotal();

        return $this->cl(MemberPage::class)->page();
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
        $session = $this->cache->get('meeting.session');
        $charge = $this->cache->get('meeting.session.charge');
        $bill = $this->billService->getMemberBill($charge, $session, $memberId);
        if($bill === null)
        {
            return $this->response;
        }

        $html = $this->renderView('pages.meeting.charge.libre.member.edit', [
            'memberId' => $memberId,
            'amount' => $this->localeService->getMoneyValue($bill->bill->amount),
        ]);
        $this->response->html("member-$memberId", $html);

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
        $session = $this->cache->get('meeting.session');
        $charge = $this->cache->get('meeting.session.charge');
        $amount = $this->convertAmount($amount);
        if(!$amount)
        {
            $this->billService->deleteBill($charge, $session, $memberId);

            $this->showTotal();

            return $this->cl(MemberPage::class)->page();
        }

        $this->billService->updateBill($charge, $session, $memberId, $amount);

        $this->showTotal();

        return $this->cl(MemberPage::class)->page();
    }
}
