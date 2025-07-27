@php
  $defId = jq()->parent()->attr('data-def-id')->toInt();
  $poolId = jq()->parent()->attr('data-pool-id')->toInt();
  $rqPoolFunc = rq(Ajax\App\Planning\Pool\PoolFunc::class);
  $rqPoolPage = rq(Ajax\App\Planning\Pool\PoolPage::class);
  $rqSession = rq(Ajax\App\Planning\Pool\Session::class);
  $rqMember = rq(Ajax\App\Planning\Pool\Subscription\Member::class);
  $rqPlanning = rq(Ajax\App\Planning\Pool\Subscription\Planning::class);
  $rqBeneficiary = rq(Ajax\App\Planning\Pool\Subscription\Beneficiary::class);
@endphp
                <div class="table-responsive" id="content-planning-pool-page" @jxnEvent([
                  ['.btn-pool-enable', 'click', $rqPoolFunc->enable($defId)],
                  ['.btn-pool-disable', 'click', $rqPoolFunc->disable($defId)
                    ->confirm(__('tontine.pool.questions.disable'))],
                  ['.btn-pool-sessions', 'click', $rqSession->pool($poolId)],
                  ['.btn-pool-subscription', 'click', $rqMember->pool($poolId)],
                  ['.btn-pool-planning', 'click', $rqPlanning->pool($poolId)],
                  ['.btn-pool-beneficiary', 'click', $rqBeneficiary->pool($poolId)]])>

                  <table class="table table-bordered responsive">
                    <thead>
                      <tr>
                        <th>{!! __('common.labels.title') !!}</th>
                        <th class="table-menu">&nbsp;</th>
                        <th class="table-menu">&nbsp;</th>
                      </tr>
                    </thead>
                    <tbody>
@foreach ($defs as $def)
@php
  $count = $def->pools->count();
  $toggleClass = $count > 0 ? 'btn-pool-disable' : 'btn-pool-enable';
  $toggleIcon = $count > 0 ? 'fa fa-toggle-on' : 'fa fa-toggle-off';
@endphp
                      <tr>
                        <td><b>{{ $def->title }}</b></td>
                        <td class="table-item-toggle" data-def-id="{{ $def->id }}">
                          <a role="link" tabindex="0" class="{{ $toggleClass }}"><i class="{{ $toggleIcon }}"></i></a>
@if ($def->pools_in_round_count > $count)
                          <i class="fa fa-compress-alt fa-rotate-by rotate-by-45deg"></i>
@endif
                        </td>
                        <td class="table-item-menu">
@if($count > 0)
@php
  $menus = !$def->remit_planned ? [] : [null, [
      'class' => 'btn-pool-planning',
      'text' => __('tontine.subscription.actions.planning'),
    ], [
      'class' => 'btn-pool-beneficiary',
      'text' => __('tontine.subscription.actions.beneficiaries'),
    ]];
@endphp
@include('tontine::parts.table.menu', [
  'dataIdKey' => 'data-pool-id',
  'dataIdValue' => $def->pools->first()->id,
  'menus' => [
    [
      'class' => 'btn-pool-sessions',
      'text' => __('tontine.actions.sessions'),
    ],
    [
      'class' => 'btn-pool-subscription',
      'text' => __('tontine.subscription.actions.subscriptions'),
    ],
    ...$menus,
  ],
])
@endif
                        </td>
                      </tr>
@endforeach
                    </tbody>
                  </table>
                  <nav @jxnPagination($rqPoolPage)>
                  </nav>
                </div>
