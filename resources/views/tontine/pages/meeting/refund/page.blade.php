@inject('locale', 'Siak\Tontine\Service\LocaleService')
@inject('refundService', 'Siak\Tontine\Service\Meeting\Credit\RefundService')
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>{!! __('meeting.labels.member') !!}</th>
                      <th class="currency">{!! __('common.labels.amount') !!}</th>
                      <th class="table-item-menu">&nbsp;</th>
                    </tr>
                  </thead>
                  <tbody>
@foreach($debts as $debt)
                    <tr>
                      <td>
                        {{ $debt->loan->member->name }}<br/>
                        {{ $debt->loan->session->title }}
                      </td>
                      <td class="currency">
                        {{ $locale->formatMoney($refundService->getDebtAmount($session, $debt), true) }}<br/>
                        {{ !$debt->loan->remitment ? __('meeting.loan.labels.' . $debt->type) : __('meeting.remitment.labels.auction') }}
                      </td>
                      <td class="table-item-menu" data-debt-id="{{ $debt->id }}">
                        {!! paymentLink($debt->refund, 'refund', $session->closed) !!}
                      </td>
                    </tr>
@endforeach
                  </tbody>
                </table>
                <nav>{!! $pagination !!}</nav>
