@php
  $memberId = Jaxon\jq()->parent()->attr('data-member-id')->toInt();
  $rqMember = Jaxon\rq(App\Ajax\Web\Tontine\Member\Member::class);
  $rqMemberPage = Jaxon\rq(App\Ajax\Web\Tontine\Member\MemberPage::class);
@endphp
                <div class="table-responsive" @jxnTarget()>
                  <div @jxnOn(['.btn-member-edit', 'click', ''], $rqMember->edit($memberId))></div>
                  <div @jxnOn(['.btn-member-toggle', 'click', ''], $rqMember->toggle($memberId))></div>
                  <div @jxnOn(['.btn-member-delete', 'click', ''], $rqMember->delete($memberId)
                    ->confirm(__('tontine.questions.delete')))></div>

                  <table class="table table-bordered responsive">
                    <thead>
                      <tr>
                        <th>{!! __('common.labels.name') !!}</th>
                        <th>{!! __('common.labels.email') !!}</th>
                        <th>{!! __('common.labels.phone') !!}</th>
                        <th>{!! __('common.labels.active') !!}</th>
                        <th class="table-menu"></th>
                      </tr>
                    </thead>
                    <tbody>
@foreach ($members as $member)
                      <tr>
                        <td>{{ $member->name }}</td>
                        <td>{{ $member->email }}</td>
                        <td>{{ $member->phone }}</td>
                        <td class="table-item-toggle" data-member-id="{{ $member->id }}">
                          <a role="link" class="btn-member-toggle"><i class="fa fa-toggle-{{ $member->active ? 'on' : 'off' }}"></i></a>
                        </td>
                        <td class="table-item-menu">
@include('tontine.app.default.parts.table.menu', [
  'dataIdKey' => 'data-member-id',
  'dataIdValue' => $member->id,
  'menus' => [[
    'class' => 'btn-member-edit',
    'text' => __('common.actions.edit'),
   ],[
    'class' => 'btn-member-delete',
    'text' => __('common.actions.delete'),
  ]],
])
                        </td>
                      </tr>
@endforeach
                    </tbody>
                  </table>
                  <nav @jxnPagination($rqMemberPage)>
                  </nav>
                </div>
