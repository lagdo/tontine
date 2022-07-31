<?php

namespace App\Ajax\App;

use Siak\Tontine\Service\MemberService;
use Siak\Tontine\Validation\MemberValidator;
use App\Ajax\CallableClass;

use function Jaxon\jq;
use function Jaxon\pm;
use function trans;

class Member extends CallableClass
{
    /**
     * @di
     * @var MemberService
     */
    public MemberService $memberService;

    /**
     * @var MemberValidator
     */
    public MemberValidator $validator;

    /**
     * @databag member
     */
    public function home()
    {
        $html = $this->view()->render('pages.member.home');
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
        if($pageNumber < 1)
        {
            $pageNumber = $this->bag('member')->get('page', 1);
        }
        $this->bag('member')->set('page', $pageNumber);

        $members = $this->memberService->getMembers($pageNumber);
        $memberCount = $this->memberService->getMemberCount();

        $html = $this->view()->render('pages.member.page')->with('members', $members)
            ->with('pagination', $this->rq()->page()->paginate($pageNumber, 10, $memberCount));
        $this->response->html('content-page', $html);

        $memberId = jq()->parent()->attr('data-member-id')->toInt()->toInt();
        $this->jq('.btn-member-edit')->click($this->rq()->edit($memberId));

        return $this->response;
    }

    public function number()
    {
        $title = trans('number.labels.title');
        $content = $this->view()->render('pages.member.number');
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
        $this->bag('faker')->set('member.count', $count);

        $html = $this->view()->render('pages.member.add')
            ->with('count', $count)
            ->with('genders', $this->memberService->getGenders());
        $this->response->html('content-home', $html);
        $this->jq('#btn-cancel')->click($this->rq()->home());
        $this->jq('#btn-fakes')->click($this->rq()->fakes());
        $this->jq('#btn-save')->click($this->rq()->create(pm()->form('member-form')));

        return $this->response;
    }

    /**
     * @databag faker
     */
    public function fakes()
    {
        $count = intval($this->bag('faker')->get('member.count'));
        $members = $this->memberService->getFakeMembers($count);
        for($i = 0; $i < $count; $i++)
        {
            $this->jq("#member_gender_$i")->val($members[$i]->gender);
            $this->jq("#member_name_$i")->val($members[$i]->name);
            $this->jq("#member_email_$i")->val($members[$i]->email);
            $this->jq("#member_phone_$i")->val($members[$i]->phone);
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
        $content = $this->view()->render('pages.member.edit')
            ->with('member', $member)
            ->with('genders', $this->memberService->getGenders());
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->update($member->id, pm()->form('member-form')),
        ]];
        $this->dialog->show($title, $content, $buttons, ['width' => '800']);

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
