<?php

namespace Ajax\App\Meeting\Session;

use Ajax\App\Meeting\FuncComponent;

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

    public function pools(int $sessionId = 0)
    {
        $this->renderSection('pools');

        $this->cl(Pool\Deposit\Deposit::class)->render();
        $this->cl(Pool\Remitment\Remitment::class)->render();

        $this->response->js('Tontine')
            ->setSmScreenHandler('session-pools-sm-screens', 'content-home');
    }

    public function savings(int $sessionId = 0)
    {
        $this->renderSection('savings');

        $this->cl(Saving\Saving::class)->render();
        $this->cl(Saving\Closing::class)->render();

        $this->response->js('Tontine')
            ->setSmScreenHandler('session-savings-sm-screens', 'content-home');
    }

    public function loans(int $sessionId = 0)
    {
        $this->renderSection('loans');

        $this->cl(Credit\Loan::class)->render();
    }

    public function refunds(int $sessionId = 0)
    {
        $this->renderSection('refunds');

        $this->cl(Credit\Total\Refund::class)->render();
        $this->cl(Credit\Partial\Refund::class)->render();

        $this->response->js('Tontine')
            ->setSmScreenHandler('session-refunds-sm-screens', 'content-home');
    }

    public function cash(int $sessionId = 0)
    {
        $this->renderSection('cash');

        $this->cl(Cash\Disbursement::class)->render();
    }

    public function charges(int $sessionId = 0)
    {
        $this->renderSection('charges');

        $this->cl(Charge\Fixed\Fee::class)->render();
        $this->cl(Charge\Libre\Fee::class)->render();

        $this->response->js('Tontine')
            ->setSmScreenHandler('session-charges-sm-screens', 'content-home');
    }

    public function reports(int $sessionId = 0)
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

        $this->response->js('Tontine')
            ->setSmScreenHandler('session-reports-sm-screens', 'content-home');
    }
}
