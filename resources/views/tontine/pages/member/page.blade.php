              <div class="table-responsive">
                <table class="table table-bordered">
                   <thead>
                    <tr>
                      <th>{!! __('common.labels.name') !!}</th>
                      <th>{!! __('common.labels.email') !!}</th>
                      <th>{!! __('common.labels.phone') !!}</th>
                      <th class="table-menu"></th>
                    </tr>
                  </thead>
                 <tbody>
@foreach ($members as $member)
                    <tr>
                      <td>{{ $member->name }}</td>
                      <td>{{ $member->email }}</td>
                      <td>{{ $member->phone }}</td>
                      <td class="table-item-menu">
@include('tontine.parts.table.menu', [
  'dataIdKey' => 'data-member-id',
  'dataIdValue' => $member->id,
  'menus' => [[
    'class' => 'btn-member-edit',
    'text' => __('common.actions.edit'),
  /*],[
    'class' => 'btn-member-disable',
    'text' => __('common.actions.disable'),*/
  ]],
  'links' => [],
])
                      </td>
                    </tr>
@endforeach
                  </tbody>
                </table>
                <nav>{!! $pagination !!}</nav>
              </div>
