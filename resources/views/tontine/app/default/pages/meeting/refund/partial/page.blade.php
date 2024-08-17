@inject('locale', 'Siak\Tontine\Service\LocaleService')
@inject('debtCalculator', 'Siak\Tontine\Service\Meeting\Credit\DebtCalculator')
                <table class="table table-bordered responsive">
                  <thead>
                    <tr>
                      <th>{!! __('meeting.labels.member') !!}</th>
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
                        {{ $locale->formatMoney($refund->amount, true) }}<br/>
                        {{ $locale->formatMoney($debtCalculator->getDebtDueAmount($refund->debt, $session, false), true) }}
                      </td>
                      <td class="table-item-menu" data-refund-id="{{ $refund->id }}">
                        {!! paymentLink($refund, 'partial-refund', !$session->opened) !!}
                      </td>
                    </tr>
@endforeach
                  </tbody>
                </table>
                <nav>{!! $pagination !!}</nav>
