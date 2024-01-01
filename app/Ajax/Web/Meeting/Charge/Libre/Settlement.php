<?php

namespace App\Ajax\Web\Meeting\Charge\Libre;

use App\Ajax\CallableSessionClass;
use App\Ajax\Web\Meeting\Charge\LibreFee as Charge;
use Siak\Tontine\Model\Charge as ChargeModel;
use Siak\Tontine\Service\Meeting\Charge\BillService;
use Siak\Tontine\Service\Meeting\Charge\SettlementService;
use Siak\Tontine\Service\Tontine\ChargeService;

use function Jaxon\jq;
use function trans;
use function trim;

/**
 * @before getCharge
 */
class Settlement extends CallableSessionClass
{
    /**
     * @var ChargeModel|null
     */
    protected ?ChargeModel $charge;

    /**
     * The constructor
     *
     * @param SettlementService $settlementService
     * @param ChargeService $chargeService
     * @param BillService $billService
     */
    public function __construct(protected SettlementService $settlementService,
        protected ChargeService $chargeService, protected BillService $billService)
    {}

    protected function getCharge()
    {
        $chargeId = $this->target()->method() === 'home' ?
            $this->target()->args()[0] : $this->bag('meeting')->get('fee.libre.id');
        $this->charge = $this->chargeService->getCharge($chargeId);
    }

    /**
     * @param int $chargeId
     *
     * @return mixed
     */
    public function home(int $chargeId)
    {
        $this->bag('meeting')->set('fee.libre.id', $chargeId);
        $this->bag('meeting')->set('settlement.libre.search', '');
        $this->bag('meeting')->set('settlement.libre.filter', null);

        $html = $this->render('pages.meeting.settlement.home', [
            'type' => 'libre',
            'charge' => $this->charge,
        ]);
        $this->response->html('meeting-fees-libre', $html);
        $this->jq('#btn-fee-libre-settlements-back')->click($this->cl(Charge::class)->rq()->home());
        $this->jq('#btn-fee-libre-settlements-filter')->click($this->rq()->toggleFilter());

        return $this->page(1);
    }

    /**
     * @param int $pageNumber
     *
     * @return mixed
     */
    public function page(int $pageNumber = 0)
    {
        $search = trim($this->bag('meeting')->get('settlement.libre.search', ''));
        $onlyUnpaid = $this->bag('meeting')->get('settlement.libre.filter', null);
        $billCount = $this->billService->getBillCount($this->charge,
            $this->session, $search, $onlyUnpaid);
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber,
            $billCount, 'meeting', 'settlement.page');
        $bills = $this->billService->getBills($this->charge, $this->session,
            $search, $onlyUnpaid, $pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $billCount);
        $settlement = $this->settlementService->getSettlement($this->charge, $this->session);

        $html = $this->render('pages.meeting.settlement.page', [
            'type' => 'libre',
            'session' => $this->session,
            'charge' => $this->charge,
            'billCount' => $billCount,
            'settlement' => $settlement,
            'bills' => $bills,
            'pagination' => $pagination,
        ]);
        $this->response->html('meeting-fee-libre-bills', $html);

        $billId = jq()->parent()->attr('data-bill-id')->toInt();
        $this->jq('.btn-add-settlement', '#meeting-fee-libre-bills')
            ->click($this->rq()->addSettlement($billId));
        $this->jq('.btn-del-settlement', '#meeting-fee-libre-bills')
            ->click($this->rq()->delSettlement($billId));
        $this->jq('.btn-edit-notes', '#meeting-fee-libre-bills')
            ->click($this->rq()->editNotes($billId));

        return $this->response;
    }

    public function toggleFilter()
    {
        $onlyUnpaid = $this->bag('meeting')->get('settlement.libre.filter', null);
        // Switch between null, true and false
        $onlyUnpaid = $onlyUnpaid === null ? true : ($onlyUnpaid === true ? false : null);
        $this->bag('meeting')->set('settlement.libre.filter', $onlyUnpaid);

        return $this->page();
    }

    /**
     * @param int $billId
     *
     * @return mixed
     * @after showBalanceAmounts
     */
    public function addSettlement(int $billId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $this->settlementService->createSettlement($this->charge, $this->session, $billId);

        return $this->page();
    }

    /**
     * @param int $billId
     *
     * @return mixed
     * @after showBalanceAmounts
     */
    public function delSettlement(int $billId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $this->settlementService->deleteSettlement($this->charge, $this->session, $billId);

        return $this->page();
    }
}
