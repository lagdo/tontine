@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $memberId = Jaxon\jq()->parent()->attr('data-member-id')->toInt();
  $paid = Jaxon\pm()->checked('check-fee-libre-paid');
  $amount = Jaxon\jq('input', jq()->parent()->parent())->val()->toInt();
  $rqMember = Jaxon\rq(App\Ajax\Web\Meeting\Session\Charge\Libre\Member::class);
@endphp
                  <div class="table-responsive" id="meeting-fee-libre-members" @jxnTarget()>
                    <div @jxnOn(['.btn-add-bill', 'click', ''], $rqMember->addBill($memberId, $paid))></div>
                    <div @jxnOn(['.btn-del-bill', 'click', ''], $rqMember->delBill($memberId))></div>
                    <div @jxnOn(['.btn-save-bill', 'click', ''], $rqMember->addBill($memberId, $paid, $amount))></div>
                    <div @jxnOn(['.btn-edit-bill', 'click', ''], $rqMember->editBill($memberId))></div>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{{ __('common.labels.name') }}</th>
                          <th class="currency">{{ __('common.labels.paid') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($members as $member)
                        <tr>
                          <td>{{ $member->name }}@if ($member->remaining > 0)<br/>{{ __('meeting.target.labels.remaining',
                            ['amount' => $locale->formatMoney($member->remaining, true)]) }}@endif</td>
@if ($charge->has_amount)
                          <td class="currency" id="member-{{ $member->id }}" data-member-id="{{ $member->id }}">
@if (!$session->opened || !$charge->is_active)
                            @if ($member->bill !== null)<i class="fa fa-toggle-on"></i>@else<i class="fa fa-toggle-off">@endif
@elseif ($member->bill !== null)
                            <a role="link" class="btn-del-bill"><i class="fa fa-toggle-on"></i></a>
@else
                            <a role="link" class="btn-add-bill"><i class="fa fa-toggle-off"></i></a>
@endif
                          </td>
@else
                          <td class="currency" id="member-{{ $member->id }}" data-member-id="{{ $member->id }}" style="width:200px">
@if (!$session->opened || !$charge->is_active)
                            @include('tontine.app.default.pages.meeting.charge.libre.member.closed', [
                              'amount' => !$member->bill ? '' : $locale->formatMoney($member->bill->amount, true),
                            ])
@elseif (!$member->bill)
                            @include('tontine.app.default.pages.meeting.charge.libre.member.edit', [
                              'id' => $member->id,
                              'amount' => '',
                            ])
@else
                            @include('tontine.app.default.pages.meeting.charge.libre.member.show', [
                              'id' => $member->id,
                              'amount' => $locale->formatMoney($member->bill->amount, false),
                            ])
@endif
                          </td>
@endif
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
