@inject('locale', 'Siak\Tontine\Service\LocaleService')
@inject('fundService', 'Siak\Tontine\Service\Tontine\FundService')
                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.loans') }}</div>
                    </div>
                    <div class="col-auto">
                      <div class="input-group mb-2">
                        <div class="input-group-prepend">
                          {!! $htmlBuilder->span('...')->id('loan_amount_available')
                            ->class('input-group-text')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                        </div>
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" id="btn-loan-balances"><i class="fa fa-caret-right"></i></button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('meeting.labels.member') !!}</th>
                          <th class="currency">{!! __('common.labels.amount') !!}</th>
                          <th class="table-item-menu">&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($loans as $loan)
                        <tr>
                          <td>{{ $loan->member->name }}<br/>{!! $fundService->getFundTitle($loan->fund) !!}</td>
                          <td class="currency">
                            {{ $locale->formatMoney($loan->principal, true) }}<br/>
                            {{ __('meeting.loan.interest.i' . $loan->interest_type) }}: {{ $loan->fixed_interest ?
                              $locale->formatMoney($loan->interest, true) : ($loan->interest_rate / 100) . '%' }}
                          </td>
                          <td class="table-item-menu">&nbsp;</td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
