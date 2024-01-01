<?php

namespace App\Ajax\Web\Tontine;

use Siak\Tontine\Service\Tontine\MemberService;
use Siak\Tontine\Validation\Tontine\MemberValidator;
use App\Ajax\Web\Faker;
use App\Ajax\CallableClass;

use function Jaxon\jq;
use function Jaxon\pm;
use function array_filter;
use function array_map;
use function config;
use function count;
use function explode;
use function strpos;
use function trans;
use function trim;

/**
 * @databag member
 */
class Member extends CallableClass
{
    /**
     * @var MemberValidator
     */
    protected MemberValidator $validator;

    public function __construct(private MemberService $memberService)
    {}

    /**
     * @after hideMenuOnMobile
     */
    public function home()
    {
        $html = $this->render('pages.member.home');
        $this->response->html('section-title', trans('tontine.menus.tontine'));
        $this->response->html('content-home', $html);

        $this->jq('#btn-member-refresh')->click($this->rq()->home());
        $this->jq('#btn-member-add')->click($this->rq()->add());
        $this->jq('#btn-member-add-list')->click($this->rq()->addList());

        return $this->page();
    }

    public function page(int $pageNumber = 0)
    {
        $search = trim($this->bag('member')->get('search', ''));
        $memberCount = $this->memberService->getMemberCount($search);
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $memberCount, 'member', 'page');
        $members = $this->memberService->getMembers($search, $pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $memberCount);

        $html = $this->render('pages.member.page', [
            'search' => $search,
            'members' => $members,
            'pagination' => $pagination,
        ]);
        $this->response->html('content-page', $html);

        $memberId = jq()->parent()->attr('data-member-id')->toInt();
        $this->jq('.btn-member-edit')->click($this->rq()->edit($memberId));
        $this->jq('.btn-member-toggle')->click($this->rq()->toggle($memberId));
        $this->jq('.btn-member-delete')->click($this->rq()->delete($memberId)
            ->confirm(trans('tontine.member.questions.delete')));
        $this->jq('#btn-member-search')
            ->click($this->rq()->search(jq('#txt-member-search')->val()));

        return $this->response;
    }

    public function search(string $search)
    {
        $this->bag('member')->set('search', trim($search));

        return $this->page();
    }

    public function add()
    {
        $title = trans('tontine.member.titles.add');
        $content = $this->render('pages.member.add');
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->create(pm()->form('member-form')),
        ]];
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function create(array $formValues)
    {
        $values = $this->validator->validateItem($formValues);
        $this->memberService->createMember($values);
        $this->dialog->hide();
        $this->notify->success(trans('tontine.member.messages.created'), trans('common.titles.success'));

        return $this->page();
    }

    public function addList()
    {
        $title = trans('tontine.member.titles.add');
        $content = $this->render('pages.member.list');
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ]];
        $useFaker = config('jaxon.app.faker', false);
        if($useFaker)
        {
            $buttons[] = [
                'title' => '<i class="fa fa-fill"></i>',
                'class' => 'btn btn-primary',
                'click' => $this->cl(Faker::class)->rq()->members(),
            ];
        }
        $buttons[] = [
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->createList(pm()->form('member-list')),
        ];
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @param string $members
     *
     * @return array
     */
    private function parseMemberList(string $members): array
    {
        $members = array_map(function($value) {
            $values = explode(";", trim($value, " \t\n\r;"));
            if(count($values) === 0 || trim($values[0]) === '')
            {
                return [];
            }
            $member = [
                'name' => $values[0],
                'email' => '',
                'phone' => '',
            ];
            // The next values are either the phone number or the email, in any order.
            foreach([1, 2] as $count)
            {
                if(count($values) > $count)
                {
                    $field = strpos($values[$count], '@') !== false ? 'email' : 'phone';
                    $member[$field] = trim($values[$count]);
                }
            }

            return $member;
        }, explode("\n", trim($members, " \t\n\r;")));
        // Filter empty lines.
        $members = array_filter($members, function($member) {
            return count($member) > 0;
        });

        return $this->validator->validateList($members);
    }

    /**
     * @di $validator
     */
    public function createList(array $formValues)
    {
        $values = $this->parseMemberList($formValues['members'] ?? '');

        $this->memberService->createMembers($values);
        $this->dialog->hide();
        $this->notify->success(trans('tontine.member.messages.created'), trans('common.titles.success'));

        return $this->page();
    }

    public function edit(int $memberId)
    {
        $member = $this->memberService->getMember($memberId);

        $title = trans('tontine.member.titles.edit');
        $content = $this->render('pages.member.edit')
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
        $member = $this->memberService->getMember($memberId);
        $values = $this->validator->validateItem($formValues);

        $this->memberService->updateMember($member, $values);
        $this->dialog->hide();
        $this->notify->success(trans('tontine.member.messages.updated'), trans('common.titles.success'));

        return $this->page();
    }

    public function toggle(int $memberId)
    {
        $member = $this->memberService->getMember($memberId);
        $this->memberService->toggleMember($member);

        return $this->page();
    }

    public function delete(int $memberId)
    {
        $member = $this->memberService->getMember($memberId);
        $this->memberService->deleteMember($member);

        return $this->page();
    }
}
