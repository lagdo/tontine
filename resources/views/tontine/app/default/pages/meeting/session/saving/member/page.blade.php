@php
  $rqMemberPage = rq(Ajax\App\Meeting\Session\Saving\MemberPage::class);
  $rqAmount = rq(Ajax\App\Meeting\Session\Saving\Amount::class);
@endphp
                  <div class="table-responsive" id="content-session-saving-members" @jxnTarget()>
                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('meeting.labels.member') !!}</th>
                          <th class="currency">{{ __('common.labels.amount') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($members as $member)
@php
  $stash->set('meeting.saving.member', $member);
  $stash->set('meeting.saving', $member->saving);
@endphp
                        <tr>
                          <td>{{ $member->name }}</td>
                          <td class="currency amount" @jxnBind($rqAmount, $member->id)>
                            @jxnHtml($rqAmount)
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqMemberPage)>
                    </nav>
                  </div> <!-- End table -->
