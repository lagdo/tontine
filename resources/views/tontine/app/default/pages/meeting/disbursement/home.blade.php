@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $disbursementId = jq()->parent()->attr('data-disbursement-id')->toInt();
  $rqDisbursement = rq(Ajax\App\Meeting\Session\Cash\Disbursement::class);
  $rqDisbursementFunc = rq(Ajax\App\Meeting\Session\Cash\DisbursementFunc::class);
  $rqBalance = rq(Ajax\App\Meeting\Session\Cash\Balance::class);
@endphp
                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.disbursements') }}</div>
                    </div>
                    <div class="col-auto" @jxnBind($rqBalance)>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqDisbursementFunc->addDisbursement())><i class="fa fa-plus"></i></button>
                        <button type="button" class="btn btn-primary" @jxnClick($rqDisbursement->home())><i class="fa fa-sync"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive" id="content-session-disbursements" @jxnTarget()>
                    <div @jxnEvent(['.btn-disbursement-edit', 'click'], $rqDisbursementFunc->editDisbursement($disbursementId))></div>
                    <div @jxnEvent(['.btn-disbursement-delete', 'click'], $rqDisbursementFunc->deleteDisbursement($disbursementId)
                      ->confirm(__('meeting.disbursement.questions.delete')))></div>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('meeting.labels.category') !!}</th>
                          <th>{!! __('meeting.labels.member') !!}</th>
                          <th>{!! __('meeting.labels.charge') !!}</th>
                          <th class="currency">{!! __('common.labels.amount') !!}</th>
                          <th class="table-item-menu">&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($disbursements as $disbursement)
                        <tr>
                          <td>{{ $disbursement->category->name }}@if (($disbursement->comment)) <br/>{{
                            $disbursement->comment }}@endif</td>
                          <td>@if (($disbursement->member)) {{ $disbursement->member->name }}@endif</td>
                          <td>@if (($disbursement->charge)) {{ $disbursement->charge->name }}@endif</td>
                          <td class="currency">{{ $locale->formatMoney($disbursement->amount) }}</td>
                          <td class="table-item-menu">
@include('tontine::parts.table.menu', [
  'dataIdKey' => 'data-disbursement-id',
  'dataIdValue' => $disbursement->id,
  'menus' => [[
    'class' => 'btn-disbursement-edit',
    'text' => __('common.actions.edit'),
  ],[
    'class' => 'btn-disbursement-delete',
    'text' => __('common.actions.delete'),
  ]],
])
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
