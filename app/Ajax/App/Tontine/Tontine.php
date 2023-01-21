<?php

namespace App\Ajax\App\Tontine;

use Siak\Tontine\Service\Planning\RoundService;
use Siak\Tontine\Service\Tontine\MemberService;
use Siak\Tontine\Service\Tontine\TenantService;
use Siak\Tontine\Service\Tontine\TontineService;
use Siak\Tontine\Model\Tontine as TontineModel;
use App\Ajax\CallableClass;

use function Jaxon\jq;
use function Jaxon\pm;
use function session;
use function trans;

class Tontine extends CallableClass
{
    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @di
     * @var TontineService
     */
    protected TontineService $tontineService;

    /**
     * @var RoundService
     */
    protected RoundService $roundService;

    /**
     * @var MemberService
     */
    protected MemberService $memberService;

    /**
     * @di $tenantService
     * @di $roundService
     * @databag tontine
     */
    public function home()
    {
        $this->response->html('section-title', trans('tontine.menus.tontines'));
        $this->response->html('content-home', $this->view()->render('tontine.pages.tontine.home'));

        // Reset the sidebar menu
        // $this->cl(Select::class)->resetSidebarMenu();

        $this->jq('#btn-tontine-create')->click($this->rq()->add());
        $this->jq('#btn-tontine-refresh')->click($this->rq()->home());

        return $this->page();
    }

    /**
     * @databag tontine
     */
    public function page(int $pageNumber = 0)
    {
        if($pageNumber < 1)
        {
            $pageNumber = $this->bag('tontine')->get('page', 1);
        }
        $this->bag('tontine')->set('page', $pageNumber);

        $tontines = $this->tontineService->getTontines($pageNumber);
        $tontineCount = $this->tontineService->getTontineCount();

        $html = $this->view()->render('tontine.pages.tontine.page')
            ->with('types', $this->tontineService->getTontineTypes())
            ->with('countries', $this->tontineService->getcountries())
            ->with('tontines', $tontines)
            ->with('pagination', $this->rq()->page()->paginate($pageNumber, 10, $tontineCount));
        $this->response->html('tontine-page', $html);

        $tontineId = jq()->parent()->attr('data-tontine-id')->toInt();
        $this->jq('.btn-tontine-edit')->click($this->rq()->edit($tontineId));
        $this->jq('.btn-tontine-rounds')->click($this->cl(Round::class)->rq()->home($tontineId));

        return $this->response;
    }

    public function add()
    {
        $title = trans('tontine.titles.add');
        $content = $this->view()->render('tontine.pages.tontine.add')
            ->with('types', $this->tontineService->getTontineTypes());
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->create(pm()->form('tontine-form')),
        ]];
        $this->dialog->show($title, $content, $buttons, ['width' => '800']);

        return $this->response;
    }

    public function create(array $formValues)
    {
        $this->tontineService->createTontine($formValues);
        $this->page(); // Back to current page

        $this->dialog->hide();
        $this->notify->success(trans('tontine.messages.created'), trans('common.titles.success'));

        return $this->response;
    }

    public function edit(int $tontineId)
    {
        $tontine = $this->tontineService->getTontine($tontineId);

        $title = trans('tontine.titles.edit');
        $content = $this->view()->render('tontine.pages.tontine.edit')
            ->with('tontine', $tontine)
            ->with('types', $this->tontineService->getTontineTypes());
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->update($tontine->id, pm()->form('tontine-form')),
        ]];
        $this->dialog->show($title, $content, $buttons, ['width' => '800']);

        return $this->response;
    }

    public function update(int $tontineId, array $formValues)
    {
        $this->tontineService->updateTontine($tontineId, $formValues);
        $this->page(); // Back to current page

        $this->dialog->hide();
        $this->notify->success(trans('tontine.messages.updated'), trans('common.titles.success'));

        return $this->response;
    }
}
