@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $rqMemberPage = Jaxon\rq(Ajax\App\Meeting\Session\Saving\MemberPage::class);
  $rqAmount = Jaxon\rq(Ajax\App\Meeting\Session\Saving\Amount::class);
@endphp
                  <div class="table-responsive" id="meeting-saving-members" @jxnTarget()>
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
                          <td class="currency" @jxnBind($rqAmount, $member->id) style="width:200px;">
@if ($session->closed)
                            @include('tontine.app.default.pages.meeting.saving.member.closed', [
                              'amount' => !$member->saving ? '' : $locale->formatMoney($member->saving->amount, true),
                            ])
@elseif (!$member->saving)
                            @include('tontine.app.default.pages.meeting.saving.member.edit', [
                              'memberId' => $member->id,
                              'amount' => '',
                              'rqAmount' => $rqAmount,
                            ])
@else
                            @include('tontine.app.default.pages.meeting.saving.member.show', [
                              'memberId' => $member->id,
                              'amount' => $locale->formatMoney($member->saving->amount, false),
                              'rqAmount' => $rqAmount,
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
