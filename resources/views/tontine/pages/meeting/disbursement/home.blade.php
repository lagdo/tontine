@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row align-items-center">
                    <div class="col-auto">
                      <div class="section-title mt-0">
                        {{ __('meeting.titles.disbursements') }}
                        (<span id="total_amount_available">{{ __('meeting.disbursement.labels.amount_available',
                          ['amount' => $locale->formatMoney($amountAvailable, true)]) }}</span>)
                      </div>
                    </div>
@if($session->opened)
                    <div class="col">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-disbursement-add"><i class="fa fa-plus"></i></button>
                        <button type="button" class="btn btn-primary" id="btn-disbursements-refresh"><i class="fa fa-sync"></i></button>
                      </div>
                    </div>
@endif
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.name') !!}</th>
                          <th class="currency">{!! __('common.labels.amount') !!}</th>
                          <th class="table-item-menu">&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($disbursements as $disbursement)
                        <tr>
                          <td>
                            {{ $disbursement->category->name }}@if (($disbursement->comment)) - {{
                              $disbursement->comment }}@endif @if (($disbursement->member))<br/>{{
                              $disbursement->member->name }}@endif</td>
                          <td class="currency">{{ $locale->formatMoney($disbursement->amount, true) }}</td>
                          <td class="table-item-menu">
@if($session->opened)
@include('tontine.parts.table.menu', [
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
@else
                            <i class="fa fa-trash-alt"></i>
@endif
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
