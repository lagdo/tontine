<?php

namespace Ajax\App\Guild\Member;

use Ajax\Base\Guild\FuncComponent;
use Ajax\App\Guild\FakerFunc;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Inject;
use Siak\Tontine\Service\Guild\MemberService;
use Siak\Tontine\Validation\Guild\MemberValidator;

use function Jaxon\form;
use function array_filter;
use function array_map;
use function config;
use function count;
use function explode;
use function strpos;
use function trans;
use function trim;

#[Before('checkHostAccess', ["guild", "members"])]
#[Databag('guild.member')]
class MemberFunc extends FuncComponent
{
    /**
     * @var MemberValidator
     */
    protected MemberValidator $validator;

    public function __construct(private MemberService $memberService)
    {}

    public function add(): void
    {
        $title = trans('tontine.member.titles.add');
        $content = $this->renderTpl('pages.guild.member.add');
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->create(form('member-form')),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    #[Inject(attr: 'validator')]
    public function create(array $formValues): void
    {
        $values = $this->validator->validateItem($formValues);

        $this->memberService->createMember($this->guild(), $values);
        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.member.messages.created'));

        $this->cl(MemberPage::class)->page();
    }

    public function addList(): void
    {
        $title = trans('tontine.member.titles.add');
        $content = $this->renderTpl('pages.guild.member.list');
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
                'click' => $this->rq(FakerFunc::class)->members(),
            ];
        }
        $buttons[] = [
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->createList(form('member-list')),
        ];
        $this->modal()->show($title, $content, $buttons);
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

    #[Inject(attr: 'validator')]
    public function createList(array $formValues): void
    {
        $values = $this->parseMemberList($formValues['members'] ?? '');

        $this->memberService->createMembers($this->guild(), $values);
        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.member.messages.created'));

        $this->cl(MemberPage::class)->page();
    }

    public function edit(int $memberId): void
    {
        $member = $this->memberService->getMember($this->guild(), $memberId);

        $title = trans('tontine.member.titles.edit');
        $content = $this->renderTpl('pages.guild.member.edit', ['member' => $member]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->update($member->id, form('member-form')),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    #[Inject(attr: 'validator')]
    public function update(int $memberId, array $formValues): void
    {
        $member = $this->memberService->getMember($this->guild(), $memberId);
        $values = $this->validator->validateItem($formValues);

        $this->memberService->updateMember($member, $values);
        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.member.messages.updated'));

        $this->cl(MemberPage::class)->page();
    }

    public function toggle(int $memberId): void
    {
        $member = $this->memberService->getMember($this->guild(), $memberId);
        $this->memberService->toggleMember($member);

        $this->cl(MemberPage::class)->page();
    }

    public function delete(int $memberId): void
    {
        $member = $this->memberService->getMember($this->guild(), $memberId);
        $this->memberService->deleteMember($member);

        $this->cl(MemberPage::class)->page();
    }
}
