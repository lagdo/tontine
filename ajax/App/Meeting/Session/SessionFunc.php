<?php

namespace Ajax\App\Meeting\Session;

use Ajax\FuncComponent;
use Siak\Tontine\Service\Meeting\SessionService;

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

    public function resync()
    {
        $this->sessionService->resyncSessions();

        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.session.messages.resynced'));
    }

    public function open(int $sessionId)
    {
        if(!($session = $this->sessionService->getSession($sessionId)) || $session->opened)
        {
            $this->alert()->title(trans('common.titles.error'))
                ->error(trans('tontine.session.errors.opened'));
            $this->cl(SessionPage::class)->page();
            return;
        }

        $this->sessionService->openSession($session);

        $this->cl(SessionPage::class)->page();
    }

    public function close(int $sessionId)
    {
        if(!($session = $this->sessionService->getSession($sessionId)) || !$session->opened)
        {
            $this->alert()->title(trans('common.titles.error'))
                ->error(trans('tontine.session.errors.not_opened'));
            $this->cl(SessionPage::class)->page();
            return;
        }

        $this->sessionService->closeSession($session);

        $this->cl(SessionPage::class)->page();
    }

    public function saveAgenda(string $text)
    {
        $sessionId = $this->bag('meeting')->get('session.id', 0);
        if(!($session = $this->sessionService->getSession($sessionId)))
        {
            $this->alert()->title(trans('common.titles.error'))
                ->error(trans('tontine.session.errors.not_found'));
            return;
        }

        $this->sessionService->saveAgenda($session, $text);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('meeting.messages.agenda.updated'));
    }

    public function saveReport(string $text)
    {
        $sessionId = $this->bag('meeting')->get('session.id', 0);
        if(!($session = $this->sessionService->getSession($sessionId)))
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
