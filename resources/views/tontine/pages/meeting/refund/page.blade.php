@inject('locale', 'Siak\Tontine\Service\LocaleService')
@inject('refundService', 'Siak\Tontine\Service\Meeting\Credit\RefundService')
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>{!! __('meeting.labels.member') !!}</th>
                      <th class="currency">{!! __('common.labels.amount') !!}</th>
                      <th class="table-item-menu">{!! __('common.labels.paid') !!}</th>
                    </tr>
                  </thead>
                  <tbody>
@foreach($debts as $debt)
                    <tr>
                      <td>
                        {{ $debt->loan->member->name }}<br/>
                        {{ $debt->loan->session->title }}@if ($debt->refund) - {{ $debt->refund->session->title }}@endif
                      </td>
                      <td class="currency">
                        {{ $locale->formatMoney($refundService->getDebtAmount($session, $debt), true) }}<br/>
                        {{ __('meeting.loan.labels.' . $debt->type) }}
                      </td>
                      <td class="table-item-menu" data-debt-id="{{ $debt->id }}">
                        {!! paymentLink($debt->refund, 'refund', !$session->opened ||
                          ($debt->refund !== null && $debt->refund->session_id !== $session->id)) !!}
                      </td>
                    </tr>
@endforeach
                  </tbody>
                </table>
                <nav>{!! $pagination !!}</nav>
