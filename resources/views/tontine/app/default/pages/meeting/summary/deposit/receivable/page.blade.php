@php
  $rqReceivablePage = rq(Ajax\App\Meeting\Summary\Pool\Deposit\ReceivablePage::class);
@endphp
                  <div class="table-responsive" id="content-meeting-receivables" @jxnTarget()>
                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.name') !!}</th>
                          <th class="currency">{!! __('common.labels.amount') !!}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($receivables as $receivable)
                        <tr>
                          <td>{{ $receivable->member }}</td>
                          <td class="currency">{{ $locale->formatMoney(!$pool->deposit_fixed ?
                            $receivable->deposit->amount : $receivable->amount) }}</td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqReceivablePage)>
                    </nav>
                  </div> <!-- End table -->
