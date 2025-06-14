@php
  $fundId = jq()->parent()->attr('data-fund-id')->toInt();
  $rqSavingPage = rq(Ajax\App\Meeting\Session\Saving\SavingPage::class);
  $rqMember = rq(Ajax\App\Meeting\Session\Saving\Member::class);
  $rqAmountFunc = rq(Ajax\App\Meeting\Session\Saving\AmountFunc::class);
@endphp
                  <div class="table-responsive" id="content-session-funds-page" @jxnEvent([
                    ['.btn-fund-savings', 'click', $rqMember->fund($fundId)],
                    ['.btn-fund-start-amount', 'click', $rqAmountFunc->editStartAmount($fundId)],
                    ['.btn-fund-end-amount', 'click', $rqAmountFunc->editEndAmount($fundId)]])>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('meeting.labels.member') !!}</th>
                          <th class="currency">{{ __('common.labels.amount') }}</th>
                          <th class="table-item-menu">&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($funds as $fund)
@php
  $menus = [[
    'class' => 'btn-fund-savings',
    'text' => __('meeting.saving.actions.deposits'),
  ]];
  if($session->id === $fund->start_sid)
  {
    $menus[] = [
      'class' => 'btn-fund-start-amount',
      'text' => __('meeting.saving.actions.start_amount'),
    ];
  }
  if($session->id === $fund->end_sid)
  {
    $menus[] = [
      'class' => 'btn-fund-end-amount',
      'text' => __('meeting.saving.actions.end_amount'),
    ];
  }
@endphp
                        <tr>
                          <td>
                            {!! $fund->title !!}
@if($session->id === $fund->start_sid && $fund->start_amount > 0)
                            <br/>{{ __('meeting.saving.labels.start_amount', [
                              'amount' => $locale->formatMoney($fund->start_amount ?? 0),
                            ]) }}
@endif
@if($session->id === $fund->end_sid && $fund->end_amount > 0)
                            <br/>{{ __('meeting.saving.labels.end_amount', [
                              'amount' => $locale->formatMoney($fund->end_amount ?? 0),
                            ]) }}
@endif
                          </td>
                          <td class="currency">
                            {{ $fund->s_count ?? 0 }}<br/>{{ $locale->formatMoney($fund->s_amount ?? 0) }}
                          </td>
                          <td class="table-item-menu">
@include('tontine::parts.table.menu', [
  'dataIdKey' => 'data-fund-id',
  'dataIdValue' => $fund->id,
  'menus' => $menus,
])
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqSavingPage)>
                    </nav>
                  </div> <!-- End table -->
