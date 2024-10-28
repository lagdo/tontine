<?php

namespace App\Ajax\Web\Meeting;

use App\Ajax\Component;
use App\Ajax\Web\Pagination;
use Jaxon\Plugin\Response\Pagination\Paginator;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Service\Meeting\SessionService;

use function trans;

/**
 * @databag meeting
 * @before getSession
 */
abstract class MeetingPageComponent extends Component
{
    /**
     * @di
     * @var SessionService
     */
    protected SessionService $sessionService;

    /**
     * The current page number.
     *
     * @var int
     */
    protected int $page = 1;

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = [];

    /**
     * Set the page number.
     *
     * @param int $pageNumber
     *
     * @return void
     */
    protected function setPageNumber(int $pageNumber): void
    {
        $bagName = $this->bagOptions[0];
        $attrName = $this->bagOptions[1] ?? 'page';
        $this->bag($bagName)->set($attrName, $pageNumber);

        $this->page = $pageNumber;
    }

    /**
     * Get the page number.
     *
     * @param int $pageNumber
     *
     * @return int
     */
    protected function getPageNumber(int $pageNumber): int
    {
        $bagName = $this->bagOptions[0];
        $attrName = $this->bagOptions[1] ?? 'page';

        return $pageNumber > 0 ? $pageNumber : (int)$this->bag($bagName)->get($attrName, 1);
    }

    /**
     * Get the total number of items to paginate.
     *
     * @return int
     */
    abstract protected function count(): int;

    /**
     * Render a page, and return a paginator for the component.
     *
     * @param int $pageNumber
     *
     * @return Paginator
     */
    protected function renderPage(int $pageNumber): Paginator
    {
        return $this->cl(Pagination::class)
            // Use the js class name as component item identifier.
            ->item($this->rq()->_class())
            ->pageNumber($this->getPageNumber($pageNumber))
            ->totalItems($this->count())
            ->itemsPerPage($this->tenantService->getLimit())
            ->paginator()
            ->page(function(int $page) {
                $this->setPageNumber($page);
                // Render the page content.
                $this->render();
            });
    }

    /**
     * @return int
     */
    protected function getSessionId(): int
    {
        return (int)$this->bag('meeting')->get('session.id');
    }

    /**
     * @return void
     */
    protected function getSession()
    {
        $session = $this->sessionService->getSession($this->getSessionId());
        if($session === null)
        {
            throw new MessageException(trans('meeting.errors.session.not_found'));
        }
        if($this->target()->method() !== 'reports' && !$session->opened)
        {
            throw new MessageException(trans('meeting.errors.session.not_opened'));
        }
        $this->cache->set('meeting.session', $session);
    }

    /**
     * @return void
     */
    protected function showBalanceAmounts()
    {
        $this->response->js()->showBalanceAmounts();
    }
}
