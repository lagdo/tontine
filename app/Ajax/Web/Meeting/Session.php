<?php

namespace App\Ajax\Web\Meeting;

use App\Ajax\CallableClass;
use Siak\Tontine\Service\Meeting\SessionService;

use function trans;

/**
 * @databag session
 * @before checkGuestAccess ["meeting", "sessions"]
 */
class Session extends CallableClass
{
    /**
     * @param SessionService $sessionService
     */
    public function __construct(protected SessionService $sessionService)
    {}

    /**
     * @before checkRoundSessions
     * @after hideMenuOnMobile
     */
    public function home()
    {
        $this->response->html('section-title', trans('tontine.menus.meeting'));
        $html = $this->renderView('pages.meeting.session.list.home');
        $this->response->html('content-home', $html);

        return $this->cl(SessionPage::class)->page();
    }

    public function resync()
    {
        $this->sessionService->resyncSessions();

        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.session.messages.resynced'));
        return $this->cl(SessionPage::class)->page();
    }

    public function open(int $sessionId)
    {
        if(!($session = $this->sessionService->getSession($sessionId)) || $session->opened)
        {
            $this->notify->title(trans('common.titles.error'))
                ->error(trans('tontine.session.errors.opened'));
            return $this->cl(SessionPage::class)->page();
        }

        $this->sessionService->openSession($session);

        return $this->cl(SessionPage::class)->page();
    }

    public function close(int $sessionId)
    {
        if(!($session = $this->sessionService->getSession($sessionId)) || !$session->opened)
        {
            $this->notify->title(trans('common.titles.error'))
                ->error(trans('tontine.session.errors.not_opened'));
            return $this->cl(SessionPage::class)->page();
        }

        $this->sessionService->closeSession($session);

        return $this->cl(SessionPage::class)->page();
    }
}
