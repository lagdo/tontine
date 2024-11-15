@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $poolId = Jaxon\jq()->parent()->attr('data-pool-id')->toInt();
  $rqPoolPage = Jaxon\rq(Ajax\App\Planning\Subscription\PoolPage::class);
  $rqMember = Jaxon\rq(Ajax\App\Planning\Subscription\Member::class);
  $rqSession = Jaxon\rq(Ajax\App\Planning\Subscription\Session::class);
  $rqPlanning = Jaxon\rq(Ajax\App\Planning\Subscription\Planning::class);
  $rqBeneficiary = Jaxon\rq(Ajax\App\Planning\Subscription\Beneficiary::class);
@endphp
                <div class="table-responsive" @jxnTarget()>
                  <div @jxnOn(['.btn-pool-member', 'click', ''], $rqMember->pool($poolId))></div>
                  <div @jxnOn(['.btn-pool-session', 'click', ''], $rqSession->pool($poolId))></div>
                  <div @jxnOn(['.btn-pool-planning', 'click', ''], $rqPlanning->pool($poolId))></div>
                  <div @jxnOn(['.btn-pool-beneficiary', 'click', ''], $rqBeneficiary->pool($poolId))></div>

                  <table class="table table-bordered responsive">
                    <thead>
                      <tr>
                        <th>{!! __('common.labels.title') !!}</th>
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
@php
  $plannedActions = !$pool->remit_planned ? [] : [[
    'class' => 'btn-pool-planning',
    'text' => __('tontine.subscription.actions.planning'),
  ],[
    'class' => 'btn-pool-beneficiary',
    'text' => __('tontine.subscription.actions.beneficiaries'),
  ]];
@endphp
@include('tontine.app.default.parts.table.menu', [
  'dataIdKey' => 'data-pool-id',
  'dataIdValue' => $pool->id,
  'menus' => [
    [
      'class' => 'btn-pool-member',
      'text' => __('tontine.subscription.actions.members'),
    ],[
      'class' => 'btn-pool-session',
      'text' => __('tontine.subscription.actions.sessions'),
    ],
    ...$plannedActions,
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
