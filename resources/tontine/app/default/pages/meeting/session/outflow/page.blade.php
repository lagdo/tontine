@php
  $outflowId = jq()->parent()->attr('data-outflow-id')->toInt();
  $rqOutflowPage = rq(Ajax\App\Meeting\Session\Cash\OutflowPage::class);
  $rqOutflowFunc = rq(Ajax\App\Meeting\Session\Cash\OutflowFunc::class);
@endphp
                    <div class="table-responsive" id="content-session-outflows" @jxnEvent([
                      ['.btn-outflow-edit', 'click', $rqOutflowFunc->editOutflow($outflowId)],
                      ['.btn-outflow-delete', 'click', $rqOutflowFunc->deleteOutflow($outflowId)
                        ->confirm(__('meeting.outflow.questions.delete'))]])>

                      <table class="table table-bordered responsive">
                        <thead>
                          <tr>
                            <th>{!! __('meeting.labels.category') !!}</th>
                            <th>{!! __('meeting.labels.member') !!}</th>
                            <th>{!! __('meeting.labels.charge') !!}</th>
                            <th class="currency">{!! __('common.labels.amount') !!}</th>
                            <th class="table-item-menu">&nbsp;</th>
                          </tr>
                        </thead>
                        <tbody>
@foreach ($outflows as $outflow)
                          <tr>
                            <td>
                              <div>{{ $outflow->category->name }}</div>
@if ($outflow->comment)
                              <div>{{ $outflow->comment }}</div>
@endif
                            </td>
                            <td>{{ $outflow->member?->name ?? '' }}</td>
                            <td>{{ $outflow->charge?->name ?? '' }}</td>
                            <td class="currency">{{ $locale->formatMoney($outflow->amount) }}</td>
                            <td class="table-item-menu">
@include('tontine_app::parts.table.menu', [
  'dataIdKey' => 'data-outflow-id',
  'dataIdValue' => $outflow->id,
  'menus' => [[
    'class' => 'btn-outflow-edit',
    'text' => __('common.actions.edit'),
  ],[
    'class' => 'btn-outflow-delete',
    'text' => __('common.actions.delete'),
  ]],
])
                            </td>
                          </tr>
@endforeach
                        </tbody>
                      </table>
                      <nav @jxnPagination($rqOutflowPage)>
                      </nav>
                    </div> <!-- End table -->
