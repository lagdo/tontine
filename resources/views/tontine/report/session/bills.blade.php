@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="pagebreak"></div>

                  <div class="row">
                    <div class="col d-flex justify-content-center flex-nowrap">
                      <div class="section-title mt-0">{{ __('meeting.charge.titles.fixed') }}</div>
                    </div>
                  </div>
@foreach($charges as $charge)
                  <div class="row">
                    <div class="col">
                      <h6>{{ $charge->name }}</h6>
                    </div>
                    <div class="col">
                      <h6>{{ $locale->formatMoney($charge->amount, true) }}</h6>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{{ __('meeting.labels.member') }}</th>
                          <th class="currency">{{ __('common.labels.amount') }}</th>
                          <th class="currency">{{ __('common.labels.paid') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($bills as $bill)
@if ($bill->charge_id === $charge->id)
                        <tr>
                          <td>{{ $bill->member->name }}@if (($bill->session)) - {{ $bill->session->title }}@endif</td>
                          <td class="currency">{{ $locale->formatMoney($bill->amount, true) }}</td>
                          <td class="currency">{{ $bill->paid ? __('common.labels.yes') : __('common.labels.no') }}</td>
                        </tr>
@endif
@endforeach
                        <tr>
                          <th>{{ __('common.labels.total') }}</th>
                          <th class="currency">{{ $locale->formatMoney($charge->total_amount, true) }}</th>
                          <th class="currency">{{ $charge->total_count }}</th>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
@endforeach
