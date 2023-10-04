@inject('locale', 'Siak\Tontine\Service\LocaleService')
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>{!! __('common.labels.title') !!}</th>
                    <th>{!! __('tontine.pool.titles.deposits') !!}:<br/>{!! __('common.labels.amount') !!}</th>
                    <th>{!! __('tontine.pool.titles.remitments') !!}:<br/>{!! __('tontine.pool.labels.fixed') !!}</th>
                    <th>{!! __('tontine.pool.titles.remitments') !!}:<br/>{!! __('tontine.pool.labels.planned') !!}</th>
                    <th>{!! __('tontine.pool.titles.remitments') !!}:<br/>{!! __('tontine.pool.labels.auction') !!}</th>
                    <th>{!! __('tontine.pool.titles.remitments') !!}:<br/>{!! __('tontine.pool.labels.lendable') !!}</th>
                    <th class="table-menu"></th>
                  </tr>
                </thead>
                <tbody>
@foreach ($pools as $pool)
                  <tr>
                    <td>{{ $pool->title }}</td>
                    <td>{{ $pool->deposit_fixed ?
                      $locale->formatMoney($pool->amount) : __('tontine.labels.types.libre') }}</td>
                    <td>{{ __('common.labels.' . ($pool->remit_fixed ? 'yes' : 'no')) }}</td>
                    <td>{{ __('common.labels.' . ($pool->remit_planned ? 'yes' : 'no')) }}</td>
                    <td>{{ __('common.labels.' . ($pool->remit_auction ? 'yes' : 'no')) }}</td>
                    <td>{{ __('common.labels.' . ($pool->remit_lendable ? 'yes' : 'no')) }}</td>
                    <td class="table-item-menu">
@include('tontine.parts.table.menu', [
  'dataIdKey' => 'data-pool-id',
  'dataIdValue' => $pool->id,
  'menus' => [[
    'class' => 'btn-pool-edit',
    'text' => __('common.actions.edit'),
  ],[
    'class' => 'btn-pool-delete',
    'text' => __('common.actions.delete'),
  ]],
])
                    </td>
                  </tr>
@endforeach
                </tbody>
              </table>
{!! $pagination !!}
