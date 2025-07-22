<?php

namespace Ajax\App\Meeting\Session;

/**
 * @before checkHostAccess ["meeting", "sessions"]
 */
class SectionFunc extends FuncComponent
{
    /**
     * @return int
     */
    protected function getSessionId(): int
    {
        $sessionId = (int)($this->target()->args()[0] ?? 0);
        if($sessionId > 0)
        {
            $this->bag('meeting')->set('session.id', $sessionId);
            return $sessionId;
        }
        return parent::getSessionId();
    }

    /**
     * @param string $section
     *
     * @return void
     */
    private function renderSection(string $section): void
    {
        $this->view()->share('section', $section);
        $this->stash()->set('section', $section);
        $this->cl(Section::class)->render();
    }

    public function pools(int $sessionId = 0): void
    {
        $this->renderSection('pools');

        $this->cl(Pool\Deposit\Deposit::class)->show();
        $this->cl(Pool\Remitment\Remitment::class)->show();

        $this->response->jo('Tontine')
            ->setSmScreenHandler('session-pools-sm-screens', 'content-home');
    }

    public function charges(int $sessionId = 0): void
    {
        $this->renderSection('charges');

        $this->cl(Charge\Fixed\Fee::class)->show();
        $this->cl(Charge\Libre\Fee::class)->show();

        $this->response->jo('Tontine')
            ->setSmScreenHandler('session-charges-sm-screens', 'content-home');
    }

    public function savings(int $sessionId = 0): void
    {
        $this->renderSection('savings');

        $this->cl(Saving\Saving::class)->show();
        $this->cl(Credit\Loan\Loan::class)->show();

        $this->response->jo('Tontine')
            ->setSmScreenHandler('session-savings-sm-screens', 'content-home');
    }

    public function refunds(int $sessionId = 0): void
    {
        $this->renderSection('refunds');

        $this->cl(Credit\Refund\Refund::class)->show();
    }

    public function profits(int $sessionId = 0): void
    {
        $this->renderSection('profits');

        $this->cl(Saving\Profit::class)->show();
    }

    public function outflows(int $sessionId = 0): void
    {
        $this->renderSection('outflows');

        $this->cl(Cash\Outflow::class)->show();
    }

    public function reports(int $sessionId = 0): void
    {
        $this->renderSection('reports');

        // Summernote options
        $options = [
            'height' => 300,
            'toolbar' => [
                // [groupName, [list of button]],
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                // ['height', ['height']],
            ],
        ];
        $this->response->jq('#session-agenda')->summernote($options);
        $this->response->jq('#session-report')->summernote($options);
        $this->response->jo('Tontine')
            ->setSmScreenHandler('session-reports-sm-screens', 'content-home');
    }
}
