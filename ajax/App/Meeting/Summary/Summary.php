<?php

namespace Ajax\App\Meeting\Summary;

use Ajax\Base\Round\Component;
use Ajax\Page\SectionContent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Siak\Tontine\Service\Meeting\Session\SessionService;

use function trans;

#[Before('checkHostAccess', ["meeting", "sessions"])]
#[Databag('summary')]
class Summary extends Component
{
    /**
     * @var string
     */
    protected string $overrides = SectionContent::class;

    /**
     * @param SessionService $sessionService
     */
    public function __construct(protected SessionService $sessionService)
    {}

    public function home(int $sessionId)
    {
        $session = $this->sessionService->getSession($this->round(), $sessionId);
        if(!$session)
        {
            $this->alert()->title(trans('common.titles.error'))
                ->error(trans('tontine.session.errors.not_opened'));
            return;
        }

        $this->bag('summary')->set('session.id', $sessionId);
        $this->stash()->set('summary.session', $session);

        $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $session = $this->stash()->get('summary.session');
        return $this->renderTpl('pages.meeting.summary.home', [
            'session' => $session,
            'prevSession' => $this->sessionService->getPrevSession($this->round(), $session),
            'nextSession' => $this->sessionService->getNextSession($this->round(), $session),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $this->pools();
        $this->charges();
        $this->savings();
        $this->refunds();
        $this->profits();
        $this->outflows();
    }

    private function pools(): void
    {
        $this->cl(Pool\Deposit\Deposit::class)->show();
        $this->cl(Pool\Remitment\Remitment::class)->show();

        $this->response()->jo('tontine')
            ->setSmScreenHandler('summary-pools-sm-screens', 'summary-pools');
    }

    private function charges(): void
    {
        $this->cl(Charge\Fixed\Fee::class)->show();
        $this->cl(Charge\Libre\Fee::class)->show();

        $this->response()->jo('tontine')
            ->setSmScreenHandler('summary-charges-sm-screens', 'summary-charges');
    }

    private function savings(): void
    {
        $this->cl(Credit\Loan\Loan::class)->show();
        $this->cl(Saving\Saving::class)->show();

        $this->response()->jo('tontine')
            ->setSmScreenHandler('summary-savings-sm-screens', 'summary-savings');
    }

    private function refunds(): void
    {
        $this->cl(Credit\Refund\Refund::class)->show();
    }

    private function profits(): void
    {
        $this->cl(Saving\Profit::class)->show();
    }

    private function outflows(): void
    {
        $this->cl(Cash\Outflow::class)->show();
    }
}
