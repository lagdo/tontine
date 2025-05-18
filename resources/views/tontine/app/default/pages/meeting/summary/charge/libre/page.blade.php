@php
  $chargeId = jq()->parent()->attr('data-charge-id')->toInt();
  $rqSettlement = rq(Ajax\App\Meeting\Summary\Charge\Libre\Settlement::class);
  $rqLibreFeePage = rq(Ajax\App\Meeting\Summary\Charge\Libre\FeePage::class);
@endphp
                  <div class="table-responsive" id="content-session-fees-libre-page" @jxnTarget()>
                    <div @jxnEvent(['.btn-fee-libre-settlements', 'click'], $rqSettlement->charge($chargeId))></div>

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
                          <td>
                            {{ $charge->name }}<br/>{{ $charge->has_amount ?
                              $locale->formatMoney($charge->amount) : __('tontine.labels.fees.variable') }}
                          </td>
                          <td class="currency">
                            {{ $sessionSettlementTotal }}/{{ $sessionBillTotal }} @if ($roundBillTotal > 0) - {{
                              $roundSettlementTotal }}/{{ $roundBillTotal }}@endif @if ($sessionSettlementAmount > 0)<br/>{{
                              $locale->formatMoney($sessionSettlementAmount) }}@endif
                          </td>
                          <td class="table-item-menu" data-charge-id="{{ $charge->id }}">
                            <button type="button" class="btn btn-primary btn-fee-libre-settlements"><i class="fa fa-arrow-circle-right"></i></button>
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqLibreFeePage)>
                    </nav>
                  </div> <!-- End table -->
