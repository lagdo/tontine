@inject('locale', 'Siak\Tontine\Service\LocaleService')
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>{!! __('common.labels.title') !!}</th>
                    <th>{!! __('common.labels.dates') !!}</th>
                    <th>{!! __('tontine.pool.titles.deposits') !!}</th>
                    <th>{!! __('tontine.pool.titles.remitments') !!}</th>
                    <th class="table-menu"></th>
                  </tr>
                </thead>
                <tbody>
@foreach ($pools as $pool)
                  <tr>
                    <td>{{ $pool->title }}<br/><b>{{ $pool->round->title }}</b></td>
                    <td>
                      {{ $pool->start_at->translatedFormat(__('tontine.date.format')) }}<br/>
                      {{ $pool->end_at->translatedFormat(__('tontine.date.format')) }}
                    </td>
                    <td>
                      {!! __('common.labels.amount') !!}: {{ $pool->deposit_fixed ?
                        $locale->formatMoney($pool->amount) : __('tontine.labels.types.libre') }}<br/>
                      {!! __('tontine.pool.labels.lendable') !!}: {{ __('common.labels.' .
                        ($pool->deposit_lendable ? 'yes' : 'no')) }}
                    </td>
                    <td>
                      {!! __('tontine.pool.labels.planned') !!}: {{ __('common.labels.' .
                        ($pool->remit_planned ? 'yes' : 'no')) }}<br/>
                      {!! __('tontine.pool.labels.auction') !!}: {{ __('common.labels.' .
                        ($pool->remit_auction ? 'yes' : 'no')) }}
                    </td>
                    <td class="table-item-menu">
@php
  $sessionAction = $pool->round_id !== $round->id ? [] : [[
    'class' => 'btn-pool-sessions',
    'text' => __('tontine.pool.actions.sessions'),
  ]];
@endphp
@include('tontine.app.default.parts.table.menu', [
  'dataIdKey' => 'data-pool-id',
  'dataIdValue' => $pool->id,
  'menus' => [
    ...$sessionAction,
    [
      'class' => 'btn-pool-edit',
      'text' => __('common.actions.edit'),
    ],[
      'class' => 'btn-pool-delete',
      'text' => __('common.actions.delete'),
    ]
  ],
])
                    </td>
                  </tr>
@endforeach
                </tbody>
              </table>
{!! $pagination !!}
