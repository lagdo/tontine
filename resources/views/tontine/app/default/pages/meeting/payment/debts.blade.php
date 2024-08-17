@inject('locale', 'Siak\Tontine\Service\LocaleService')
@inject('debtCalculator', 'Siak\Tontine\Service\Meeting\Credit\DebtCalculator')
                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.refunds') }}</div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{{ __('common.labels.title') }}</th>
                          <th class="currency">{{ __('common.labels.amount') }}</th>
                          <th class="table-item-menu">{{ __('common.labels.paid') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach($debts as $debt)
@php
  $debtAmount = $debtCalculator->getDebtDueAmount($debt, $session, true);
@endphp
                        <tr>
                          <td>{{ __('meeting.loan.labels.' . $debt->type_str) }}<br/>{{ $debt->session->title }}</td>
                          <td class="currency">{{ $locale->formatMoney($debtAmount, true) }}</td>
                          <td class="table-item-menu"><i class="fa fa-toggle-{{ $debt->paid ? 'on' : 'off' }}"></i></td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
