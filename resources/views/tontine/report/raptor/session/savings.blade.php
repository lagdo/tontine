@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="section-title">
                    {!! __('meeting.titles.savings') !!}
                  </div>
                  <div class="table">
                    <table>
                      <thead>
                        <tr>
                          <th>{{ __('meeting.labels.member') }}</th>
                          <th style="width:30%;">{{ __('tontine.fund.labels.fund') }}</th>
                          <th style="width:20%;text-align:right;">{{ __('common.labels.amount') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($savings as $saving)
                        <tr>
                          <td>{{ $saving->member->name }}</td>
                          <td>{!! $saving->fund ? $saving->fund->title : __('tontine.fund.labels.default') !!}</td>
                          <td style="text-align:right;">{{ $locale->formatMoney($saving->amount, true) }}</td>
                        </tr>
@endforeach
                        <tr class="total">
                          <td>{{ __('common.labels.total') }}</td>
                          <td style="width:30%;">{{ $total->total_count }}</td>
                          <td style="width:20%;">{{ $locale->formatMoney($total->total_amount, true) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
