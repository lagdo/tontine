                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.name') !!}</th>
                          <th>&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($members as $member)
                        <tr>
                          <td>{{ $member->name }}</td>
                          <td id="member-{{ $member->id }}" data-member-id="{{ $member->id }}" style="width:200px">
@if ($charge->has_amount)
@if ($session->closed)
                            @if ($member->bill !== null)<i class="fa fa-toggle-on"></i>@else<i class="fa fa-toggle-off">@endif
@elseif ($member->bill !== null)
                            <a href="javascript:void(0)" class="btn-del-bill"><i class="fa fa-toggle-on"></i></a>
@else
                            <a href="javascript:void(0)" class="btn-add-bill"><i class="fa fa-toggle-off"></i></a>
@endif
@else
@if ($session->closed)
                            @include('tontine.pages.meeting.charge.variable.member.closed', [
                              'amount' => !$member->bill ? '' : $member->bill->amount,
                            ])
@elseif (!$member->bill)
                            @include('tontine.pages.meeting.charge.variable.member.edit', [
                              'id' => $member->id,
                              'amount' => '',
                            ])
@else
                            @include('tontine.pages.meeting.charge.variable.member.show', [
                              'id' => $member->id,
                              'amount' => $member->bill->amount,
                            ])
@endif
@endif
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav>{!! $pagination !!}</nav>
