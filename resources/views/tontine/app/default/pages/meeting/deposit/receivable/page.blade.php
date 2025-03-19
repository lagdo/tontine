@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $receivableId = jq()->parent()->attr('data-receivable-id')->toInt();
  $rqReceivableFunc = rq(Ajax\App\Meeting\Session\Pool\Deposit\ReceivableFunc::class);
  $rqReceivablePage = rq(Ajax\App\Meeting\Session\Pool\Deposit\ReceivablePage::class);
  $rqAmount = rq(Ajax\App\Meeting\Session\Pool\Deposit\Amount::class);
@endphp
                  <div class="table-responsive" id="content-meeting-receivables" @jxnTarget()>
                    <div @jxnEvent(['.btn-add-deposit', 'click'], $rqReceivableFunc->addDeposit($receivableId))></div>
                    <div @jxnEvent(['.btn-del-deposit', 'click'], $rqReceivableFunc->delDeposit($receivableId))></div>

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
                          <td class="currency">{{ $locale->formatMoney($receivable->amount) }}</td>
                          <td class="table-item-menu" id="receivable-{{ $receivable->id }}" data-receivable-id="{{ $receivable->id }}">
                            {!! paymentLink($receivable->deposit, 'deposit', !$session->opened) !!}
                          </td>
@else
@php
  $stash->set('meeting.session.receivable', $receivable);
@endphp
                          <td class="currency amount" @jxnBind($rqAmount, $receivable->id)>
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
