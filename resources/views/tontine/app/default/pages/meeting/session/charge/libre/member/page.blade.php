@php
  $memberId = jq()->parent()->attr('data-member-id')->toInt();
  $paid = je('check-fee-libre-paid')->rd()->checked();
  $rqMemberFunc = rq(Ajax\App\Meeting\Session\Charge\Libre\MemberFunc::class);
  $rqMemberPage = rq(Ajax\App\Meeting\Session\Charge\Libre\MemberPage::class);
  $rqMemberName = rq(Ajax\App\Meeting\Session\Charge\Libre\MemberName::class);
  $rqAmount = rq(Ajax\App\Meeting\Session\Charge\Libre\Amount::class);
@endphp
                  <div class="table-responsive" id="content-session-fee-libre-members" @jxnEvent([
                    ['.btn-add-bill', 'click', $rqMemberFunc->addBill($memberId, $paid)],
                    ['.btn-del-bill', 'click', $rqMemberFunc->delBill($memberId)]])>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{{ __('common.labels.name') }}</th>
                          <th class="currency">&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($members as $member)
@php
  $stash->set('meeting.charge.member', $member);
  $stash->set('meeting.charge.bill', $member->bill);
@endphp
                        <tr>
                          <td @jxnBind($rqMemberName, $member->id)>@jxnHtml($rqMemberName)</td>
@if ($charge->has_amount)
                          <td class="currency" id="member-{{ $member->id }}" data-member-id="{{ $member->id }}">
@if (!$session->opened)
                            @if ($member->bill !== null)<i class="fa fa-toggle-on"></i>@else<i class="fa fa-toggle-off">@endif
@elseif ($member->bill !== null)
                            <a role="link" tabindex="0" class="btn-del-bill"><i class="fa fa-toggle-on"></i></a>
@else
                            <a role="link" tabindex="0" class="btn-add-bill"><i class="fa fa-toggle-off"></i></a>
@endif
                          </td>
@else
                          <td class="currency amount" @jxnBind($rqAmount, $member->id)>@jxnHtml($rqAmount)</td>
@endif
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqMemberPage)>
                    </nav>
                  </div> <!-- End table -->
