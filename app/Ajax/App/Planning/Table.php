<?php

namespace App\Ajax\App\Planning;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Fund as FundModel;
use Siak\Tontine\Service\SubscriptionService;
use Siak\Tontine\Service\TenantService;

use function intval;
use function jq;
use function pm;

/**
 * @databag table
 * @before getFund
 */
class Table extends CallableClass
{
    /**
     * @var TenantService
     */
    public TenantService $tenantService;

    /**
     * @di
     * @var SubscriptionService
     */
    public SubscriptionService $subscriptionService;

    /**
     * @var FundModel|null
     */
    protected ?FundModel $fund = null;

    /**
     * @return void
     */
    protected function getFund()
    {
        $fundId = $this->target()->method() === 'select' ?
            $this->target()->args()[0] : $this->bag('table')->get('fund.id', 0);
        if($fundId !== 0)
        {
            $this->fund = $this->subscriptionService->getFund(intval($fundId));
        }
        if(!$this->fund)
        {
            $this->fund = $this->subscriptionService->getFirstFund();
            // Save the current fund id
            $this->bag('table')->set('fund.id', $this->fund ? $this->fund->id : 0);
        }
    }

    public function select($fundId, $showDeposits)
    {
        if(($this->fund))
        {
            $this->bag('table')->set('fund.id', $this->fund->id);
        }

        return $showDeposits ? $this->deposits() : $this->remittances();
    }

    public function home()
    {
        // Don't try to show the page if there is no fund selected.
        return ($this->fund) ? $this->deposits() : $this->response;
    }

    public function deposits()
    {
        $receivables = $this->subscriptionService->getReceivables($this->fund);
        $this->view()->shareValues($receivables);
        $html = $this->view()->render('pages.planning.table.deposits')
            ->with('fund', $this->fund)
            ->with('funds', $this->subscriptionService->getFunds());
        $this->response->html('content-home', $html);

        $this->jq('#btn-fund-select')->click($this->rq()->select(pm()->select('select-fund'), true));
        $this->jq('#btn-subscription-refresh')->click($this->rq()->deposits());
        $this->jq('#btn-subscription-remittances')->click($this->rq()->remittances());
        $this->jq('.fund-session-toggle')->click($this->rq()->toggleSession(jq()->attr('data-session-id')));

        return $this->response;
    }

    /**
     * @di $tenantService
     */
    public function toggleSession($sessionId)
    {
        $session = $this->tenantService->getSession(intval($sessionId));
        $this->subscriptionService->toggleSession($this->fund, $session);

        return $this->deposits();
    }

    public function remittances()
    {
        $payables = $this->subscriptionService->getPayables($this->fund);
        $this->view()->shareValues($payables);
        $html = $this->view()->render('pages.planning.table.remittances')
            ->with('fund', $this->fund)
            ->with('funds', $this->subscriptionService->getFunds());
        $this->response->html('content-home', $html);

        $this->jq('#btn-fund-select')->click($this->rq()->select(pm()->select('select-fund'), false));
        $this->jq('#btn-subscription-refresh')->click($this->rq()->remittances());
        $this->jq('#btn-subscription-deposits')->click($this->rq()->deposits());
        $this->jq('.select-beneficiary')->change($this->rq()
            ->saveBeneficiary(jq()->attr('data-session-id'), jq()->attr('data-subscription-id'), jq()->val()));

        return $this->response;
    }

    /**
     * @di $tenantService
     */
    public function saveBeneficiary($sessionId, $currSubscriptionId, $nextSubscriptionId)
    {
        $session = $this->tenantService->getSession(intval($sessionId));
        $this->subscriptionService->saveBeneficiary($this->fund, $session,
            intval($currSubscriptionId), intval($nextSubscriptionId));

        return $this->remittances();
    }
}
