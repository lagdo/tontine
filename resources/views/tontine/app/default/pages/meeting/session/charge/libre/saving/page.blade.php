@php
  $billId = jq()->parent()->attr('data-bill-id')->toInt();
  $fundId = je('settlement-saving-fund')->rd()->select()->toInt();
  $rqSavingFunc = rq(Ajax\App\Meeting\Session\Charge\Libre\SavingFunc::class);
  $rqSavingPage = rq(Ajax\App\Meeting\Session\Charge\Libre\SavingPage::class);
@endphp
                  <div class="table-responsive" id="content-session-fee-libre-bills" @jxnEvent([
                    ['.btn-add-settlement', 'click', $rqSavingFunc->addSettlement($billId, $fundId)],
                    ['.btn-del-settlement', 'click', $rqSavingFunc->delSettlement($billId)],
                  ])>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.name') !!}</th>
                          <th class="currency">{!! __('common.labels.amount') !!}</th>
                          <th class="table-item-menu">{!! __('common.labels.paid') !!}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($bills as $bill)
                        <tr>
                          <td>{{ $bill->member }}@if ($bill->libre && $bill->session->id !== $session->id) <br/>{{
                            $bill->session->title }} @endif</td>
                          <td class="currency">
                            <div>{{ $locale->formatMoney($bill->amount) }}</div>
@if ($bill->settlement?->fund !== null)
                            <div>{{ $bill->settlement->fund->title }}</div>
@endif
                          </td>
                          <td class="table-item-menu" data-bill-id="{{ $bill->id }}">
                            {!! paymentLink($bill->settlement, 'settlement', !$session->opened) !!}
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqSavingPage)>
                    </nav>
                  </div> <!-- End table -->
