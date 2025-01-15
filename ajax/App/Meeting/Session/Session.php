<?php

namespace Ajax\App\Meeting\Session;

use Ajax\Component;
use Ajax\App\Page\SectionContent;
use Ajax\App\Page\SectionTitle;
use Siak\Tontine\Service\Meeting\SessionService;
use Stringable;

use function trans;

/**
 * @databag meeting
 * @before checkHostAccess ["meeting", "sessions"]
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
    public function home()
    {
        $this->render();
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
    public function html(): Stringable
    {
        return $this->renderView('pages.meeting.session.list.home');
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

        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.session.messages.resynced'));
        $this->cl(SessionPage::class)->page();
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
