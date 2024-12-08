<?php

namespace Ajax;

use Jaxon\App\CallableClass as JaxonCallableClass;
use Jaxon\App\View\Store;
use Siak\Tontine\Exception\MeetingRoundException;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Exception\PlanningPoolException;
use Siak\Tontine\Exception\PlanningRoundException;
use Siak\Tontine\Exception\TontineMemberException;
use Siak\Tontine\Service\TenantService;

use function floor;
use function trans;

/**
 * @databag tenant
 * @callback jaxon.ajax.callback.tontine
 */
class CallableClass extends JaxonCallableClass
{
    use CallableTrait;

    /**
     * @di
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * Get the page number to show
     *
     * @param int $pageNumber
     * @param int $itemCount
     * @param string $bagName
     * @param string $attrName
     *
     * @return array
     */
    protected function pageNumber(int $pageNumber, int $itemCount,
        string $bagName, string $attrName = 'page'): array
    {
        $perPage = 10;
        $pageCount = (int)floor($itemCount / $perPage) + ($itemCount % $perPage > 0 ? 1 : 0);
        if($pageNumber < 1)
        {
            $pageNumber = (int)$this->bag($bagName)->get($attrName, 1);
            if($pageNumber < 1)
            {
                $pageNumber = 1;
            }
        }
        if($pageNumber > $pageCount)
        {
            $pageNumber = $pageCount;
        }
        $this->bag($bagName)->set($attrName, $pageNumber);

        return [$pageNumber, $this->tenantService->getLimit()];
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
    protected function hideMenuOnMobile()
    {
        // The current template main menu doesn't hide automatically
        // after a click on mobile devices. We need to do that manually.
        $this->response->jq('body')->trigger('touchend');
    }

    /**
     * Render a view
     *
     * @param string $view
     * @param array $viewData
     *
     * @return null|Store
     */
    protected function renderView(string $view, array $viewData = []): ?Store
    {
        return $this->view()->render("tontine::$view", $viewData);
    }

    /**
     * @return void
     */
    protected function checkRoundSessions()
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
    protected function checkRoundPools()
    {
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
    protected function checkOpenedSessions()
    {
        // First check for created sessions
        $this->checkRoundSessions();

        $tontine = $this->tenantService->tontine();
        if(!$tontine || $tontine->members()->active()->count() === 0)
        {
            throw new TontineMemberException(trans('tontine.errors.checks.members'));
        }

        $round = $this->tenantService->round();
        if(!$round || $round->sessions->filter(fn($session) =>
            ($session->opened || $session->closed))->count() === 0)
        {
            throw new MeetingRoundException(trans('tontine.errors.checks.opened_sessions'));
        }
    }
}
