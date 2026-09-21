@php
  $billId = jq()->parent()->attr('data-bill-id')->toInt();
  $rqSettlementFunc = rq(Ajax\App\Meeting\Session\Charge\Libre\SettlementFunc::class);
  $rqSettlementPage = rq(Ajax\App\Meeting\Session\Charge\Libre\SettlementPage::class);
@endphp
                  <div class="table-responsive" id="content-session-fee-libre-bills" @jxnEvent([
                    ['.btn-add-settlement', 'click', $rqSettlementFunc->addSettlement($billId)],
                    ['.btn-del-settlement', 'click', $rqSettlementFunc->delSettlement($billId)],
                    ['.btn-edit-notes', 'click', $rqSettlementFunc->editNotes($billId)]])>

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
                          <td>
                            <div>{{ $bill->member }}</div>
@if ($bill->libre && $bill->session->id !== $session->id)
                            <div>{{ $bill->session->title }}</div>
@endif
                          </td>
                          <td class="currency">
                            <div>{{ $locale->formatMoney($bill->amount) }}</div>
@if ($bill->settlement?->fund !== null)
                            <div>{!! $bill->settlement->fund->title !!}</div>
@endif
                          </td>
                          <td class="table-item-menu" data-bill-id="{{ $bill->id }}">
                            {!! paymentLink($bill->settlement, 'settlement', !$session->opened) !!}
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqSettlementPage)>
                    </nav>
                  </div> <!-- End table -->
