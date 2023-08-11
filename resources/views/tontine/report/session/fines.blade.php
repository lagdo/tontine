@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row">
                    <div class="col d-flex justify-content-center flex-nowrap">
                      <div class="section-title mt-0">{{ __('meeting.charge.titles.variable') }}</div>
                    </div>
                  </div>
@foreach($fines as $fine)
                  <div class="row">
                    <div class="col">
                      <h6>{{ $fine->name }}</h6>
                    </div>
                    <div class="col">
                      <h6>{{ $fine->has_amount ? $locale->formatMoney($fine->amount, true) : __('tontine.labels.fees.variable') }}</h6>
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
@if ($bill->charge_id === $fine->id && $bill->session->id === $session->id) {{-- Created on this session --}}
                        <tr>
                          <td>{{ $bill->member->name }}</td>
                          <td class="currency">{{ $locale->formatMoney($bill->amount, true) }}</td>
                          <td class="currency">{{ $bill->paid ? __('common.labels.yes') : __('common.labels.no') }}</td>
                        </tr>
@endif
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->

                  <div class="row">
                    <div class="col">
                      <h6>{{ $fine->name }} - {{ __('meeting.labels.payments') }}</h6>
                    </div>
                    <div class="col">
                      <h6>{{ $fine->has_amount ? $locale->formatMoney($fine->amount, true) : __('tontine.labels.fees.variable') }}</h6>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{{ __('meeting.labels.member') }}</th>
                          <th>{{ __('meeting.labels.session') }}</th>
                          <th class="currency">{{ __('common.labels.amount') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($bills as $bill)
@if ($bill->charge_id === $fine->id && $bill->paid) {{-- Paid on this session --}}
                        <tr>
                          <td>{{ $bill->member->name }}</td>
                          <td>{{ $bill->session->title }}</td>
                          <td class="currency">{{ $locale->formatMoney($bill->amount, true) }}</td>
                        </tr>
@endif
@endforeach
                        <tr>
                          <th>{{ __('common.labels.total') }}</th>
                          <th>&nbsp;</th>
                          <th class="currency">{{ $locale->formatMoney($fine->total_amount, true) }}</th>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
@endforeach
