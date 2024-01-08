@inject('locale', 'Siak\Tontine\Service\LocaleService')
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>
                            <div class="input-group">
                              {!! Form::text('search', $search, ['class' => 'form-control', 'id' => 'txt-fee-member-search']) !!}
                              <div class="input-group-append">
                                <button type="button" class="btn btn-primary" id="btn-saving-search"><i class="fa fa-search"></i></button>
                              </div>
                            </div>
                          </th>
                          <th class="currency">{{ $savingCount }}<br/>{{ $locale->formatMoney($savingSum, true) }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($members as $member)
                        <tr>
                          <td>{{ $member->name }}</td>
                          <td class="currency" id="saving-member-{{ $member->id }}" data-member-id="{{ $member->id }}" style="width:200px">
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
