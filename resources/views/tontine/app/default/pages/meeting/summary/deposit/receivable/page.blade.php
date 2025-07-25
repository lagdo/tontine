@php
  $rqReceivablePage = rq(Ajax\App\Meeting\Summary\Pool\Deposit\ReceivablePage::class);
@endphp
                  <div class="table-responsive" id="content-meeting-receivables">
                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.name') !!}</th>
                          <th class="currency">{!! __('common.labels.amount') !!}</th>
                          <th class="table-item-menu">{!! __('common.labels.paid') !!}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($receivables as $receivable)
                        <tr>
                          <td>{{ $receivable->member }}</td>
                          <td class="currency">
                            {{ !$receivable->deposit ? '' : $locale->formatMoney(!$pool->deposit_fixed ?
                              $receivable->deposit->amount : $receivable->amount) }}
                          </td>
                          <td class="table-item-menu">
                            <i class="fa @if($receivable->deposit) fa-toggle-on @else fa-toggle-off @endif"></i>
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqReceivablePage)>
                    </nav>
                  </div> <!-- End table -->
