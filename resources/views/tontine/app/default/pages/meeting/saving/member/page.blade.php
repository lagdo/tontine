@inject('locale', 'Siak\Tontine\Service\LocaleService')
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
                          <td class="currency" id="saving-member-{{ $member->id }}" data-member-id="{{ $member->id }}">
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
                    <nav>{!! $pagination !!}</nav>
