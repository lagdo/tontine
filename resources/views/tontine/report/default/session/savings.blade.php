@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row mt-0">
                    <div class="col d-flex justify-content-center">
                      <h5>{!! __('meeting.titles.savings') !!}</h5>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
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
                        <tr>
                          <th>{{ __('common.labels.total') }}</th>
                          <th style="width:30%;text-align:right;">{{ $total->total_count }}</th>
                          <th style="width:20%;text-align:right;">{{ $locale->formatMoney($total->total_amount, true) }}</th>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
