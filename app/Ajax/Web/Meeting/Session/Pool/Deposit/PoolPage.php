<?php

namespace App\Ajax\Web\Meeting\Session\Pool\Deposit;

use App\Ajax\Cache;
use App\Ajax\MeetingPageComponent;
use App\Ajax\Web\Meeting\Session\Pool\PoolTrait;
use Siak\Tontine\Service\Meeting\Pool\DepositService;
use Siak\Tontine\Service\Meeting\Pool\PoolService;

/**
 * @before getPool
 */
class PoolPage extends MeetingPageComponent
{
    use PoolTrait;

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['meeting', 'deposit.page'];

    /**
     * The constructor
     *
     * @param PoolService $poolService
     * @param DepositService $depositService
     */
    public function __construct(protected PoolService $poolService,
        protected DepositService $depositService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $pool = Cache::get('meeting.pool');
        $session = Cache::get('meeting.session');

        return (string)$this->renderView('pages.meeting.deposit.pool.page', [
            'pool' => $pool,
            'session' => $session,
            'receivables' => $this->depositService->getReceivables($pool, $session, $this->page),
        ]);
    }

    protected function count(): int
    {
        $pool = Cache::get('meeting.pool');
        $session = Cache::get('meeting.session');

        return $this->depositService->getReceivableCount($pool, $session);
    }

    private function showTotal()
    {
        $session = Cache::get('meeting.session');
        $pool = Cache::get('meeting.pool');
        Cache::set('meeting.pool.deposit.count',
            $this->depositService->countDeposits($pool, $session));

        $this->cl(Total::class)->render();
        $this->cl(Action::class)->render();
    }

    public function page(int $pageNumber = 0)
    {
        // Render the page content.
        $this->renderPage($pageNumber)
            // Render the paginator.
            ->render($this->rq()->page());

        $this->response->js()->makeTableResponsive('meeting-pool-deposits');

        $this->showTotal();

        return $this->response;
    }
}
