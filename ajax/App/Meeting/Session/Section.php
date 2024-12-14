<?php

namespace Ajax\App\Meeting\Session;

use Ajax\App\SectionContent;
use Ajax\App\Meeting\MeetingComponent;
use Stringable;

class Section extends MeetingComponent
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

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
        if($sessionId > 0)
        {
            $this->bag('meeting')->set('session.id', $sessionId);
            return $sessionId;
        }
        return parent::getSessionId();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView("pages.meeting.session.home.{$this->section}", [
            'session' => $this->cache()->get('meeting.session'),
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

    public function pools(int $sessionId = 0)
    {
        $this->renderSection('pools');

        $this->cl(Pool\Deposit\Deposit::class)->render();
        $this->cl(Pool\Remitment\Remitment::class)->render();

        $this->response->js()->setSmScreenHandler('session-pools-sm-screens');

        return $this->response;
    }

    public function savings(int $sessionId = 0)
    {
        $this->renderSection('savings');

        $this->cl(Saving\Saving::class)->render();
        $this->cl(Saving\Closing::class)->render();

        $this->response->js()->setSmScreenHandler('session-savings-sm-screens');

        return $this->response;
    }

    public function credits(int $sessionId = 0)
    {
        $this->renderSection('credits');

        $this->cl(Credit\Loan::class)->render();
        $this->cl(Credit\Partial\Refund::class)->render();
        $this->cl(Credit\Refund::class)->render();

        $this->response->js()->setSmScreenHandler('session-credits-sm-screens');

        return $this->response;
    }

    public function cash(int $sessionId = 0)
    {
        $this->renderSection('cash');

        $this->cl(Cash\Disbursement::class)->render();

        return $this->response;
    }

    public function charges(int $sessionId = 0)
    {
        $this->renderSection('charges');

        $this->cl(Charge\Fixed\Fee::class)->render();
        $this->cl(Charge\Libre\Fee::class)->render();

        $this->response->js()->setSmScreenHandler('session-charges-sm-screens');

        return $this->response;
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

        return $this->response;
    }
}
