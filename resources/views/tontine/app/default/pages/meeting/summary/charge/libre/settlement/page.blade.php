@php
  $rqSettlementPage = rq(Ajax\App\Meeting\Summary\Charge\Libre\SettlementPage::class);
@endphp
                  <div class="table-responsive" id="content-session-fee-libre-bills">
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
                          <td class="table-item-menu">
                            <i class="fa fa-toggle-{{ $bill->settlement ? 'on' : 'off' }}"></i>
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqSettlementPage)>
                    </nav>
                  </div> <!-- End table -->
