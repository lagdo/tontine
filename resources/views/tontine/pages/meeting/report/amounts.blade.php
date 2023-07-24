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
                <div class="btn-group float-right ml-2" role="group" aria-label="">
                  <button type="button" class="btn btn-primary" id="btn-meeting-report-refresh"><i class="fa fa-sync"></i></button>
                  <a type="button" class="btn btn-primary" target="_blank" href="{{ route('report.pool', ['poolId' => $pool->id]) }}"><i class="fa fa-file-pdf"></i></a>
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
@if ($tontine->is_libre)
                      <th>{{ __('figures.titles.start') }}</th>
                      <th>{{ __('figures.deposit.titles.count') }}</th>
                      <th>{{ __('figures.deposit.titles.amount') }}</th>
                      <th>{{ __('figures.titles.recv') }}</th>
                      <th>{{ __('figures.remitment.titles.count') }}</th>
                      <th>{{ __('figures.remitment.titles.amount') }}</th>
                      <th>{{ __('figures.titles.end') }}</th>
@else
                      <th @if(!$tontine->is_libre)colspan="2"@endif>{{ __('figures.titles.start') }}</th>
                      <th @if(!$tontine->is_libre)colspan="2"@endif>{{ __('figures.deposit.titles.count') }}</th>
                      <th @if(!$tontine->is_libre)colspan="2"@endif>{{ __('figures.deposit.titles.amount') }}</th>
                      <th @if(!$tontine->is_libre)colspan="2"@endif>{{ __('figures.titles.recv') }}</th>
                      <th @if(!$tontine->is_libre)colspan="2"@endif>{{ __('figures.remitment.titles.count') }}</th>
                      <th @if(!$tontine->is_libre)colspan="2"@endif>{{ __('figures.remitment.titles.amount') }}</th>
                      <th @if(!$tontine->is_libre)colspan="2"@endif>{{ __('figures.titles.end') }}</th>
@endif
                    </tr>
                  </thead>
                  <tbody>
@if ($tontine->is_libre)
@foreach ($sessions as $session)
                    <tr>
                      <th>{{ $session->title }}</th>
                      <td class="currency"><b>{!! $figures->collected[$session->id]->cashier->start !!}</b></td>
                      <td class="currency"><b>{!! $figures->collected[$session->id]->deposit->count !!}</b></td>
                      <td class="currency"><b>{!! $figures->collected[$session->id]->deposit->amount !!}</b></td>
                      <td class="currency"><b>{!! $figures->collected[$session->id]->cashier->recv !!}</b></td>
                      <td class="currency"><b>{!! $figures->collected[$session->id]->remitment->count !!}</b></td>
                      <td class="currency"><b>{!! $figures->collected[$session->id]->remitment->amount !!}</b></td>
                      <td class="currency"><b>{!! $figures->collected[$session->id]->cashier->end !!}</b></td>
                    </tr>
@endforeach
@else
@foreach ($sessions as $session)
                    <tr>
                      <th>{{ $session->title }}</th>
                      <td class="currency">{{ $figures->expected[$session->id]->cashier->start }}</td>
                      <td class="currency"><b>{!! $figures->collected[$session->id]->cashier->start !!}</b></td>
                      <td class="currency">{{ $figures->expected[$session->id]->deposit->count }}</td>
                      <td class="currency"><b>{!! $figures->collected[$session->id]->deposit->count !!}</b></td>
                      <td class="currency">{{ $figures->expected[$session->id]->deposit->amount }}</td>
                      <td class="currency"><b>{!! $figures->collected[$session->id]->deposit->amount !!}</b></td>
                      <td class="currency">{{ $figures->expected[$session->id]->cashier->recv }}</td>
                      <td class="currency"><b>{!! $figures->collected[$session->id]->cashier->recv !!}</b></td>
                      <td class="currency">{{ $figures->expected[$session->id]->remitment->count }}</td>
                      <td class="currency"><b>{!! $figures->collected[$session->id]->remitment->count !!}</b></td>
                      <td class="currency">{{ $figures->expected[$session->id]->remitment->amount }}</td>
                      <td class="currency"><b>{!! $figures->collected[$session->id]->remitment->amount !!}</b></td>
                      <td class="currency">{{ $figures->expected[$session->id]->cashier->end }}</td>
                      <td class="currency"><b>{!! $figures->collected[$session->id]->cashier->end !!}</b></td>
                    </tr>
@endforeach
@endif
                  </tbody>
                </table>
              </div>
            </div>
          </div>
