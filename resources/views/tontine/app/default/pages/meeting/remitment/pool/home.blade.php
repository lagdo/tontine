@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.remitments') }} - {{
                        $pool->title }}@if (!$pool->remit_planned) ({{ $locale->formatMoney($depositAmount) }})@endif</div>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-remitments-back"><i class="fa fa-arrow-left"></i></button>
@if (!$pool->remit_planned)
                        <button type="button" class="btn btn-primary" id="btn-add-remitment"><i class="fa fa-plus"></i></button>
@endif
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive" id="meeting-pool-remitments">
                  </div> <!-- End table -->
