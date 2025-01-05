<?php

namespace Ajax\App\Planning\Session;

use Ajax\Component;
use Ajax\App\Page\SectionContent;
use Ajax\App\Page\SectionTitle;
use Ajax\App\Tontine\Select;
use Jaxon\Response\AjaxResponse;
use Siak\Tontine\Service\Planning\RoundService;
use Stringable;

use function Jaxon\pm;
use function trans;

/**
 * @databag planning
 */
class Round extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @param RoundService $roundService
     */
    public function __construct(protected RoundService $roundService)
    {}

    /**
     * @before checkHostAccess ["planning", "sessions"]
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
        $this->cl(SectionTitle::class)->show(trans('tontine.menus.planning'));
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.planning.round.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(RoundPage::class)->page();

        // Show the session of the default round.
        $round = $this->tenantService->round();
        if($round !== null)
        {
            $this->bag('planning')->set('round.id', $round->id);
            $this->stash()->set('planning.round', $round);
            $this->cl(Session::class)->render();
        }

        $this->response->js('Tontine')->showSmScreen('content-home-sessions', 'round-sm-screens');
    }

    public function add()
    {
        $title = trans('tontine.round.titles.add');
        $content = $this->renderView('pages.planning.round.add');
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->create(pm()->form('round-form')),
        ]];
        $this->modal()->show($title, $content, $buttons);

        return $this->response;
    }

    public function create(array $formValues)
    {
        $this->roundService->createRound($formValues);

        $this->cl(RoundPage::class)->page(); // Back to current page
        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.round.messages.created'));

        return $this->response;
    }

    public function edit(int $roundId)
    {
        $round = $this->roundService->getRound($roundId);

        $title = trans('tontine.round.titles.edit');
        $content = $this->renderView('pages.planning.round.edit')->with('round', $round);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->update($round->id, pm()->form('round-form')),
        ]];
        $this->modal()->show($title, $content, $buttons);

        return $this->response;
    }

    public function update(int $roundId, array $formValues)
    {
        $this->roundService->updateRound($roundId, $formValues);

        $this->cl(RoundPage::class)->page(); // Back to current page
        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.round.messages.updated'));

        return $this->response;
    }

    public function delete(int $roundId)
    {
        $this->roundService->deleteRound($roundId);

        $this->cl(RoundPage::class)->page(); // Back to current page
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.round.messages.deleted'));

        $currentRound = $this->tenantService->round();
        if($currentRound !== null && $currentRound->id === $roundId)
        {
            // If the currently selected round is deleted, then choose another.
            $this->cl(Select::class)->saveOrganisation($this->tenantService->tontine()->id);
        }

        return $this->response;
    }
}
