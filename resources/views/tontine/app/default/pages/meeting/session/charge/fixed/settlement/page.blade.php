@php
  $billId = jq()->parent()->attr('data-bill-id')->toInt();
  $rqSettlementFunc = rq(Ajax\App\Meeting\Session\Charge\Fixed\SettlementFunc::class);
  $rqSettlementPage = rq(Ajax\App\Meeting\Session\Charge\Fixed\SettlementPage::class);
@endphp
                  <div class="table-responsive" id="content-session-fee-fixed-bills" @jxnTarget()>
                    <div @jxnEvent(['.btn-add-settlement', 'click'], $rqSettlementFunc->addSettlement($billId))></div>
                    <div @jxnEvent(['.btn-del-settlement', 'click'], $rqSettlementFunc->delSettlement($billId))></div>
                    <div @jxnEvent(['.btn-edit-notes', 'click'], $rqSettlementFunc->editNotes($billId))></div>

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
                          <td class="currency">{{ $locale->formatMoney($bill->amount) }}</td>
                          <td class="table-item-menu" data-bill-id="{{ $bill->id }}">
                            {!! paymentLink($bill->settlement, 'settlement', !$session->opened || !$charge->is_active) !!}
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqSettlementPage)>
                    </nav>
                  </div> <!-- End table -->
