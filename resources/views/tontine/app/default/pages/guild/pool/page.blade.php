@php
  $poolId = jq()->parent()->attr('data-pool-id')->toInt();
  $rqPoolFunc = rq(Ajax\App\Guild\Pool\PoolFunc::class);
  $rqPoolPage = rq(Ajax\App\Guild\Pool\PoolPage::class);
@endphp
                <div class="table-responsive" id="content-planning-pool-page" @jxnEvent([
                  ['.btn-pool-edit', 'click', $rqPoolFunc->edit($poolId)],
                  ['.btn-pool-delete', 'click', $rqPoolFunc->delete($poolId)
                    ->confirm(__('tontine.pool.questions.delete'))]])>

                  <table class="table table-bordered responsive">
                    <thead>
                      <tr>
                        <th>{!! __('common.labels.title') !!}</th>
                        <th>{!! __('tontine.pool.titles.deposits') !!}</th>
                        <th>{!! __('tontine.pool.titles.remitments') !!}</th>
                        <th>{!! __('common.labels.active') !!}</th>
                        <th class="table-menu"></th>
                      </tr>
                    </thead>
                    <tbody>
@foreach ($pools as $pool)
                      <tr>
                        <td>
                          <b>{{ $pool->title }}</b>
                        </td>
                        <td>
                          <div>{!! __('common.labels.amount') !!}: {{ $pool->deposit_fixed ?
                            $locale->formatMoney($pool->amount) : __('tontine.labels.types.libre') }}</div>
                          <div>{!! __('tontine.pool.labels.lendable') !!}: {{ __('common.labels.' .
                            ($pool->deposit_lendable ? 'yes' : 'no')) }}</div>
                        </td>
                        <td>
                          <div>{!! __('tontine.pool.labels.planned') !!}: {{ __('common.labels.' .
                            ($pool->remit_planned ? 'yes' : 'no')) }}</div>
                          <div>{!! __('tontine.pool.labels.auction') !!}: {{ __('common.labels.' .
                            ($pool->remit_auction ? 'yes' : 'no')) }}</div>
                        </td>
                        <td class="table-item-toggle" data-pool-id="{{ $pool->id }}">
                          <a role="link" tabindex="0" class="btn-pool-toggle"><i class="fa fa-toggle-{{ $pool->active ? 'on' : 'off' }}"></i></a>
                        </td>
                        <td class="table-item-menu">
@include('tontine::parts.table.menu', [
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
