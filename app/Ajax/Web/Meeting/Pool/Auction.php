<?php

namespace App\Ajax\Web\Meeting\Pool;

use App\Ajax\CallableClass;
use App\Ajax\Web\Meeting\Cash\Disbursement;
use App\Ajax\Web\Meeting\Credit\Loan;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Pool\AuctionService;
use Siak\Tontine\Validation\Meeting\DebtValidator;

use function Jaxon\jq;
use function trans;

/**
 * @databag meeting
 * @databag auction
 * @before getSession
 */
class Auction extends CallableClass
{
    /**
     * @var DebtValidator
     */
    protected DebtValidator $validator;

    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session = null;

    /**
     * The constructor
     *
     * @param AuctionService $auctionService
     */
    public function __construct(private AuctionService $auctionService)
    {}

    /**
     * @return void
     */
    protected function getSession()
    {
        $sessionId = $this->bag('meeting')->get('session.id');
        $this->session = $this->auctionService->getSession($sessionId);
    }

    public function home()
    {
        $html = $this->view()->render('tontine.pages.meeting.auction.home')
            ->with('session', $this->session);
        $this->response->html('meeting-remitments', $html);
        $this->jq('#btn-auctions-refresh')->click($this->rq()->home());
        $this->jq('#btn-auctions-filter')->click($this->rq()->toggleFilter());
        $this->jq('#btn-remitments-back')->click($this->cl(Remitment::class)->rq()->home());

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

        $html = $this->view()->render('tontine.pages.meeting.auction.page', [
            'session' => $this->session,
            'auctions' => $auctions,
            'pagination' => $pagination,
        ]);
        $this->response->html('meeting-auctions-page', $html);

        $auctionId = jq()->parent()->attr('data-auction-id')->toInt();
        $this->jq('.btn-toggle-payment', '#meeting-auctions-page')
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
     */
    public function togglePayment(string $auctionId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $this->validator->validate($auctionId);
        $this->auctionService->toggleAuctionPayment($this->session, $auctionId);

        // Refresh the amounts available
        $this->cl(Loan::class)->refreshAmount($this->session);
        $this->cl(Disbursement::class)->refreshAmount($this->session);

        return $this->page();
    }
}
