@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row mt-0">
                    <div class="col d-flex justify-content-center">
                      <h5>{{ __('meeting.titles.disbursements') }}</h5>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{{ __('meeting.labels.category') }}</th>
                          <th>{{ __('meeting.labels.member') }}</th>
                          <th class="currency">{{ __('common.labels.amount') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($disbursements as $disbursement)
                        <tr>
                          <td>{{ $disbursement->category->name }}</td>
                          <td>{{ $disbursement->member ? $disbursement->member->name : '' }}</td>
                          <td class="currency">{{ $locale->formatMoney($disbursement->amount, true) }}</td>
                        </tr>
@endforeach
                        <tr>
                          <th>{{ __('common.labels.total') }}</th>
                          <th>{{ $total->total_count }}</th>
                          <th class="currency">{{ $locale->formatMoney($total->total_amount, true) }}</th>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
