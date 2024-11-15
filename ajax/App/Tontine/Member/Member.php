<?php

namespace Ajax\App\Tontine\Member;

use Ajax\Component;
use Ajax\App\Faker;
use Ajax\App\SectionContent;
use Ajax\App\SectionTitle;
use Jaxon\Response\AjaxResponse;
use Siak\Tontine\Service\Tontine\MemberService;
use Siak\Tontine\Validation\Tontine\MemberValidator;

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
class Member extends Component
{
    /**
     * @var string
     */
    protected $overrides = SectionContent::class;

    /**
     * @var MemberValidator
     */
    protected MemberValidator $validator;

    public function __construct(private MemberService $memberService)
    {}

    /**
     * @before checkGuestAccess ["tontine", "members"]
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
        $this->cl(SectionTitle::class)->show(trans('tontine.menus.tontine'));
        $this->bag('member')->set('search', '');
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderView('pages.member.home');
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(MemberPage::class)->page();
    }

    public function search(string $search)
    {
        $this->bag('member')->set('search', trim($search));

        return $this->cl(MemberPage::class)->page();
    }

    public function add()
    {
        $title = trans('tontine.member.titles.add');
        $content = $this->renderView('pages.member.add');
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
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.member.messages.created'));

        return $this->cl(MemberPage::class)->page();
    }

    public function addList()
    {
        $title = trans('tontine.member.titles.add');
        $content = $this->renderView('pages.member.list');
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
                'click' => $this->rq(Faker::class)->members(),
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
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.member.messages.created'));

        return $this->cl(MemberPage::class)->page();
    }

    public function edit(int $memberId)
    {
        $member = $this->memberService->getMember($memberId);

        $title = trans('tontine.member.titles.edit');
        $content = $this->renderView('pages.member.edit')
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
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.member.messages.updated'));

        return $this->cl(MemberPage::class)->page();
    }

    public function toggle(int $memberId)
    {
        $member = $this->memberService->getMember($memberId);
        $this->memberService->toggleMember($member);

        return $this->cl(MemberPage::class)->page();
    }

    public function delete(int $memberId)
    {
        $member = $this->memberService->getMember($memberId);
        $this->memberService->deleteMember($member);

        return $this->cl(MemberPage::class)->page();
    }
}
