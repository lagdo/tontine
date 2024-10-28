<?php

namespace App\Ajax\Web\Meeting\Session;

use App\Ajax\Cache;
use App\Ajax\Web\Meeting\MeetingComponent;

/**
 * @databag meeting
 */
class Menu extends MeetingComponent
{
    /**
     * @var string
     */
    private string $section;

    /**
     * @return int
     */
    protected function getSessionId(): int
    {
        $sessionId = (int)($this->target()->args()[0] ?? 0);
        return $sessionId > 0 ? $sessionId : parent::getSessionId();
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return (string)$this->renderView("pages.meeting.session.home.{$this->section}", [
            'session' => Cache::get('meeting.session'),
        ]);
    }

    /**
     * @param string $section
     *
     * @return void
     */
    private function renderSection(string $section): void
    {
        $this->section = $section;
        $this->view()->share('section', $this->section);
        $this->render();
    }

    public function pools()
    {
        $this->renderSection('pools');

        $this->cl(Pool\Deposit::class)->render();
        $this->cl(Pool\Remitment::class)->render();

        $this->response->js()->setSmScreenHandler('session-pools-sm-screens');

        return $this->response;
    }

    public function savings()
    {
        $this->renderSection('savings');

        $this->cl(Saving\Saving::class)->render();
        $this->cl(Saving\Closing::class)->render();

        $this->response->js()->setSmScreenHandler('session-savings-sm-screens');

        return $this->response;
    }

    public function credits()
    {
        $this->renderSection('credits');

        $this->cl(Credit\Loan::class)->render();
        $this->cl(Credit\PartialRefund::class)->render();
        $this->cl(Credit\Refund::class)->render();

        $this->response->js()->setSmScreenHandler('session-credits-sm-screens');

        return $this->response;
    }

    public function cash()
    {
        $this->renderSection('cash');

        $this->cl(Cash\Disbursement::class)->render();

        return $this->response;
    }

    public function charges()
    {
        $this->renderSection('charges');

        $this->cl(Charge\FixedFee::class)->render();
        $this->cl(Charge\LibreFee::class)->render();

        $this->response->js()->setSmScreenHandler('session-charges-sm-screens');

        return $this->response;
    }

    public function reports()
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

        return $this->response;
    }
}
