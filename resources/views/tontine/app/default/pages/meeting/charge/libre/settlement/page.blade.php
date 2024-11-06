@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $billId = Jaxon\jq()->parent()->attr('data-bill-id')->toInt();
  $rqSettlement = Jaxon\rq(App\Ajax\Web\Meeting\Session\Charge\Libre\Settlement::class);
  $rqSettlementPage = Jaxon\rq(App\Ajax\Web\Meeting\Session\Charge\Libre\SettlementPage::class);
@endphp
                  <div class="table-responsive" id="meeting-fee-libre-bills" @jxnTarget()>
                    <div @jxnOn(['.btn-add-settlement', 'click', ''], $rqSettlement->addSettlement($billId))></div>
                    <div @jxnOn(['.btn-del-settlement', 'click', ''], $rqSettlement->delSettlement($billId))></div>
                    <div @jxnOn(['.btn-edit-notes', 'click', ''], $rqSettlement->editNotes($billId))></div>

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
                          <td class="currency">{{ $locale->formatMoney($bill->amount, true) }}</td>
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
