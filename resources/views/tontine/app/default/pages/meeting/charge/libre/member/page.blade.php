@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $memberId = jq()->parent()->attr('data-member-id')->toInt();
  $paid = pm()->checked('check-fee-libre-paid');
  $rqMember = rq(Ajax\App\Meeting\Session\Charge\Libre\Member::class);
  $rqMemberPage = rq(Ajax\App\Meeting\Session\Charge\Libre\MemberPage::class);
  $rqAmount = rq(Ajax\App\Meeting\Session\Charge\Libre\Amount::class);
@endphp
                  <div class="table-responsive" id="content-session-fee-libre-members" @jxnTarget()>
                    <div @jxnEvent(['.btn-add-bill', 'click'], $rqMember->addBill($memberId, $paid))></div>
                    <div @jxnEvent(['.btn-del-bill', 'click'], $rqMember->delBill($memberId))></div>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{{ __('common.labels.name') }}</th>
                          <th class="currency">&nbsp;</th>
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
                            <a role="link" tabindex="0" class="btn-del-bill"><i class="fa fa-toggle-on"></i></a>
@else
                            <a role="link" tabindex="0" class="btn-add-bill"><i class="fa fa-toggle-off"></i></a>
@endif
                          </td>
@else
@php
  $stash->set('meeting.charge.member.id', $member->id);
  $stash->set('meeting.charge.bill', $member->bill);
@endphp
                          <td class="currency" @jxnBind($rqAmount, $member->id) style="width:200px">
                            @jxnHtml($rqAmount)
                          </td>
@endif
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqMemberPage)>
                    </nav>
                  </div> <!-- End table -->
