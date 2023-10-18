@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('tontine.report.titles.bills.session') }}</div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{{ __('common.labels.title') }}</th>
                          <th class="currency">{{ __('common.labels.amount') }}</th>
                          <th class="table-item-menu">{{ __('common.labels.paid') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach($bills as $bill)
                        <tr>
                          <td>{{ $bill->charge }}@if (($bill->session))<br/>{{ $bill->session->title }}@endif</td>
                          <td class="currency">{{ $locale->formatMoney($bill->amount, true) }}</td>
                          <td class="table-item-menu"><i class="fa fa-toggle-{{ $bill->paid ? 'on' : 'off' }}"></i></td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
