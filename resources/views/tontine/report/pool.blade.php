@inject('locale', 'Siak\Tontine\Service\LocaleService')
@extends('tontine.report.layout')

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
              <h3 class="float-right">{{ $country }}</h3>
            </div>
          </div>
          <div class="row mt-2">
            <div class="col d-flex justify-content-center flex-nowrap">
              <h4>{{ $pool->title }}</h4>
            </div>
          </div>

          <div class="row mt-5">
            <div class="col">
              <h5 class="section-title">{{ __('figures.titles.amounts') }} ({{ $currency }})</h5>
            </div>
          </div>

          <div class="row">
            <div class="col" id="content-page">
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
                      <th colspan="2">{{ __('figures.titles.start') }}</th>
                      <th colspan="2">{{ __('figures.deposit.titles.count') }}</th>
                      <th colspan="2">{{ __('figures.deposit.titles.amount') }}</th>
                      <th colspan="2">{{ __('figures.titles.recv') }}</th>
                      <th colspan="2">{{ __('figures.remitment.titles.count') }}</th>
                      <th colspan="2">{{ __('figures.remitment.titles.amount') }}</th>
                      <th colspan="2">{{ __('figures.titles.end') }}</th>
@endif
                    </tr>
                  </thead>
                  <tbody>
@if ($tontine->is_libre)
@foreach ($sessions as $session)
                    <tr>
                      <th>{{ $session->title }}</th>
@if($session->disabled($pool))
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
@else
                      <td class="currency"><b>{!! $locale->formatMoney($figures->collected[$session->id]->cashier->start, true) !!}</b></td>
                      <td class="currency"><b>{!! $figures->collected[$session->id]->deposit->count !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($figures->collected[$session->id]->deposit->amount, true) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($figures->collected[$session->id]->cashier->recv, true) !!}</b></td>
                      <td class="currency"><b>{!! $figures->collected[$session->id]->remitment->count !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($figures->collected[$session->id]->remitment->amount, true) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($figures->collected[$session->id]->cashier->end, true) !!}</b></td>
@endif
                    </tr>
@endforeach
@else
@foreach ($sessions as $session)
                    <tr>
                      <th>{{ $session->title }}</th>
@if($session->disabled($pool))
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
@elseif($session->pending)
                      <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->cashier->start, true) }}</td>
                      <td class="currency"></td>
                      <td class="currency">{{ $figures->expected[$session->id]->deposit->count }}</td>
                      <td class="currency"></td>
                      <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->deposit->amount, true) }}</td>
                      <td class="currency"></td>
                      <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->cashier->recv, true) }}</td>
                      <td class="currency"></td>
                      <td class="currency">{{ $figures->expected[$session->id]->remitment->count }}</td>
                      <td class="currency"></td>
                      <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->remitment->amount, true) }}</td>
                      <td class="currency"></td>
                      <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->cashier->end, true) }}</td>
                      <td class="currency"></td>
@else
                      <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->cashier->start, true) }}</td>
                      <td class="currency"><b>{!! $locale->formatMoney($figures->collected[$session->id]->cashier->start, true) !!}</b></td>
                      <td class="currency">{{ $figures->expected[$session->id]->deposit->count }}</td>
                      <td class="currency"><b>{!! $figures->collected[$session->id]->deposit->count !!}</b></td>
                      <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->deposit->amount, true) }}</td>
                      <td class="currency"><b>{!! $locale->formatMoney($figures->collected[$session->id]->deposit->amount, true) !!}</b></td>
                      <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->cashier->recv, true) }}</td>
                      <td class="currency"><b>{!! $locale->formatMoney($figures->collected[$session->id]->cashier->recv, true) !!}</b></td>
                      <td class="currency">{{ $figures->expected[$session->id]->remitment->count }}</td>
                      <td class="currency"><b>{!! $figures->collected[$session->id]->remitment->count !!}</b></td>
                      <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->remitment->amount, true) }}</td>
                      <td class="currency"><b>{!! $locale->formatMoney($figures->collected[$session->id]->remitment->amount, true) !!}</b></td>
                      <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->cashier->end, true) }}</td>
                      <td class="currency"><b>{!! $locale->formatMoney($figures->collected[$session->id]->cashier->end, true) !!}</b></td>
@endif
                    </tr>
@endforeach
@endif
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="section-body pagebreak">
            <div class="row">
              <div class="col">
                <h5 class="section-title">{{ __('meeting.titles.deposits') }} ({{ $currency }})</h5>
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
@isset($subscription->receivables[$session->id])
                      <td class="currency"><b>{!! $subscription->receivables[$session->id]->deposit ? $locale->formatMoney($pool->amount, true) : 0 !!}</b></td>
@else
                      <td class="currency">&nbsp;</td>
@endisset
@endforeach
                    </tr>
                    <tr>
@foreach($sessions as $session)
                      <td class="currency">{{ $session->disabled($pool) ? '&nbsp;' : $locale->formatMoney($pool->amount, true) }}</td>
@endforeach
                    </tr>
@endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
@endsection
