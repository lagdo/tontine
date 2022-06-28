                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.title') !!}</th>
                          <th>&nbsp;</th>
                          <th>&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($charges as $charge)
                        <tr>
                          <td>{{ $charge->name }} ({{ $charge->money('amount') }})</td>
                          <td>[{{ $charge->members_paid }}/{{ $charge->members_count }}]</td>
                          <td class="table-item-menu">
@if($session->opened)
@if( $charge->is_fee )
@include('parts.table.menu', [
  'dataIdKey' => 'data-charge-id',
  'dataIdValue' => $charge->id,
  'menus' => [[
    'class' => 'btn-charge-settlements',
    'text' => __('meeting.actions.settlements'),
  ]],
])
@else
@include('parts.table.menu', [
  'dataIdKey' => 'data-charge-id',
  'dataIdValue' => $charge->id,
  'menus' => [[
    'class' => 'btn-charge-fine',
    'text' => __('meeting.actions.fine'),
  ],[
    'class' => 'btn-charge-settlements',
    'text' => __('meeting.actions.settlements'),
  ]],
])
@endif
@endif
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    {!! $pagination !!}
