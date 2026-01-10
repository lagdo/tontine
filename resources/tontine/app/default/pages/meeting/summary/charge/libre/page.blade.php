@php
  $chargeId = jq()->parent()->attr('data-charge-id')->toInt();
  $rqSettlement = rq(Ajax\App\Meeting\Summary\Charge\Libre\Settlement::class);
  $rqLibreFeePage = rq(Ajax\App\Meeting\Summary\Charge\Libre\FeePage::class);
@endphp
                  <div class="table-responsive" id="content-session-fees-libre-page" @jxnEvent([
                    ['.btn-fee-libre-settlements', 'click', $rqSettlement->charge($chargeId)]])>

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
  $chargeAmount = $charge->has_amount ? $locale->formatMoney($charge->amount) :
    __('tontine.labels.fees.variable');
@endphp
                        <tr>
                          <td>
                            <div>{{ $charge->name }}</div>
                            <div>
                              <div style="float:left">{{ $chargeAmount }}</div>
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
                            <button type="button" class="btn btn-primary btn-fee-libre-settlements"><i class="fa fa-arrow-circle-right"></i></button>
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqLibreFeePage)>
                    </nav>
                  </div> <!-- End table -->
