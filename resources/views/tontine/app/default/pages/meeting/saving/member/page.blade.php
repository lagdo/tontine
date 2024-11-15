@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $memberId = Jaxon\jq()->parent()->attr('data-member-id')->toInt();
  $amount = Jaxon\jq('input', Jaxon\jq()->parent()->parent())->val()->toInt();
  $rqMember = Jaxon\rq(Ajax\App\Meeting\Session\Saving\Member::class);
  $rqMemberPage = Jaxon\rq(Ajax\App\Meeting\Session\Saving\MemberPage::class);
@endphp
                  <div class="table-responsive" id="meeting-saving-members" @jxnTarget()>
                    <div @jxnOn(['.btn-save-saving', 'click', ''], $rqMember->saveSaving($memberId, $amount))></div>
                    <div @jxnOn(['.btn-edit-saving', 'click', ''], $rqMember->editSaving($memberId))></div>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('meeting.labels.member') !!}</th>
                          <th class="currency">{{ __('common.labels.amount') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($members as $member)
                        <tr>
                          <td>{{ $member->name }}</td>
                          <td class="currency" id="saving-member-{{ $member->id }}" data-member-id="{{ $member->id }}" style="width:200px;">
@if ($session->closed)
                            @include('tontine.app.default.pages.meeting.saving.member.closed', [
                              'amount' => !$member->saving ? '' : $locale->formatMoney($member->saving->amount, true),
                            ])
@elseif (!$member->saving)
                            @include('tontine.app.default.pages.meeting.saving.member.edit', [
                              'memberId' => $member->id,
                              'amount' => '',
                            ])
@else
                            @include('tontine.app.default.pages.meeting.saving.member.show', [
                              'memberId' => $member->id,
                              'amount' => $locale->formatMoney($member->saving->amount, false),
                            ])
@endif
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqMemberPage)>
                    </nav>
                  </div> <!-- End table -->
