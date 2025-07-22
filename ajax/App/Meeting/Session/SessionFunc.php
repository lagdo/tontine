<?php

namespace Ajax\App\Meeting\Session;

use Ajax\FuncComponent;
use Siak\Tontine\Service\Meeting\Session\SessionService;

use function trans;

/**
 * @databag meeting
 * @before checkHostAccess ["meeting", "sessions"]
 */
class SessionFunc extends FuncComponent
{
    /**
     * @param SessionService $sessionService
     */
    public function __construct(protected SessionService $sessionService)
    {}

    public function open(int $sessionId): void
    {
        $round = $this->stash()->get('tenant.round');
        $session = $this->sessionService->getSession($round, $sessionId);
        if(!$session || $session->opened)
        {
            $this->alert()->title(trans('common.titles.error'))
                ->error(trans('tontine.session.errors.opened'));
            $this->cl(SessionPage::class)->page();
            return;
        }

        $this->sessionService->openSession($session);

        $this->cl(SessionPage::class)->page();
    }

    public function close(int $sessionId): void
    {
        $round = $this->stash()->get('tenant.round');
        $session = $this->sessionService->getSession($round, $sessionId);
        if(!$session || !$session->opened)
        {
            $this->alert()->title(trans('common.titles.error'))
                ->error(trans('tontine.session.errors.not_opened'));
            $this->cl(SessionPage::class)->page();
            return;
        }

        $this->sessionService->closeSession($session);

        $this->cl(SessionPage::class)->page();
    }

    public function saveAgenda(string $text): void
    {
        $round = $this->stash()->get('tenant.round');
        $sessionId = $this->bag('meeting')->get('session.id', 0);
        $session = $this->sessionService->getSession($round, $sessionId);
        if(!$session)
        {
            $this->alert()->title(trans('common.titles.error'))
                ->error(trans('tontine.session.errors.not_found'));
            return;
        }

        $this->sessionService->saveAgenda($session, $text);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('meeting.messages.agenda.updated'));
    }

    public function saveReport(string $text): void
    {
        $round = $this->stash()->get('tenant.round');
        $sessionId = $this->bag('meeting')->get('session.id', 0);
        $session = $this->sessionService->getSession($round, $sessionId);
        if(!$session)
        {
            $this->alert()->title(trans('common.titles.error'))
                ->error(trans('tontine.session.errors.not_found'));
            return;
        }

        $this->sessionService->saveReport($session, $text);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('meeting.messages.report.updated'));
    }
}
