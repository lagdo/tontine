@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $poolId = Jaxon\jq()->parent()->attr('data-pool-id')->toInt();
  $rqPoolPage = Jaxon\rq(Ajax\App\Planning\Pool\Session\PoolPage::class);
  $rqSession = Jaxon\rq(Ajax\App\Planning\Pool\Session\Pool\Session::class);
  $rqStartSession = Jaxon\rq(Ajax\App\Planning\Pool\Session\Pool\StartSession::class);
  $rqEndSession = Jaxon\rq(Ajax\App\Planning\Pool\Session\Pool\EndSession::class);
@endphp
                <div class="table-responsive" @jxnTarget()>
                  <div @jxnEvent(['.btn-pool-start-session', 'click'], $rqStartSession->pool($poolId))></div>
                  <div @jxnEvent(['.btn-pool-end-session', 'click'], $rqEndSession->pool($poolId))></div>
                  <div @jxnEvent(['.btn-pool-enabled-sessions', 'click'], $rqSession->pool($poolId))></div>

                  <table class="table table-bordered responsive">
                    <thead>
                      <tr>
                        <th>{!! __('tontine.labels.pool') !!}</th>
                        <th>{!! __('common.labels.dates') !!}</th>
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
                        <td class="table-item-menu">
@include('tontine.app.default.parts.table.menu', [
  'dataIdKey' => 'data-pool-id',
  'dataIdValue' => $pool->id,
  'menus' => [
    [
      'class' => 'btn-pool-start-session',
      'text' => __('tontine.pool_round.actions.start'),
    ],[
      'class' => 'btn-pool-end-session',
      'text' => __('tontine.pool_round.actions.end'),
    ],[
      'class' => 'btn-pool-enabled-sessions',
      'text' => __('tontine.pool_round.actions.enabled'),
    ],
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
