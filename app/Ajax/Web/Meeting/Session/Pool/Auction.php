<?php

namespace App\Ajax\Web\Meeting\Session\Pool;

use App\Ajax\OpenedSessionCallable;
use Siak\Tontine\Service\Meeting\Pool\AuctionService;
use Siak\Tontine\Validation\Meeting\DebtValidator;

use function Jaxon\jq;
use function trans;

/**
 * @databag auction
 */
class Auction extends OpenedSessionCallable
{
    /**
     * @var DebtValidator
     */
    protected DebtValidator $validator;

    /**
     * The constructor
     *
     * @param AuctionService $auctionService
     */
    public function __construct(private AuctionService $auctionService)
    {}

    public function home()
    {
        $html = $this->renderView('pages.meeting.auction.home')
            ->with('session', $this->session);
        $this->response->html('meeting-remitments', $html);
        $this->response->jq('#btn-auctions-refresh')->click($this->rq()->home());
        $this->response->jq('#btn-auctions-filter')->click($this->rq()->toggleFilter());
        $this->response->jq('#btn-remitments-back')->click($this->rq(Remitment::class)->home());

        return $this->page();
    }

    /**
     * @param int $pageNumber
     *
     * @return mixed
     */
    public function page(int $pageNumber = 0)
    {
        $filtered = $this->bag('auction')->get('filter', null);
        $auctionCount = $this->auctionService->getAuctionCount($this->session, $filtered);
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $auctionCount, 'auction');
        $auctions = $this->auctionService->getAuctions($this->session, $filtered, $pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $auctionCount);

        $html = $this->renderView('pages.meeting.auction.page', [
            'session' => $this->session,
            'auctions' => $auctions,
        ]);
        $this->response->html('meeting-auctions-page', $html);
        $this->response->js()->makeTableResponsive('meeting-auctions-page');

        $auctionId = jq()->parent()->attr('data-auction-id')->toInt();
        $this->response->jq('.btn-toggle-payment', '#meeting-auctions-page')
            ->click($this->rq()->togglePayment($auctionId));

        return $this->response;
    }

    public function toggleFilter()
    {
        $filtered = $this->bag('auction')->get('filter', null);
        // Switch between null, true and false
        $filtered = $filtered === null ? true : ($filtered === true ? false : null);
        $this->bag('auction')->set('filter', $filtered);

        return $this->page(1);
    }

    /**
     * @di $validator
     * @after showBalanceAmounts
     */
    public function togglePayment(string $auctionId)
    {
        $this->validator->validate($auctionId);
        $this->auctionService->toggleAuctionPayment($this->session, $auctionId);

        return $this->page();
    }
}
