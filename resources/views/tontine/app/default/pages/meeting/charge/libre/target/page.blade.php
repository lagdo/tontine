@inject('locale', 'Siak\Tontine\Service\LocaleService')
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
                          <th class="currency">{{ __('common.labels.paid') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($members as $member)
@php
  $paid = $member->paid ?? 0;
  $remaining = $target->amount > $paid ? $target->amount - $paid : 0;
@endphp
                        <tr>
                          <td>{{ $member->name }}@if ($remaining > 0)<br/>{{ __('meeting.target.labels.remaining',
                            ['amount' => $locale->formatMoney($remaining, true)]) }}@endif</td>
                          <td class="currency">{{ $locale->formatMoney($paid, true) }}</td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav>{!! $pagination !!}</nav>
