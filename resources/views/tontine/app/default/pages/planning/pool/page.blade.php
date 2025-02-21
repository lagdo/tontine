@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $poolId = jq()->parent()->attr('data-pool-id')->toInt();
  $rqPoolFunc = rq(Ajax\App\Planning\Pool\PoolFunc::class);
  $rqPoolPage = rq(Ajax\App\Planning\Pool\PoolPage::class);
@endphp
                <div class="table-responsive" id="content-planning-pool-page" @jxnTarget()>
                  <div @jxnEvent(['.btn-pool-edit', 'click'], $rqPoolFunc->edit($poolId))></div>
                  <div @jxnEvent(['.btn-pool-delete', 'click'], $rqPoolFunc->delete($poolId)
                    ->confirm(__('tontine.pool.questions.delete')))></div>

                  <table class="table table-bordered responsive">
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
                        <td>
                          <b>{{ $pool->title }}<br/>{{ $pool->round->title }}</b></td>
                        <td>
                          {{ $pool->start_at?->translatedFormat(__('tontine.date.format')) ?? '' }}<br/>
                          {{ $pool->end_at?->translatedFormat(__('tontine.date.format')) ?? '' }}
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
@include('tontine.app.default.parts.table.menu', [
  'dataIdKey' => 'data-pool-id',
  'dataIdValue' => $pool->id,
  'menus' => [
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
                  <nav @jxnPagination($rqPoolPage)>
                  </nav>
                </div>

