          <div class="section-body">
            <div class="row align-items-center">
              <div class="col">
                <h2 class="section-title">{{ __('tontine.fund.titles.deposits') }} - {{ $fund->title }}</h2>
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
                  <button type="button" class="btn btn-primary" id="btn-subscription-deposits"><i class="fa fa-user-times"></i></button>
                  <button type="button" class="btn btn-primary" id="btn-subscription-remittances"><i class="fa fa-user-check"></i></button>
                  <button type="button" class="btn btn-primary" id="btn-subscription-refresh"><i class="fa fa-sync"></i></button>
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
                        <a href="javascript:void(0)" class="fund-session-toggle" data-session-id="{{ $session->id }}">
                          @if($session->disabled($fund))<i class="fa fa-toggle-off"></i>@else<i class="fa fa-toggle-on"></i>@endif
                        </a>
                      </th>
@endforeach
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>{{ __('figures.titles.start') }}</td>
                      @foreach($sessions as $session)<td class="currency">{{ $figures->expected[$session->id]->cashier->start }}</td>@endforeach
                    </tr>
                    <tr>
                      <td>{{ __('figures.deposit.titles.count') }}</td>
                      @foreach($sessions as $session)<td class="currency">{{ $figures->expected[$session->id]->deposit->count }}</td>@endforeach
                    </tr>
                    <tr>
                      <td>{{ __('figures.deposit.titles.amount') }}</td>
                      @foreach($sessions as $session)<td class="currency">{{ $figures->expected[$session->id]->deposit->amount }}</td>@endforeach
                    </tr>
                    <tr>
                      <td>{{ __('figures.titles.recv') }}</td>
                      @foreach($sessions as $session)<td class="currency">{{ $figures->expected[$session->id]->cashier->recv }}</td>@endforeach
                    </tr>
                    <tr>
                      <td>{{ __('figures.remittance.titles.count') }}</td>
                      @foreach($sessions as $session)<td class="currency">{{ $figures->expected[$session->id]->remittance->count }}</td>@endforeach
                    </tr>
                    <tr>
                      <td>{{ __('figures.remittance.titles.amount') }}</td>
                      @foreach($sessions as $session)<td class="currency">{{ $figures->expected[$session->id]->remittance->amount }}</td>@endforeach
                    </tr>
                    <tr>
                      <td>{{ __('figures.titles.end') }}</td>
                      @foreach($sessions as $session)<td class="currency">{{ $figures->expected[$session->id]->cashier->end }}</td>@endforeach
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
