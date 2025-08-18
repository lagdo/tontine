@php
  $receivableId = jq()->parent()->attr('data-receivable-id')->toInt();
  $rqReceivableFunc = rq(Ajax\App\Meeting\Session\Pool\Deposit\ReceivableFunc::class);
  $rqReceivablePage = rq(Ajax\App\Meeting\Session\Pool\Deposit\ReceivablePage::class);
  $rqAmount = rq(Ajax\App\Meeting\Session\Pool\Deposit\Amount::class);
@endphp
                  <div class="table-responsive" id="content-meeting-receivables" @jxnEvent([
                    ['.btn-add-deposit', 'click', $rqReceivableFunc->addDeposit($receivableId)],
                    ['.btn-del-deposit', 'click', $rqReceivableFunc->delDeposit($receivableId)],
                  ])>
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
@php
  $paidLate = $receivable->paid_late;
@endphp
                        <tr>
                          <td>{{ $receivable->member }}@if ($paidLate)<br/>{{ $receivable->deposit->session->title }}@endif</td>
@if ($pool->deposit_fixed)
                          <td class="currency">{{ $locale->formatMoney($receivable->amount) }}</td>
                          <td class="table-item-menu" id="receivable-{{ $receivable->id }}" data-receivable-id="{{ $receivable->id }}">
                            {!! paymentLink($receivable->deposit, 'deposit', $paidLate) !!}
                          </td>
@else
@if ($paidLate)
                          <td class="currency">
                            <div class="input-group">
                              <input class="form-control" type="text" value="{{ $locale->formatMoney($receivable->deposit->amount) }}" readonly="readonly" style="height:36px; text-align:right">
                              <div class="input-group-append">
                                <button type="button" disabled="disabled" class="btn btn-secondary"><i class="fa fa-check"></i></button>
                              </div>
                            </div>
                          </td>
@else
@php
  $stash->set('meeting.session.receivable', $receivable);
@endphp
                          <td class="currency amount" @jxnBind($rqAmount, $receivable->id)>
                            @jxnHtml($rqAmount)
                          </td>
@endif
@endif
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqReceivablePage)>
                    </nav>
                  </div> <!-- End table -->
