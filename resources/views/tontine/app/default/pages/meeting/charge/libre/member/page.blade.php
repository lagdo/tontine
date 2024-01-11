@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $settlementCount = $settlement->total ?? 0;
  $settlementAmount = $settlement->amount ?? 0;
@endphp
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>
                            <div class="input-group">
                              {!! Form::text('search', $search, ['class' => 'form-control', 'id' => 'txt-fee-member-search']) !!}
                              <div class="input-group-append">
                                <button type="button" class="btn btn-primary" id="btn-fee-libre-search"><i class="fa fa-search"></i></button>
                              </div>
                            </div>
                          </th>
                          <th class="currency">@if ($settlementCount > 0) {{ $settlementCount }}<br />{!!
                            $locale->formatMoney($settlementAmount, true) !!}@else &nbsp;@endif</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($members as $member)
                        <tr>
                          <td>{{ $member->name }}@if ($member->remaining > 0)<br/>{{ __('meeting.target.labels.remaining',
                            ['amount' => $locale->formatMoney($member->remaining, true)]) }}@endif</td>
@if ($charge->has_amount)
                          <td class="currency" id="member-{{ $member->id }}" data-member-id="{{ $member->id }}" style="width:180px">
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
                    <nav>{!! $pagination !!}</nav>
