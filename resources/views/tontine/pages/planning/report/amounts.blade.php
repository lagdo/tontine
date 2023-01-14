          <div class="section-body">
            <div class="row align-items-center">
              <div class="col">
                <h2 class="section-title">{{ __('tontine.pool.titles.deposits') }} - {{ $pool->title }}</h2>
              </div>
              <div class="col-auto">
                <div class="input-group float-right ml-2">
                  {!! Form::select('pool_id', $pools, $pool->id, ['class' => 'form-control', 'id' => 'select-pool']) !!}
                  <div class="input-group-append">
                    <button type="button" class="btn btn-primary" id="btn-pool-select"><i class="fa fa-arrow-right"></i></button>
                  </div>
                </div>
              </div>
              <div class="col-auto">
                <div class="btn-group float-right" role="group" aria-label="">
                  <button type="button" class="btn btn-primary" id="btn-subscription-remitments"><i class="fa fa-user-check"></i></button>
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
                        <a href="javascript:void(0)" class="pool-session-toggle" data-session-id="{{ $session->id }}">
                          @if($session->disabled($pool))<i class="fa fa-toggle-off"></i>@else<i class="fa fa-toggle-on"></i>@endif
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
                      <td>{{ __('figures.remitment.titles.count') }}</td>
                      @foreach($sessions as $session)<td class="currency">{{ $figures->expected[$session->id]->remitment->count }}</td>@endforeach
                    </tr>
                    <tr>
                      <td>{{ __('figures.remitment.titles.amount') }}</td>
                      @foreach($sessions as $session)<td class="currency">{{ $figures->expected[$session->id]->remitment->amount }}</td>@endforeach
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
