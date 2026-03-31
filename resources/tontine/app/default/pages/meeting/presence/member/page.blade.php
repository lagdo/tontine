@php
  $memberId = jq()->parent()->attr('data-member-id')->toInt();
  $rqPresenceFunc = rq(Ajax\App\Meeting\Presence\PresenceFunc::class);
  $rqMemberFunc = rq(Ajax\App\Meeting\Presence\MemberFunc::class);
  $rqMemberPage = rq(Ajax\App\Meeting\Presence\MemberPage::class);
@endphp
                  <div class="table-responsive" id="content-presence-members" @jxnEvent([
                    ['.btn-toggle-member-presence', 'click', $rqMemberFunc->togglePresence($memberId)],
                    ['.btn-show-member-presences', 'click', $rqPresenceFunc->selectMember($memberId)]])>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{{ __('common.labels.name') }}</th>
                          <th class="table-item-toggle"></th>
                          <th class="table-item-menu"></th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($members as $member)
                        <tr>
                          <td>{{ $member->name }}</td>
                          <td class="table-item-toggle">{{ $sessionCount - ($member->absences_count ?? 0) }}/{{ $sessionCount }}</td>
                          <td class="table-item-menu" data-member-id="{{ $member->id }}">
@if (!$session)
                            <button type="button" class="btn btn-primary btn-show-member-presences"><i class="fa fa-arrow-circle-right"></i></button>
@elseif ($session->opened)
                            <a role="link" tabindex="0" class="btn-toggle-member-presence"><i class="fa fa-toggle-{{
                              $absences->has($member->id) ? 'off' : 'on' }}"></i></a>
@else
                            <i class="fa fa-toggle-{{ $absences->has($member->id) ? 'off' : 'on' }}"></i>
@endif
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqMemberPage)>
                    </nav>
                  </div> <!-- End table -->
