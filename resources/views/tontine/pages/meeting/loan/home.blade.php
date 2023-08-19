@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row align-items-center">
                    <div class="col-auto">
                      <div class="section-title mt-0">
                        {{ __('meeting.titles.loans') }} ({{ __('meeting.loan.labels.amount_available',
                          ['amount' => $locale->formatMoney($amountAvailable, true)]) }})
                      </div>
                    </div>
@if($session->opened)
                    <div class="col">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-loan-add"><i class="fa fa-plus"></i></button>
                        <button type="button" class="btn btn-primary" id="btn-loans-refresh"><i class="fa fa-sync"></i></button>
                      </div>
                    </div>
@endif
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('meeting.labels.member') !!}</th>
                          <th class="currency">{!! __('common.labels.amount') !!}</th>
                          <th class="table-item-menu">&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($loans as $loan)
                        <tr>
                          <td>{{ $loan->member->name }}</td>
                          <td class="currency">
                            {{ $locale->formatMoney($loan->principal, true) }}<br/>
                            {{ __('meeting.loan.interest.i' . $loan->interest_type) }}: {{ $loan->fixed_interest ?
                              $locale->formatMoney($loan->interest, true) : ($loan->interest_rate / 100) . '%' }}
                          </td>
                          <td class="table-item-menu">
@if ($session->closed || ($loan->remitment_id))
                            <i class="fa fa-trash-alt"></i>
@else
@include('tontine.parts.table.menu', [
  'dataIdKey' => 'data-loan-id',
  'dataIdValue' => $loan->id,
  'menus' => [[
    'class' => 'btn-loan-edit',
    'text' => __('common.actions.edit'),
  ],[
    'class' => 'btn-loan-delete',
    'text' => __('common.actions.delete'),
  ]],
])
@endif
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
