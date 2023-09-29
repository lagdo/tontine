@inject('locale', 'Siak\Tontine\Service\LocaleService')
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.name') !!}</th>
                          <th class="currency">{!! __('common.labels.amount') !!}</th>
                          <th>{!! __('common.labels.paid') !!}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($payables as $payable)
                        <tr>
                          <td>{{ $payable->subscription->member->name ?? __('tontine.remitment.labels.not-assigned') }}</td>
                          <td class="currency">
                            {{ $locale->formatMoney($payable->amount, true) }}
@if ($payable->remitment && $payable->remitment->loan)
                            <br/>{{ __('meeting.remitment.labels.auction') }}: {{
                              $locale->formatMoney($payable->remitment->loan->interest_debt->amount) }}
@endif
                          </td>
                          <td class="table-item-menu" data-payable-id="{{ $payable->id }}">
@if (!$session->opened)
                            @if ($payable->remitment)<i class="fa fa-toggle-on"></i>@else<i class="fa fa-toggle-off">@endif
@elseif ($payable->remitment)
                            <a href="javascript:void(0)" class="btn-del-remitment"><i class="fa fa-toggle-on"></i></a>
@elseif ($payable->id > 0) {{-- Planned --}}
                            <a href="javascript:void(0)" class="btn-add-remitment"><i class="fa fa-toggle-off"></i></a>
@elseif ($pool->remit_planned)
                            <a href="javascript:void(0)" class="btn-new-remitment"><i class="fa fa-toggle-off"></i></a>
@else
                            <i class="fa fa-toggle-off"></i>
@endif
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>