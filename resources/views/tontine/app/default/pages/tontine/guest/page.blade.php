                    <table class="table table-bordered">
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
                          <td>{{ $tontine->name }}<br/><b>{{ $tontine->invites->first()->host->name }}</b></td>
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
{!! $pagination !!}
