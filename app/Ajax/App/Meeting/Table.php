<?php

namespace App\Ajax\App\Meeting;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Fund as FundModel;
use Siak\Tontine\Service\SubscriptionService;
use Siak\Tontine\Service\MeetingService;

use function intval;
use function jq;

/**
 * @databag meeting
 * @before getFund
 */
class Table extends CallableClass
{
    /**
     * @di
     * @var MeetingService
     */
    public MeetingService $meetingService;

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
        $fundId = intval($this->target()->method() === 'select' ?
            $this->target()->args()[0] : $this->bag('meeting')->get('fund.id', 0));
        if($fundId !== 0)
        {
            $this->fund = $this->subscriptionService->getFund($fundId);
        }
        if(!$this->fund)
        {
            $this->fund = $this->subscriptionService->getFirstFund();
            // Save the current fund id
            $this->bag('meeting')->set('fund.id', $this->fund ? $this->fund->id : 0);
        }
    }

    public function select($fundId)
    {
        if(($this->fund))
        {
            $this->bag('meeting')->set('fund.id', $this->fund->id);
            return $this->show();
        }

        return $this->response;
    }

    public function home()
    {
        // Don't try to show the page if there is no fund selected.
        return ($this->fund) ? $this->show() : $this->response;
    }

    /**
     * @exclude
     */
    public function show()
    {
        $this->view()->shareValues($this->meetingService->getFigures($this->fund));
        $html = $this->view()->render('pages.meeting.table.show')
            ->with('fund', $this->fund)
            ->with('funds', $this->subscriptionService->getFunds());
        $this->response->html('content-home', $html);

        $this->jq('#btn-fund-select')->click($this->rq()->select(pm()->select('select-fund'), true));
        $this->jq('#btn-meeting-table-refresh')->click($this->rq()->home());
        $this->jq('#btn-meeting-table-print')->click($this->rq()->print());

        return $this->response;
    }

    public function print()
    {
        return $this->response;
    }
}
