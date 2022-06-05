          <div class="section-body">
            <div class="row align-items-center">
              <div class="col">
                <h2 class="section-title">{{ $fund->title }} - {{ __('figures.titles.amounts') }}</h2>
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
                  <button type="button" class="btn btn-primary" id="btn-meeting-table-print"><i class="fa fa-arrow-left"></i></button>
                  <button type="button" class="btn btn-primary" id="btn-meeting-table-refresh"><i class="fa fa-sync"></i></button>
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
                      <th></th>
@foreach($sessions as $session)
                      <th>
                        {{ $session->abbrev }}
                      </th>
@endforeach
                    </tr>
                  </thead>
                 <tbody>
@foreach ($subscriptions as $subscription)
                    <tr>
                      <td rowspan="2">{{ $subscription->member->name }}</td>
                        @foreach($sessions as $session)<td class="currency"><b>{{ $subscription->receivables[$session->id]->deposit ? $fund->money('amount', true) : ($session->opened ? 0 : '') }}</b></td>@endforeach
                    </tr>
                    <tr>
                      @foreach($sessions as $session)<td class="currency">{{ $session->disabled($fund) ? '' : $fund->money('amount', true) }}</td>@endforeach
                    </tr>
@endforeach
                    <tr>
                      <th colspan="{{ (count($sessions) + 1) }}">{{ __('figures.titles.amounts') }}</th>
                    </tr>
                    <tr>
                      <td rowspan="2">{{ __('figures.titles.start') }}</td>
                        @foreach($sessions as $session)<td class="currency"><b>{{ $figures->achieved[$session->id]->cashier->start }}</b></td>@endforeach
                    </tr>
                    <tr>
                      @foreach($sessions as $session)<td class="currency">{{ $figures->expected[$session->id]->cashier->start }}</td>@endforeach
                    </tr>
                    <tr>
                      <td rowspan="2">{{ __('figures.deposit.titles.count') }}</td>
                      @foreach($sessions as $session)<td class="currency"><b>{{ $figures->achieved[$session->id]->deposit->count }}</b></td>@endforeach
                    </tr>
                    <tr>
                      @foreach($sessions as $session)<td class="currency">{{ $figures->expected[$session->id]->deposit->count }}</td>@endforeach
                    </tr>
                    <tr>
                      <td rowspan="2">{{ __('figures.deposit.titles.amount') }}</td>
                      @foreach($sessions as $session)<td class="currency"><b>{{ $figures->achieved[$session->id]->deposit->amount }}</b></td>@endforeach
                    </tr>
                    <tr>
                      @foreach($sessions as $session)<td class="currency">{{ $figures->expected[$session->id]->deposit->amount }}</td>@endforeach
                    </tr>
                    <tr>
                      <td rowspan="2">{{ __('figures.titles.recv') }}</td>
                      @foreach($sessions as $session)<td class="currency"><b>{{ $figures->achieved[$session->id]->cashier->recv }}</b></td>@endforeach
                    </tr>
                    <tr>
                      @foreach($sessions as $session)<td class="currency">{{ $figures->expected[$session->id]->cashier->recv }}</td>@endforeach
                    </tr>
                    <tr>
                      <td rowspan="2">{{ __('figures.remittance.titles.count') }}</td>
                      @foreach($sessions as $session)<td class="currency"><b>{{ $figures->achieved[$session->id]->remittance->count }}</b></td>@endforeach
                    </tr>
                    <tr>
                      @foreach($sessions as $session)<td class="currency">{{ $figures->expected[$session->id]->remittance->count }}</td>@endforeach
                    </tr>
                    <tr>
                      <td rowspan="2">{{ __('figures.remittance.titles.amount') }}</td>
                      @foreach($sessions as $session)<td class="currency"><b>{{ $figures->achieved[$session->id]->remittance->amount }}</b></td>@endforeach
                    </tr>
                    <tr>
                      @foreach($sessions as $session)<td class="currency">{{ $figures->expected[$session->id]->remittance->amount }}</td>@endforeach
                    </tr>
                    <tr>
                      <td rowspan="2">{{ __('figures.titles.end') }}</td>
                      @foreach($sessions as $session)<td class="currency"><b>{{ $figures->achieved[$session->id]->cashier->end }}</b></td>@endforeach
                    </tr>
                    <tr>
                      @foreach($sessions as $session)<td class="currency">{{ $figures->expected[$session->id]->cashier->end }}</td>@endforeach
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
