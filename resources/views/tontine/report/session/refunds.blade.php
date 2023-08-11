@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row">
                    <div class="col d-flex justify-content-center flex-nowrap">
                      <div class="section-title mt-0">{{ __('meeting.titles.refunds') }}</div>
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
@foreach ($debts as $debt)
                        <tr>
                          <td>{{ $debt->member->name }}</td>
                          <td>{{ $debt->session->title }} - {{ __('meeting.loan.labels.' . $debt->type_str) }}</td>
                          <td class="currency">{{ $locale->formatMoney($debt->amount, true) }}</td>
                        </tr>
@endforeach
                        <tr>
                          <th>{{ __('common.labels.total') }}</th>
                          <th>{{ __('meeting.loan.labels.principal') }}</th>
                          <th class="currency">{{ $locale->formatMoney($total->principal, true) }}</th>
                        </tr>
                        <tr>
                          <th>{{ __('common.labels.total') }}</th>
                          <th>{{ __('meeting.loan.labels.interest') }}</th>
                          <th class="currency">{{ $locale->formatMoney($total->interest, true) }}</th>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
