@php
  $chargeId = jq()->parent()->attr('data-charge-id')->toInt();
  $rqSettlement = rq(Ajax\App\Meeting\Summary\Charge\Fixed\Settlement::class);
  $rqFixedFeePage = rq(Ajax\App\Meeting\Summary\Charge\Fixed\FeePage::class);
@endphp
                  <div class="table-responsive" id="content-session-fees-fixed-page" @jxnEvent([
                    ['.btn-fee-fixed-settlements', 'click', $rqSettlement->charge($chargeId)]])>

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
                            <div>{{ $charge->name }}</div>
                            <div>
                              <div style="float:left">{{ $locale->formatMoney($charge->amount) }}</div>
@if ($roundBillTotal > 0)
                              <div style="float:right">{{ $roundSettlementTotal }}/{{ $roundBillTotal }}</div>
@endif
                            </div>
                          </td>
                          <td class="currency">
                            <div>{{ $sessionSettlementTotal }}/{{ $sessionBillTotal }}</div>
@if ($sessionSettlementTotal > 0)
                            <div>{{ $locale->formatMoney($sessionSettlementAmount) }}</div>
@endif
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
