@php
  $rqSettlementPage = rq(Ajax\App\Meeting\Summary\Charge\Fixed\SettlementPage::class);
@endphp
                  <div class="table-responsive" id="content-session-fee-fixed-bills" @jxnTarget()>
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
                            <i class="fa fa-toggle-{{ $bill->settlement ? 'on' : 'off' }}"></i>
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqSettlementPage)>
                    </nav>
                  </div> <!-- End table -->
