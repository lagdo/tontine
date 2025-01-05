<?php

namespace Ajax\App\Meeting\Session;

use Ajax\Component;
use Ajax\App\Page\SectionContent;
use Ajax\App\Page\SectionTitle;
use Jaxon\Response\AjaxResponse;
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
    public function home(): AjaxResponse
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
        return $this->cl(SessionPage::class)->page();
    }

    public function open(int $sessionId)
    {
        if(!($session = $this->sessionService->getSession($sessionId)) || $session->opened)
        {
            $this->alert()->title(trans('common.titles.error'))
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
            $this->alert()->title(trans('common.titles.error'))
                ->error(trans('tontine.session.errors.not_opened'));
            return $this->cl(SessionPage::class)->page();
        }

        $this->sessionService->closeSession($session);

        return $this->cl(SessionPage::class)->page();
    }

    public function saveAgenda(string $text)
    {
        $sessionId = $this->bag('meeting')->get('session.id', 0);
        if(!($session = $this->sessionService->getSession($sessionId)))
        {
            $this->alert()->title(trans('common.titles.error'))
                ->error(trans('tontine.session.errors.not_found'));
            return $this->response;
        }

        $this->sessionService->saveAgenda($session, $text);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('meeting.messages.agenda.updated'));

        return $this->response;
    }

    public function saveReport(string $text)
    {
        $sessionId = $this->bag('meeting')->get('session.id', 0);
        if(!($session = $this->sessionService->getSession($sessionId)))
        {
            $this->alert()->title(trans('common.titles.error'))
                ->error(trans('tontine.session.errors.not_found'));
            return $this->response;
        }

        $this->sessionService->saveReport($session, $text);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('meeting.messages.report.updated'));

        return $this->response;
    }
}
