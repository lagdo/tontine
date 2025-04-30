                          <td>
                            {!! $debt->member !!}<br/>
                            {!! $debt->loan->session->title !!}
                          </td>
                          <td>
                            {!! __('meeting.refund.labels.loan', [
                              'member' => __('meeting.loan.labels.' . $debt->type),
                              'amount' => $locale->formatMoney($debtAmount, false, true),
                            ]) !!}<br/>
                            {!! $debt->loan->fund->title !!}
                          </td>
                          <td class="currency">
                            {{ __('meeting.refund.labels.before', [
                              'amount' => $locale->formatMoney($paidAmount, false, true),
                            ]) }}<br/>
                            {{ __('meeting.refund.labels.total', [
                              'amount' => $locale->formatMoney($totalPaidAmount, false, true),
                            ]) }}
                          </td>
                          <td class="currency">
                            {{ __('meeting.refund.labels.before', [
                              'amount' => $locale->formatMoney($amountDueBeforeSession, false, true),
                            ]) }}<br/>
                            {{ __('meeting.refund.labels.after', [
                              'amount' => $locale->formatMoney($amountDueAfterSession, false, true),
                            ]) }}
                          </td>
                          <td class="currency">
                            @if(!$debt->partial_refund)&nbsp;@else{{ $locale
                              ->formatMoney($debt->partial_refund->amount, false, true) }}@endif
                          </td>
                          <td class="table-item-menu">
                            <div data-debt-id="{{ $debt->id }}" @jxnTarget()>
                              {!! paymentLink($debt->refund, 'refund', !$debt->isEditable) !!}
                            </div>
                          </td>
