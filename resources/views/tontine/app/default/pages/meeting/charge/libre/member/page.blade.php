@inject('locale', 'Siak\Tontine\Service\LocaleService')
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
                    <nav></nav>
