@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $payableId = jq()->parent()->attr('data-payable-id')->toInt();
  $rqPayable = rq(Ajax\App\Meeting\Session\Pool\Remitment\Payable::class);
@endphp
                  <div class="table-responsive" id="content-session-pool-remitments" @jxnTarget()>
                    <div @jxnEvent(['.btn-add-remitment', 'click'], $rqPayable->addRemitment($payableId))></div>
                    <div @jxnEvent(['.btn-save-remitment', 'click'], $rqPayable->createRemitment($payableId))></div>
                    <div @jxnEvent(['.btn-del-remitment', 'click'], $rqPayable->deleteRemitment($payableId)
                      ->confirm(__('meeting.remitment.questions.delete')))></div>

                    <table class="table table-bordered responsive">
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
                          <td>{{ $payable->member }}</td>
                          <td class="currency">
                            {{ $locale->formatMoney($payable->amount, true) }}
@if ($payable->remitment && $payable->remitment->auction)
                            <br/>{{ __('meeting.remitment.labels.auction') }}: {{
                              $locale->formatMoney($payable->remitment->auction->amount) }}
@endif
                          </td>
                          <td class="table-item-menu" data-payable-id="{{ $payable->id }}">
@if (!$session->opened)
                            @if ($payable->remitment)<i class="fa fa-toggle-on"></i>@else<i class="fa fa-toggle-off">@endif
@elseif ($payable->remitment)
                            <a role="link" tabindex="0" class="btn-del-remitment"><i class="fa fa-toggle-on"></i></a>
@elseif ($payable->id > 0)
                            <a role="link" tabindex="0" class="btn-save-remitment"><i class="fa fa-toggle-off"></i></a>
@else
                            <a role="link" tabindex="0" class="btn-add-remitment"><i class="fa fa-toggle-off"></i></a>
@endif
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
