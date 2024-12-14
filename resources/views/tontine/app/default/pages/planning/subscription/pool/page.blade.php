@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $poolId = jq()->parent()->attr('data-pool-id')->toInt();
  $rqPoolPage = rq(Ajax\App\Planning\Subscription\PoolPage::class);
  $rqMember = rq(Ajax\App\Planning\Subscription\Member::class);
  $rqPlanning = rq(Ajax\App\Planning\Subscription\Planning::class);
  $rqBeneficiary = rq(Ajax\App\Planning\Subscription\Beneficiary::class);
@endphp
                <div class="table-responsive" @jxnTarget()>
                  <div @jxnEvent(['.btn-pool-member', 'click'], $rqMember->pool($poolId))></div>
                  <div @jxnEvent(['.btn-pool-planning', 'click'], $rqPlanning->pool($poolId))></div>
                  <div @jxnEvent(['.btn-pool-beneficiary', 'click'], $rqBeneficiary->pool($poolId))></div>

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
