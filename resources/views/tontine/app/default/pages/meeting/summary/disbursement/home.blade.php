@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.disbursements') }}</div>
                    </div>
                    <div class="col-auto">
                      <div class="input-group mb-2">
                        <div class="input-group-prepend">
                          {!! $htmlBuilder->span('...')->id('total_amount_available')
                            ->class('input-group-text')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                        </div>
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" id="btn-disbursement-balances"><i class="fa fa-caret-right"></i></button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('meeting.labels.category') !!}</th>
                          <th>{!! __('meeting.labels.member') !!}</th>
                          <th>{!! __('meeting.labels.charge') !!}</th>
                          <th class="currency">{!! __('common.labels.amount') !!}</th>
                          <th class="table-item-menu">&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($disbursements as $disbursement)
                        <tr>
                          <td>{{ $disbursement->category->name }}@if (($disbursement->comment)) <br/>{{
                            $disbursement->comment }}@endif</td>
                          <td>@if (($disbursement->member)) {{ $disbursement->member->name }}@endif</td>
                          <td>@if (($disbursement->charge)) {{ $disbursement->charge->name }}@endif</td>
                          <td class="currency">{{ $locale->formatMoney($disbursement->amount, true) }}</td>
                          <td class="table-item-menu">&nbsp;</td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
