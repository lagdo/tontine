<?php

namespace Ajax\App\Meeting\Session\Charge;

use Ajax\App\Meeting\MeetingPageComponent;
use Siak\Tontine\Service\Meeting\Charge\LibreFeeService;

class LibreFeePage extends MeetingPageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['meeting', 'fee.libre.page'];

    /**
     * The constructor
     *
     * @param LibreFeeService $feeService
     */
    public function __construct(protected LibreFeeService $feeService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        return $this->feeService->getFeeCount();
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $session = $this->cache->get('meeting.session');

        return $this->renderView('pages.meeting.charge.libre.page', [
            'session' => $session,
            'charges' => $this->feeService->getFees($this->page),
            'bills' => $this->feeService->getBills($session),
            'settlements' => $this->feeService->getSettlements($session),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('meeting-fees-libre-page');
    }
}
