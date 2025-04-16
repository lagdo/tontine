@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $outflowId = jq()->parent()->attr('data-outflow-id')->toInt();
  $rqOutflow = rq(Ajax\App\Meeting\Session\Cash\Outflow::class);
  $rqOutflowFunc = rq(Ajax\App\Meeting\Session\Cash\OutflowFunc::class);
  $rqBalance = rq(Ajax\App\Meeting\Session\Cash\Balance::class);
@endphp
                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.outflows') }}</div>
                    </div>
                    <div class="col-auto" @jxnBind($rqBalance)>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqOutflowFunc->addOutflow())><i class="fa fa-plus"></i></button>
                        <button type="button" class="btn btn-primary" @jxnClick($rqOutflow->home())><i class="fa fa-sync"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive" id="content-session-outflows" @jxnTarget()>
                    <div @jxnEvent(['.btn-outflow-edit', 'click'], $rqOutflowFunc->editOutflow($outflowId))></div>
                    <div @jxnEvent(['.btn-outflow-delete', 'click'], $rqOutflowFunc->deleteOutflow($outflowId)
                      ->confirm(__('meeting.outflow.questions.delete')))></div>

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
                          <td>{{ $outflow->category->name }}@if (($outflow->comment)) <br/>{{
                            $outflow->comment }}@endif</td>
                          <td>@if (($outflow->member)) {{ $outflow->member->name }}@endif</td>
                          <td>@if (($outflow->charge)) {{ $outflow->charge->name }}@endif</td>
                          <td class="currency">{{ $locale->formatMoney($outflow->amount) }}</td>
                          <td class="table-item-menu">
@include('tontine::parts.table.menu', [
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
                  </div> <!-- End table -->
