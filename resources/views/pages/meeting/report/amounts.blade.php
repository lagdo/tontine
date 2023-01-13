          <div class="section-body">
            <div class="row align-items-center">
              <div class="col">
                <h2 class="section-title">{{ $pool->title }} - {{ __('figures.titles.amounts') }}</h2>
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
                  <a type="button" class="btn btn-primary" target="_blank" href="{{ route('report.pool', ['poolId' => $pool->id]) }}"><i class="fa fa-file-pdf"></i></a>
                  <button type="button" class="btn btn-primary" id="btn-meeting-table-deposits"><i class="fa fa-user-times"></i></button>
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
                    <tr>
                      <td rowspan="2">{{ __('figures.titles.start') }}</td>
                        @foreach($sessions as $session)<td class="currency"><b>{!! $figures->achieved[$session->id]->cashier->start !!}</b></td>@endforeach
                    </tr>
                    <tr>
                      @foreach($sessions as $session)<td class="currency">{{ $figures->expected[$session->id]->cashier->start }}</td>@endforeach
                    </tr>
                    <tr>
                      <td rowspan="2">{{ __('figures.deposit.titles.count') }}</td>
                      @foreach($sessions as $session)<td class="currency"><b>{!! $figures->achieved[$session->id]->deposit->count !!}</b></td>@endforeach
                    </tr>
                    <tr>
                      @foreach($sessions as $session)<td class="currency">{{ $figures->expected[$session->id]->deposit->count }}</td>@endforeach
                    </tr>
                    <tr>
                      <td rowspan="2">{{ __('figures.deposit.titles.amount') }}</td>
                      @foreach($sessions as $session)<td class="currency"><b>{!! $figures->achieved[$session->id]->deposit->amount !!}</b></td>@endforeach
                    </tr>
                    <tr>
                      @foreach($sessions as $session)<td class="currency">{{ $figures->expected[$session->id]->deposit->amount }}</td>@endforeach
                    </tr>
                    <tr>
                      <td rowspan="2">{{ __('figures.titles.recv') }}</td>
                      @foreach($sessions as $session)<td class="currency"><b>{!! $figures->achieved[$session->id]->cashier->recv !!}</b></td>@endforeach
                    </tr>
                    <tr>
                      @foreach($sessions as $session)<td class="currency">{{ $figures->expected[$session->id]->cashier->recv }}</td>@endforeach
                    </tr>
                    <tr>
                      <td rowspan="2">{{ __('figures.remitment.titles.count') }}</td>
                      @foreach($sessions as $session)<td class="currency"><b>{!! $figures->achieved[$session->id]->remitment->count !!}</b></td>@endforeach
                    </tr>
                    <tr>
                      @foreach($sessions as $session)<td class="currency">{{ $figures->expected[$session->id]->remitment->count }}</td>@endforeach
                    </tr>
                    <tr>
                      <td rowspan="2">{{ __('figures.remitment.titles.amount') }}</td>
                      @foreach($sessions as $session)<td class="currency"><b>{!! $figures->achieved[$session->id]->remitment->amount !!}</b></td>@endforeach
                    </tr>
                    <tr>
                      @foreach($sessions as $session)<td class="currency">{{ $figures->expected[$session->id]->remitment->amount }}</td>@endforeach
                    </tr>
                    <tr>
                      <td rowspan="2">{{ __('figures.titles.end') }}</td>
                      @foreach($sessions as $session)<td class="currency"><b>{!! $figures->achieved[$session->id]->cashier->end !!}</b></td>@endforeach
                    </tr>
                    <tr>
                      @foreach($sessions as $session)<td class="currency">{{ $figures->expected[$session->id]->cashier->end }}</td>@endforeach
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
