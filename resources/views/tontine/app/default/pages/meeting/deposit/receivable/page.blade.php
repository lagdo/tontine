@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $receivableId = jq()->parent()->attr('data-receivable-id')->toInt();
  $rqReceivable = rq(Ajax\App\Meeting\Session\Pool\Deposit\Receivable::class);
  $rqReceivablePage = rq(Ajax\App\Meeting\Session\Pool\Deposit\ReceivablePage::class);
  $rqAmount = rq(Ajax\App\Meeting\Session\Pool\Deposit\Amount::class);
@endphp
                  <div class="table-responsive" id="meeting-pool-deposits" @jxnTarget()>
                    <div @jxnEvent(['.btn-add-deposit', 'click'], $rqReceivable->addDeposit($receivableId))></div>
                    <div @jxnEvent(['.btn-del-deposit', 'click'], $rqReceivable->delDeposit($receivableId))></div>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.name') !!}</th>
                          <th class="currency">{!! __('common.labels.amount') !!}</th>
@if ($pool->deposit_fixed)
                          <th class="table-item-menu">&nbsp;</th>
@endif
                        </tr>
                      </thead>
                      <tbody>
@foreach ($receivables as $receivable)
                        <tr>
                          <td>{{ $receivable->member }}</td>
@if ($pool->deposit_fixed)
                          <td class="currency">{{ $locale->formatMoney($receivable->amount, true) }}</td>
                          <td class="table-item-menu" id="receivable-{{ $receivable->id }}" data-receivable-id="{{ $receivable->id }}">
                            {!! paymentLink($receivable->deposit, 'deposit', !$session->opened) !!}
                          </td>
@else
@php
  $stash->set('meeting.session.receivable', $receivable);
@endphp
                          <td class="currency" @jxnBind($rqAmount, $receivable->id) style="width:200px">
                            @jxnHtml($rqAmount)
                          </td>
@endif
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqReceivablePage)>
                    </nav>
                  </div> <!-- End table -->
