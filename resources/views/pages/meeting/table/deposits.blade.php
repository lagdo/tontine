          <div class="section-body">
            <div class="row align-items-center">
              <div class="col">
                <h2 class="section-title">{{ $fund->title }} - {{ __('figures.titles.deposits') }}</h2>
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
                  <button type="button" class="btn btn-primary" id="btn-meeting-table-print"><i class="fa fa-file-pdf"></i></button>
                  <button type="button" class="btn btn-primary" id="btn-meeting-table-amounts"><i class="fa fa-cash-register"></i></button>
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
                  </tbody>
                </table>
              </div>
            </div>
          </div>
