@php
  $tontineId = Jaxon\jq()->parent()->attr('data-tontine-id')->toInt();
  $rqTontine = Jaxon\rq(App\Ajax\Web\Tontine\Tontine::class);
  $rqTontinePage = Jaxon\rq(App\Ajax\Web\Tontine\TontinePage::class);
  $rqSelect = Jaxon\rq(App\Ajax\Web\Tontine\Select::class);
@endphp
                <div class="table-responsive" @jxnTarget()>
                  <div @jxnOn(['.btn-tontine-edit', 'click', ''], $rqTontine->edit($tontineId))></div>
                  <div @jxnOn(['.btn-tontine-choose', 'click', ''], $rqSelect->saveTontine($tontineId))></div>
                  <div @jxnOn(['.btn-tontine-delete', 'click', ''], $rqTontine->delete($tontineId)
                    ->confirm(__('tontine.questions.delete')))></div>

                  <table class="table table-bordered responsive" @jxnTarget()>
                    <thead>
                      <tr>
                        <th>{!! __('common.labels.name') !!}</th>
                        <th>{!! __('common.labels.city') !!}</th>
                        <th>{!! __('common.labels.country') !!}</th>
                        <th>{!! __('common.labels.currency') !!}</th>
                        <th class="table-item-menu"></th>
                      </tr>
                    </thead>
                    <tbody>
@foreach ($tontines as $tontine)
                      <tr>
                        <td>{{ $tontine->name }}</td>
                        <td>{{ $tontine->city }}</td>
                        <td>{{ $countries[$tontine->country_code] }}</td>
                        <td>{{ $currencies[$tontine->currency_code] }}</td>
                        <td class="table-item-menu">
@include('tontine.app.default.parts.table.menu', [
'dataIdKey' => 'data-tontine-id',
'dataIdValue' => $tontine->id,
'menus' => [[
  'class' => 'btn-tontine-edit',
  'text' => __('common.actions.edit'),
],[
  'class' => 'btn-tontine-choose',
  'text' => __('tontine.actions.choose'),
],[
  'class' => 'btn-tontine-delete',
  'text' => __('common.actions.delete'),
]],
])
                        </td>
                      </tr>
@endforeach
                    </tbody>
                  </table>
                  <nav @jxnPagination($rqTontinePage)>
                  </nav>
                </div>
