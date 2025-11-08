                          <td>
                            <div>{!! $debt->member !!}</div>
                            <div>{!! $debt->loan->session->title !!}</div>
                          </td>
                          <td>
                            <div>{!! __('meeting.refund.labels.loan', [
                              'member' => __('meeting.loan.labels.' . $debt->type),
                              'amount' => $locale->formatMoney($debtAmount, false, true),
                            ]) !!}</div>
                            <div>{!! $debt->loan->fund->title !!}</div>
                          </td>
                          <td class="currency">
                            <div>{{ __('meeting.refund.labels.before', [
                              'amount' => $locale->formatMoney($paidAmount, false, true),
                            ]) }}</div>
                            <div>{{ __('meeting.refund.labels.total', [
                              'amount' => $locale->formatMoney($totalPaidAmount, false, true),
                            ]) }}</div>
                          </td>
                          <td class="currency">
                            <div>{{ __('meeting.refund.labels.before', [
                              'amount' => $locale->formatMoney($amountDueBeforeSession, false, true),
                            ]) }}</div>
                            <div>{{ __('meeting.refund.labels.after', [
                              'amount' => $locale->formatMoney($amountDueAfterSession, false, true),
                            ]) }}</div>
                          </td>
                          <td class="currency">
                            @if(!$debt->partial_refund)&nbsp;@else{{ $locale
                              ->formatMoney($debt->partial_refund->amount, false, true) }}@endif
                          </td>
                          <td class="table-item-menu">
                            <div data-debt-id="{{ $debt->id }}">
                              {!! paymentLink($debt->refund, 'refund', !$debt->isEditable) !!}
                            </div>
                          </td>
