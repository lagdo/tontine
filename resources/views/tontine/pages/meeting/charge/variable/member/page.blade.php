@inject('locale', 'Siak\Tontine\Service\LocaleService')
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.name') !!}</th>
@if ($charge->has_amount)
                          <th class="table-item-menu">&nbsp;</th>
@else
                          <th class="currency">&nbsp;</th>
@endif
                        </tr>
                      </thead>
                      <tbody>
@foreach ($members as $member)
                        <tr>
                          <td>{{ $member->name }}</td>
@if ($charge->has_amount)
                          <td class="table-item-menu" id="member-{{ $member->id }}" data-member-id="{{ $member->id }}">
@if ($session->closed)
                            @if ($member->bill !== null)<i class="fa fa-toggle-on"></i>@else<i class="fa fa-toggle-off">@endif
@elseif ($member->bill !== null)
                            <a href="javascript:void(0)" class="btn-del-bill"><i class="fa fa-toggle-on"></i></a>
@else
                            <a href="javascript:void(0)" class="btn-add-bill"><i class="fa fa-toggle-off"></i></a>
@endif
                          </td>
@else
                          <td class="currency" id="member-{{ $member->id }}" data-member-id="{{ $member->id }}" style="width:200px">
@if ($session->closed)
                            @include('tontine.pages.meeting.charge.variable.member.closed', [
                              'amount' => !$member->bill ? '' : $locale->formatMoney($member->bill->amount, true),
                            ])
@elseif (!$member->bill)
                            @include('tontine.pages.meeting.charge.variable.member.edit', [
                              'id' => $member->id,
                              'amount' => '',
                            ])
@else
                            @include('tontine.pages.meeting.charge.variable.member.show', [
                              'id' => $member->id,
                              'amount' => $locale->formatMoney($member->bill->amount, true),
                            ])
@endif
                          </td>
@endif
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav>{!! $pagination !!}</nav>
