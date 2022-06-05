          <div class="section-body">
            <div class="row align-items-center">
              <div class="col">
                <h2 class="section-title">{{ __('tontine.fund.titles.remittances') }} - {{ $fund->title }}</h2>
              </div>
              <div class="col-auto">
                <div class="input-group float-right ml-2">
                  {!! Form::select('fund_id', $funds, $fund->id, ['class' => 'form-control', 'id' => 'select-fund']) !!}
                  <div class="input-group-append">
                    <button type="button" class="btn btn-primary" id="btn-fund-select"><i class="fa fa-arrow-right"></i></button>
                  </div>
                </div>
              </div>
              <div class="col-auto">
                <div class="btn-group float-right" role="group" aria-label="">
                  <button type="button" class="btn btn-primary" id="btn-subscription-refresh"><i class="fa fa-sync"></i></button>
                  <button type="button" class="btn btn-primary" id="btn-subscription-deposits"><i class="fa fa-calendar-plus"></i></button>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow mb-4">
            <div class="card-body" id="content-page">
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>{{ __('figures.titles.sessions') }}</th>
                      <th class="currency">{{ __('figures.titles.amount') }}</th>
                      <th class="currency">{{ __('figures.remittance.titles.count') }}</th>
                      <th class="currency">{{ __('figures.remittance.titles.amount') }}</th>
                      <th>{{ __('figures.titles.beneficiaries') }}</th>
                    </tr>
                  </thead>
                  <tbody>
@foreach ($sessions as $session)
@if ($session->disabled($fund))
                    <tr>
                      <td>{{ $session->title }}</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
@else
                    <tr>
                      <td>{{ $session->title }}</td>
                      <td class="currency">{{ $figures->expected[$session->id]->cashier->recv }}</td>
                      <td class="currency">{{ $figures->expected[$session->id]->remittance->count }}</td>
                      <td class="currency">{{ $figures->expected[$session->id]->remittance->amount }}</td>
                      <td>
@foreach ($session->beneficiaries as $subscription)
                        {!! Form::select('', $subscription === 0 ? $subscriptions :
                            collect($subscriptions->all())->put($subscription, $beneficiaries[$subscription] ?? 'Not found'), $subscription, [
                                'class' => 'form-control my-2 select-beneficiary',
                                'data-session-id' => $session->id,
                                'data-subscription-id' => $subscription,
                            ]) !!}
@endforeach
                      </td>
                    </tr>
@endif
@endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
