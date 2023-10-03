<?php

namespace App\Ajax\Web\Meeting\Charge;

use App\Ajax\CallableClass;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\Charge\FineService;
use Siak\Tontine\Model\Session as SessionModel;

use function Jaxon\jq;

/**
 * @databag meeting
 * @before getSession
 */
class Fine extends CallableClass
{
    /**
     * @di
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @var FineService
     */
    protected FineService $fineService;

    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session;

    /**
     * The constructor
     *
     * @param FineService $fineService
     */
    public function __construct(FineService $fineService)
    {
        $this->fineService = $fineService;
    }

    /**
     * @return void
     */
    protected function getSession()
    {
        $sessionId = $this->bag('meeting')->get('session.id');
        $this->session = $this->fineService->getSession($sessionId);
    }

    /**
     * @exclude
     */
    public function show(SessionModel $session)
    {
        $this->session = $session;

        return $this->home();
    }

    public function home()
    {
        $html = $this->view()->render('tontine.pages.meeting.charge.variable.home')
            ->with('session', $this->session);
        $this->response->html('meeting-fines', $html);
        $this->jq('#btn-fines-refresh')->click($this->rq()->home());

        return $this->page(1);
    }

    public function page(int $pageNumber)
    {
        $fineCount = $this->fineService->getFineCount();
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $fineCount, 'meeting', 'fine.page');
        $fines = $this->fineService->getFines($pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $fineCount);
        // Bill and settlement counts and amounts
        $bills = $this->fineService->getBills($this->session);
        $settlements = $this->fineService->getSettlements($this->session);
        foreach($fines as $fine)
        {
            $fine->currentBillCount = ($bills['total']['current'][$fine->id] ?? 0);
            $fine->previousBillCount = ($bills['total']['previous'][$fine->id] ?? 0);
        }

        $html = $this->view()->render('tontine.pages.meeting.charge.variable.page')
            ->with('session', $this->session)
            ->with('fines', $fines)
            ->with('bills', $bills)
            ->with('settlements', $settlements)
            ->with('pagination', $pagination);
        $this->response->html('meeting-fines-page', $html);

        $fineId = jq()->parent()->attr('data-fine-id')->toInt();
        $this->jq('.btn-fine-add')->click($this->cl(Member\Fine::class)->rq()->home($fineId));
        $this->jq('.btn-fine-settlements')->click($this->cl(Settlement\Fine::class)->rq()->home($fineId));

        return $this->response;
    }
}
