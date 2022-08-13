@extends('report.layout')

@section('page-title', 'Siak Tontine')

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
              <h4>{{ $session->title }}</h4>
            </div>
          </div>

          <div class="row mt-5">
            <div class="col">
              <h6 class="section-title">{{ __('meeting.titles.agenda') }}</h6>
              <p>{{ $session->agenda }}</p>
            </div>
          </div>

          <div class="row mt-5">
            <div class="col">
              <h6 class="section-title">{{ __('meeting.titles.report') }}</h6>
              <p>{{ $session->report }}</p>
            </div>
          </div>

          <div class="row align-items-center">
            <div class="col-auto">
              <div class="section-title mt-0">{!! __('meeting.titles.deposits') !!}</div>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>{!! __('common.labels.title') !!}</th>
                  <th>&nbsp;</th>
                  <th>&nbsp;</th>
                </tr>
              </thead>
              <tbody>
@foreach($receivables as $fund)
@if($session->disabled($fund))
                @include('pages.meeting.fund.disabled', [
                    'fund' => $fund,
                ])
@elseif($session->opened)
                @include('pages.meeting.fund.opened', [
                    'fund' => $fund,
                    'paid' => $fund->recv_paid,
                    'count' => $fund->recv_count,
                    'tontine' => $tontine,
                    'menuClass' => 'btn-fund-deposits',
                    'menuText' => __('meeting.actions.deposits'),
                ])
@elseif($session->closed)
                @include('pages.meeting.fund.closed', [
                    'fund' => $fund,
                    'paid' => $fund->recv_paid,
                    'count' => $fund->recv_count,
                    'summary' => $summary['receivables'],
                ])
@else
                @include('pages.meeting.fund.pending', [
                    'fund' => $fund,
                    'paid' => $fund->recv_paid,
                    'count' => $fund->recv_count,
                ])
@endif
@endforeach
@if($session->closed)
                <tr>
                  <td colspan="2">{!! __('common.labels.total') !!}</td>
                  <td>{{ $summary['sum']['receivables'] }}</td>
                </tr>
@endif
              </tbody>
            </table>
          </div> <!-- End table -->

          <div class="row align-items-center">
            <div class="col-auto">
              <div class="section-title mt-0">{!! __('meeting.titles.remittances') !!}</div>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>{!! __('common.labels.title') !!}</th>
                  <th>&nbsp;</th>
                  <th>&nbsp;</th>
                </tr>
              </thead>
              <tbody>
@foreach($payables as $fund)
@if($session->disabled($fund))
                @include('pages.meeting.fund.disabled', [
                    'fund' => $fund,
                ])
@elseif($session->opened)
                @include('pages.meeting.fund.opened', [
                    'fund' => $fund,
                    'paid' => $fund->pay_paid,
                    'count' => $fund->pay_count,
                    'tontine' => $tontine,
                    'menuClass' => 'btn-fund-remittances',
                    'menuText' => __('meeting.actions.remittances'),
                ])
@elseif($session->closed)
                @include('pages.meeting.fund.closed', [
                    'fund' => $fund,
                    'paid' => $fund->pay_paid,
                    'count' => $fund->pay_count,
                    'summary' => $summary['payables'],
                ])
@else
                @include('pages.meeting.fund.pending', [
                    'fund' => $fund,
                    'paid' => $fund->pay_paid,
                    'count' => $fund->pay_count,
                ])
@endif
@endforeach
@if($session->closed)
                <tr>
                  <td colspan="2">{!! __('common.labels.total') !!}</td>
                  <td>{{ $summary['sum']['payables'] }}</td>
                </tr>
@endif
              </tbody>
            </table>
          </div> <!-- End table -->

          @include('report.fees', $fees)

          @include('report.fines', $fines)
@endsection
