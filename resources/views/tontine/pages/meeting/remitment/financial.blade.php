@inject('locale', 'Siak\Tontine\Service\LocaleService')
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.title') !!}</th>
                          <th class="currency">{!! __('common.labels.amount') !!}</th>
                          <th>{!! __('common.labels.paid') !!}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($payables as $payable)
                        <tr>
                          <td>{{ $payable->subscription->member->name ?? __('tontine.remitment.labels.not-assigned') }}</td>
                          <td class="currency">{{ $locale->formatMoney($payable->amount, true) }}</td>
                          <td class="table-item-menu" data-payable-id="{{ $payable->id }}">
@if ($session->closed)
                            @if ($payable->id > 0)<i class="fa fa-toggle-on"></i>@else<i class="fa fa-toggle-off">@endif
@elseif ($payable->id > 0)
                            <a href="javascript:void(0)" class="btn-del-remitment"><i class="fa fa-toggle-on"></i></a>
@else
                            <a href="javascript:void(0)" class="btn-add-remitment"><i class="fa fa-toggle-off"></i></a>
@endif
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
