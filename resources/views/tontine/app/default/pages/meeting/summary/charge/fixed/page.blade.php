@php
  $chargeId = jq()->parent()->attr('data-charge-id')->toInt();
  $rqSettlement = rq(Ajax\App\Meeting\Summary\Charge\Fixed\Settlement::class);
  $rqFixedFeePage = rq(Ajax\App\Meeting\Summary\Charge\Fixed\FeePage::class);
@endphp
                  <div class="table-responsive" id="content-session-fees-fixed-page" @jxnTarget()>
                    <div @jxnEvent(['.btn-fee-fixed-settlements', 'click'], $rqSettlement->charge($chargeId))></div>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.title') !!}</th>
                          <th>&nbsp;</th>
                          <th>&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($charges as $charge)
@php
  $sessionBillTotal = $bills['total']['session'][$charge->id] ?? 0;
  $roundBillTotal = $bills['total']['round'][$charge->id] ?? 0;
  $sessionSettlementTotal = $settlements['total']['session'][$charge->id] ?? 0;
  $roundSettlementTotal = $settlements['total']['round'][$charge->id] ?? 0;
  $sessionSettlementAmount = $settlements['amount']['session'][$charge->id] ?? 0;
@endphp
                        <tr>
                          <td @if (!$charge->is_active) style="text-decoration:line-through" @endif>
                            {{ $charge->name }}<br/>{{ $locale->formatMoney($charge->amount) }}
                          </td>
                          <td class="currency">
                            {{ $sessionSettlementTotal }}/{{ $sessionBillTotal }} @if ($roundBillTotal > 0) - {{
                              $roundSettlementTotal }}/{{ $roundBillTotal }}@endif @if ($sessionSettlementAmount > 0)<br/>{{
                              $locale->formatMoney($sessionSettlementAmount) }}@endif
                          </td>
                          <td class="table-item-menu" data-charge-id="{{ $charge->id }}">
                            <button type="button" class="btn btn-primary btn-fee-fixed-settlements"><i class="fa fa-arrow-circle-right"></i></button>
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqFixedFeePage)>
                    </nav>
                  </div> <!-- End table -->
