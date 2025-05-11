<?php

namespace Ajax\App\Meeting\Summary;

use Ajax\Component;
use Ajax\Page\SectionContent;
use Siak\Tontine\Service\Meeting\SessionService;
use Stringable;

use function trans;

/**
 * @databag summary
 * @before checkHostAccess ["meeting", "sessions"]
 */
class Summary extends Component
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

    public function home(int $sessionId)
    {
        $this->bag('summary')->set('session.id', $sessionId);
        // Sending an Ajax request to the Saving class needs to set
        // the session id in the report databag.
        $this->bag('report')->set('session.id', $sessionId);

        $session = $this->sessionService->getSession($sessionId);
        if(!$session)
        {
            $this->alert()->title(trans('common.titles.error'))
                ->error(trans('tontine.session.errors.not_opened'));
            return;
        }

        $this->stash()->set('summary.session', $session);

        $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('summary.session');
        return $this->renderView('pages.meeting.summary.home', [
            'session' => $session,
            'prevSession' => $this->sessionService->getPrevSession($session),
            'nextSession' => $this->sessionService->getNextSession($session),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->pools();
        $this->charges();
        $this->savings();
        $this->refunds();
        $this->profits();
        $this->outflows();
    }

    private function pools()
    {
        $this->cl(Pool\Deposit\Deposit::class)->show();
        $this->cl(Pool\Remitment\Remitment::class)->show();

        $this->response->js('Tontine')
            ->setSmScreenHandler('summary-pools-sm-screens', 'summary-pools');
    }

    private function charges()
    {
        $this->cl(Charge\Fixed\Fee::class)->show();
        $this->cl(Charge\Libre\Fee::class)->show();

        $this->response->js('Tontine')
            ->setSmScreenHandler('summary-charges-sm-screens', 'summary-charges');
    }

    private function savings()
    {
        $this->cl(Credit\Loan\Loan::class)->show();
        $this->cl(Saving\Saving::class)->show();

        $this->response->js('Tontine')
            ->setSmScreenHandler('summary-savings-sm-screens', 'summary-savings');
    }

    private function refunds()
    {
        $this->cl(Credit\Refund\Refund::class)->show();
    }

    private function profits()
    {
        $this->cl(Saving\Profit::class)->show();
    }

    private function outflows()
    {
        $this->cl(Cash\Outflow::class)->show();
    }
}
