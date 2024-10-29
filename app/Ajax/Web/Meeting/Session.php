<?php

namespace App\Ajax\Web\Meeting;

use App\Ajax\Component;
use App\Ajax\Web\SectionContent;
use App\Ajax\Web\SectionTitle;
use Jaxon\Response\ComponentResponse;
use Siak\Tontine\Service\Meeting\SessionService;

use function trans;

/**
 * @databag session
 * @before checkGuestAccess ["meeting", "sessions"]
 */
class Session extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @param SessionService $sessionService
     */
    public function __construct(protected SessionService $sessionService)
    {}

    /**
     * @before checkRoundSessions
     * @after hideMenuOnMobile
     */
    public function home(): ComponentResponse
    {
        return $this->render();
    }

    /**
     * @inheritDoc
     */
    protected function before()
    {
        $this->cl(SectionTitle::class)->show(trans('tontine.menus.meeting'));
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return (string)$this->renderView('pages.meeting.session.list.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(SessionPage::class)->page();
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
