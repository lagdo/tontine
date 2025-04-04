@inject('locale', 'Siak\Tontine\Service\LocaleService')
@inject('debtCalculator', 'Siak\Tontine\Service\Meeting\Credit\DebtCalculator')
@inject('paymentService', 'Siak\Tontine\Service\Meeting\PaymentServiceInterface')
@php
  $rqPartialRefundPage = rq(Ajax\App\Meeting\Summary\Refund\Partial\RefundPage::class);
@endphp
                    <div class="table-responsive" id="content-summary-partial-refunds-page">
                      <table class="table table-bordered responsive">
                        <thead>
                          <tr>
                            <th>{!! __('meeting.refund.labels.loan') !!}</th>
                            <th class="currency">{!! __('common.labels.amount') !!}</th>
                            <th class="table-item-menu">&nbsp;</th>
                          </tr>
                        </thead>
                        <tbody>
@foreach($refunds as $refund)
                          <tr>
                            <td>
                              {{ $refund->debt->loan->member->name }}<br/> {{
                                __('meeting.loan.labels.' . $refund->debt->type) }}: {{ $refund->debt->loan->session->title }}
                            </td>
                            <td class="currency">
                              {{ $locale->formatMoney($refund->amount) }}<br/>
                              {{ $locale->formatMoney($debtCalculator->getDebtDueAmount($refund->debt, $session, false)) }}
                            </td>
@if( $refund->debt->refund !== null || !$paymentService->isEditable($refund) )
                            <td class="table-item-menu">
                              {!! paymentLink($refund, 'partial-refund', true) !!}
                            </td>
@else
                            <td class="table-item-menu">&nbsp;</td>
@endif
                          </tr>
@endforeach
                        </tbody>
                      </table>
                      <nav @jxnPagination($rqPartialRefundPage)>
                      </nav>
                    </div> <!-- End table -->
