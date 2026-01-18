<?php

namespace Ajax\Base;

use Jaxon\App\View\ViewRenderer;
use Jaxon\Attributes\Attribute\Inject;
use Siak\Tontine\Exception\MeetingRoundException;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Exception\PlanningPoolException;
use Siak\Tontine\Exception\PlanningRoundException;
use Siak\Tontine\Exception\TontineMemberException;
use Siak\Tontine\Service\TenantService;

use function trans;

trait ComponentTrait
{
    /**
     * @var string
     */
    protected static string $tontineViewPrefix = 'tontine_app::';

    /**
     * @var TenantService
     */
    #[Inject]
    protected TenantService $tenantService;

    /**
     * Get the view renderer
     *
     * @return ViewRenderer
     */
    abstract protected function view(): ViewRenderer;

    /**
     * Render a view
     *
     * @param string $view
     * @param array $viewData
     *
     * @return string
     */
    protected function renderTpl(string $view, array $viewData = []): string
    {
        $html = $this->view()->render(self::$tontineViewPrefix . $view, $viewData);
        return $html = null ? '' : (string)$html;
    }

    /**
     * Check guest user access to a menu entry in a section
     *
     * @param string $section
     * @param string $entry
     * @param bool $return
     *
     * @return bool
     * @throws MessageException
     */
    protected function checkHostAccess(string $section, string $entry, bool $return = false): bool
    {
        return $this->tenantService->checkHostAccess($section, $entry, $return);
    }

    /**
     * @return void
     */
    protected function checkRoundSessions(): void
    {
        $round = $this->tenantService->round();
        if(!$round || $round->sessions->count() === 0)
        {
            throw new PlanningRoundException(trans('tontine.errors.checks.sessions'));
        }
    }

    /**
     * @return void
     */
    protected function checkRoundPools(): void
    {
        // First check for created sessions
        $this->checkRoundSessions();

        $round = $this->tenantService->round();
        if(!$round || $round->pools->count() === 0)
        {
            throw new PlanningPoolException(trans('tontine.errors.checks.pools'));
        }
    }

    /**
     * @return void
     */
    protected function checkOpenedSessions(): void
    {
        // First check for created sessions
        $this->checkRoundSessions();

        $round = $this->tenantService->round();
        if(!$round || $round->members()->count() === 0)
        {
            throw new TontineMemberException(trans('tontine.errors.checks.members'));
        }
        if($round->sessions->filter(fn($session) =>
            ($session->opened || $session->closed))->count() === 0)
        {
            throw new MeetingRoundException(trans('tontine.errors.checks.opened_sessions'));
        }
    }
}
