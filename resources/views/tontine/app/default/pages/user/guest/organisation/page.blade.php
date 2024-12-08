@php
  $tontineId = Jaxon\jq()->parent()->attr('data-tontine-id')->toInt();
  $rqSelect = Jaxon\rq(Ajax\App\Tontine\Select::class);
@endphp
                  <div class="table-responsive" @jxnTarget()>
                    <div @jxnEvent(['.btn-guest-tontine-choose', 'click'], $rqSelect->saveOrganisation($tontineId))></div>

                    <table class="table table-bordered responsive">
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
                          <td>{{ $tontine->name }}<br/><b>{{ $tontine->user->name }}</b></td>
                          <td>{{ $tontine->city }}</td>
                          <td>{{ $countries[$tontine->country_code] }}</td>
                          <td>{{ $currencies[$tontine->currency_code] }}</td>
                          <td class="table-item-menu">
@include('tontine.app.default.parts.table.menu', [
  'dataIdKey' => 'data-tontine-id',
  'dataIdValue' => $tontine->id,
  'menus' => [[
    'class' => 'btn-guest-tontine-choose',
    'text' => __('tontine.actions.choose'),
  ]],
])
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div>
