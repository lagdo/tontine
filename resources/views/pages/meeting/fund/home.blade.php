                  <div class="row align-items-center">
                    <div class="col-auto">
                      <div class="section-title mt-0">{!! __('meeting.titles.funds') !!}</div>
                    </div>
@if($tontine->is_financial)
                    <div class="col">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-biddings"><i class="fa fa-user-shield"></i></button>
                      </div>
                    </div>
@endif
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.title') !!}</th>
                          <th>{!! __('common.labels.amount') !!}</th>
                          <th>&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach($funds as $fund)
@if($session->disabled($fund))
                        <tr style="background-color:rgba(0, 0, 0, 0.02)">
                          <td>{{ $fund->title }} [{{ $fund->recv_paid }}/{{ $fund->recv_count }}] [{{ $fund->pay_paid }}/{{ $fund->pay_count }}]</td>
                          <td>{{ $fund->money('amount') }}</td>
                          <td></td>
                        </tr>
@else
                        <tr>
                          <td>{{ $fund->title }} [{{ $fund->recv_paid }}/{{ $fund->recv_count }}] [{{ $fund->pay_paid }}/{{ $fund->pay_count }}]</td>
                          <td>{{ $fund->money('amount') }}</td>
                          <td class="table-item-menu">
@if($session->opened)
<?php
$menu = [[
    'class' => 'btn-fund-deposits',
    'text' => __('meeting.actions.deposits'),
]];
if($tontine->is_mutual)
{
    $menu[] = [
        'class' => 'btn-fund-remittances',
        'text' => __('meeting.actions.remittances'),
    ];
}
if($tontine->is_financial)
{
    $menu[] = [
        'class' => 'btn-fund-biddings',
        'text' => __('meeting.actions.biddings'),
    ];
}
?>
@include('parts.table.menu', [
  'dataIdKey' => 'data-fund-id',
  'dataIdValue' => $fund->id,
  'menus' => $menu,
])
@endif
                          </td>
                        </tr>
@endif
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
