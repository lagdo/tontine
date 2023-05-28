<?php

namespace App\Ajax\App\Tontine;

use Siak\Tontine\Service\Tontine\MemberService;
use Siak\Tontine\Validation\Tontine\MemberValidator;
use App\Ajax\App\Faker;
use App\Ajax\CallableClass;

use function Jaxon\jq;
use function Jaxon\pm;
use function config;
use function trans;

class Member extends CallableClass
{
    /**
     * @di
     * @var MemberService
     */
    protected MemberService $memberService;

    /**
     * @var MemberValidator
     */
    protected MemberValidator $validator;

    /**
     * @databag member
     */
    public function home()
    {
        $html = $this->view()->render('tontine.pages.member.home');
        $this->response->html('section-title', trans('tontine.menus.tontine'));
        $this->response->html('content-home', $html);
        $this->jq('#btn-refresh')->click($this->rq()->home());
        $this->jq('#btn-create')->click($this->rq()->number());

        return $this->page($this->bag('member')->get('page', 1));
    }

    /**
     * @databag member
     */
    public function page(int $pageNumber = 0)
    {
        $memberCount = $this->memberService->getMemberCount();
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $memberCount, 'member', 'page');
        $members = $this->memberService->getMembers($pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $memberCount);

        $html = $this->view()->render('tontine.pages.member.page')
            ->with('members', $members)
            ->with('pagination', $pagination);
        $this->response->html('content-page', $html);

        $memberId = jq()->parent()->attr('data-member-id')->toInt();
        $this->jq('.btn-member-edit')->click($this->rq()->edit($memberId));

        return $this->response;
    }

    public function number()
    {
        $title = trans('number.labels.title');
        $content = $this->view()->render('tontine.pages.member.number');
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.add'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->add(pm()->input('text-number')),
        ]];
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @databag faker
     */
    public function add(int $count)
    {
        if($count <= 0)
        {
            $this->notify->warning(trans('number.errors.invalid'));
            return $this->response;
        }
        if($count > 10)
        {
            $this->notify->warning(trans('number.errors.max', ['max' => 10]));
            return $this->response;
        }

        $this->dialog->hide();

        $useFaker = config('jaxon.app.faker', false);
        $html = $this->view()->render('tontine.pages.member.add')
            ->with('useFaker', $useFaker)
            ->with('count', $count);
        $this->response->html('content-home', $html);
        $this->jq('#btn-cancel')->click($this->rq()->home());
        $this->jq('#btn-save')->click($this->rq()->create(pm()->form('member-form')));
        if($useFaker)
        {
            $this->bag('faker')->set('member.count', $count);
            $this->jq('#btn-fakes')->click($this->cl(Faker::class)->rq()->members());
        }

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function create(array $formValues)
    {
        $values = $this->validator->validateList($formValues['members'] ?? []);

        $this->memberService->createMembers($values);
        $this->notify->success(trans('tontine.member.messages.created'), trans('common.titles.success'));

        return $this->home();
    }

    public function edit(int $memberId)
    {
        $member = $this->memberService->getMember($memberId);

        $title = trans('tontine.member.titles.edit');
        $content = $this->view()->render('tontine.pages.member.edit')
            ->with('member', $member);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->update($member->id, pm()->form('member-form')),
        ]];
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function update(int $memberId, array $formValues)
    {
        $values = $this->validator->validateItem($formValues);

        $member = $this->memberService->getMember($memberId);

        $this->memberService->updateMember($member, $values);
        $this->page(); // Back to current page
        $this->dialog->hide();
        $this->notify->success(trans('tontine.member.messages.updated'), trans('common.titles.success'));

        return $this->response;
    }

    /*public function delete(int $memberId)
    {
        $this->notify->error("Cette fonction n'est pas encore disponible", trans('common.titles.error'));

        return $this->response;
    }*/
}
