@extends('report.layout')

@section('page-title', 'Siak Tontine')

@section('css')
  <style>
    @page {
      size: A4 landscape;
      /*margin: 0;*/
    }
  </style>
@endsection

@section('content')
          <div class="row">
            <div class="col-auto">
              <h3>{{ $tontine->name }}</h3>
            </div>
            <div class="col">
              <h3 class="float-right">{{ $tontine->country->name }}</h3>
            </div>
          </div>
          <div class="row mt-2">
            <div class="col d-flex justify-content-center flex-nowrap">
              <h4>{{ $pool->title }}</h4>
            </div>
          </div>

          <div class="row mt-5">
            <div class="col">
              <h5 class="section-title">{{ __('figures.titles.amounts') }} ({{ $tontine->currency->symbol() }})</h5>
            </div>
          </div>

          <div class="row">
            <div class="col" id="content-page">
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
                      @foreach($sessions as $session)<td class="currency"><b>{!! $figures->collected[$session->id]->cashier->start !!}</b></td>@endforeach
                    </tr>
                    <tr>
                      @foreach($sessions as $session)<td class="currency">{{ $figures->expected[$session->id]->cashier->start }}</td>@endforeach
                    </tr>
                    <tr>
                      <td rowspan="2">{{ __('figures.deposit.titles.count') }}</td>
                      @foreach($sessions as $session)<td class="currency"><b>{!! $figures->collected[$session->id]->deposit->count !!}</b></td>@endforeach
                    </tr>
                    <tr>
                      @foreach($sessions as $session)<td class="currency">{{ $figures->expected[$session->id]->deposit->count }}</td>@endforeach
                    </tr>
                    <tr>
                      <td rowspan="2">{{ __('figures.deposit.titles.amount') }}</td>
                      @foreach($sessions as $session)<td class="currency"><b>{!! $figures->collected[$session->id]->deposit->amount !!}</b></td>@endforeach
                    </tr>
                    <tr>
                      @foreach($sessions as $session)<td class="currency">{{ $figures->expected[$session->id]->deposit->amount }}</td>@endforeach
                    </tr>
                    <tr>
                      <td rowspan="2">{{ __('figures.titles.recv') }}</td>
                      @foreach($sessions as $session)<td class="currency"><b>{!! $figures->collected[$session->id]->cashier->recv !!}</b></td>@endforeach
                    </tr>
                    <tr>
                      @foreach($sessions as $session)<td class="currency">{{ $figures->expected[$session->id]->cashier->recv }}</td>@endforeach
                    </tr>
                    <tr>
                      <td rowspan="2">{{ __('figures.remitment.titles.count') }}</td>
                      @foreach($sessions as $session)<td class="currency"><b>{!! $figures->collected[$session->id]->remitment->count !!}</b></td>@endforeach
                    </tr>
                    <tr>
                      @foreach($sessions as $session)<td class="currency">{{ $figures->expected[$session->id]->remitment->count }}</td>@endforeach
                    </tr>
                    <tr>
                      <td rowspan="2">{{ __('figures.remitment.titles.amount') }}</td>
                      @foreach($sessions as $session)<td class="currency"><b>{!! $figures->collected[$session->id]->remitment->amount !!}</b></td>@endforeach
                    </tr>
                    <tr>
                      @foreach($sessions as $session)<td class="currency">{{ $figures->expected[$session->id]->remitment->amount }}</td>@endforeach
                    </tr>
                    <tr>
                      <td rowspan="2">{{ __('figures.titles.end') }}</td>
                      @foreach($sessions as $session)<td class="currency"><b>{!! $figures->collected[$session->id]->cashier->end !!}</b></td>@endforeach
                    </tr>
                    <tr>
                      @foreach($sessions as $session)<td class="currency">{{ $figures->expected[$session->id]->cashier->end }}</td>@endforeach
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="section-body pagebreak">
            <div class="row">
              <div class="col">
                <h5 class="section-title">{{ __('meeting.titles.deposits') }} ({{ $tontine->currency->symbol() }})</h5>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col" id="content-page">
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>{{ $pool->title }}</th>
@foreach($sessions as $session)
                      <th>
                        {{ $session->abbrev }}
                      </th>
@endforeach
                    </tr>
                  </thead>
                  <tbody>
@foreach ($subscriptions as $subscription)
                    <tr class="no-pagebreak">
                      <td rowspan="2">{{ $subscription->member->name }}</td>
@foreach($sessions as $session)
                      <td class="currency"><b>{!! $subscription->receivables[$session->id]->deposit ? $pool->money('amount', true) : ($session->opened ? 0 : '&nbsp;') !!}</b></td>
@endforeach
                    </tr>
                    <tr>
@foreach($sessions as $session)
                      <td class="currency">{{ $session->disabled($pool) ? '' : $pool->money('amount', true) }}</td>
@endforeach
                    </tr>
@endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
@endsection
