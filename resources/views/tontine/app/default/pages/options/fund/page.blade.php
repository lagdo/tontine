@php
  $fundId = Jaxon\jq()->parent()->attr('data-fund-id')->toInt();
  $rqFund = Jaxon\rq(App\Ajax\Web\Tontine\Fund::class);
@endphp
                  <div class="table-responsive" @jxnTarget()>
                    <div @jxnOn(['.btn-fund-edit', 'click', ''], $rqFund->edit($fundId))></div>
                    <div @jxnOn(['.btn-fund-toggle', 'click', ''], $rqFund->toggle($fundId))></div>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.title') !!}</th>
                          <th class="table-item-menu">{!! __('common.labels.active') !!}</th>
                          <th class="table-item-menu"></th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($funds as $fund)
                        <tr>
                          <td>{{ $fund->title }}</td>
                          <td class="table-item-toggle" data-fund-id="{{ $fund->id }}">
                            <a role="link" class="btn-fund-toggle"><i class="fa fa-toggle-{{ $fund->active ? 'on' : 'off' }}"></i></a>
                          </td>
                          <td class="table-item-menu">
@include('tontine.app.default.parts.table.menu', [
  'dataIdKey' => 'data-fund-id',
  'dataIdValue' => $fund->id,
  'menus' => [[
    'class' => 'btn-fund-edit',
    'text' => __('common.actions.edit'),
  ]/*,[
    'class' => 'btn-fund-delete',
    'text' => __('common.actions.delete'),
  ]*/],
])
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
