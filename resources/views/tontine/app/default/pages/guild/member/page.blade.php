@php
  $memberId = jq()->parent()->attr('data-member-id')->toInt();
  $rqMemberFunc = rq(Ajax\App\Guild\Member\MemberFunc::class);
  $rqMemberPage = rq(Ajax\App\Guild\Member\MemberPage::class);
@endphp
                <div class="table-responsive" id="content-member-page" @jxnEvent([
                  ['.btn-member-edit', 'click', $rqMemberFunc->edit($memberId)],
                  ['.btn-member-toggle', 'click', $rqMemberFunc->toggle($memberId)],
                  ['.btn-member-delete', 'click', $rqMemberFunc->delete($memberId)
                    ->confirm(__('tontine.member.questions.delete'))]])>

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
                          <a role="link" tabindex="0" class="btn-member-toggle"><i class="fa fa-toggle-{{ $member->active ? 'on' : 'off' }}"></i></a>
                        </td>
                        <td class="table-item-menu">
@include('tontine::parts.table.menu', [
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
