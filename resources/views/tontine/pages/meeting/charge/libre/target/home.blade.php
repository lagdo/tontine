@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{{ $charge->name }} - {{ __('meeting.target.actions.deadline') }}</div>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-fee-target-back"><i class="fa fa-arrow-left"></i></button>
@if (!$target)
                        <button type="button" class="btn btn-primary" id="btn-fee-target-add"><i class="fa fa-plus"></i></button>
@else
                        <button type="button" class="btn btn-primary" id="btn-fee-target-edit"><i class="fa fa-edit"></i></button>
                        <button type="button" class="btn btn-primary" id="btn-fee-target-remove"><i class="fa fa-trash"></i></button>
@endif
                      </div>
                    </div>
                  </div>
@if (($target))
                  <div class="row align-items-center">
                    <div class="col">
                      {{ __('meeting.target.titles.summary', [
                          'amount' => $locale->formatMoney($target->amount, true),
                          'deadline' => $target->deadline->title,
                      ]) }}
                    </div>
                  </div>
@endif
                  <div class="table-responsive" id="meeting-fee-libre-target">
                  </div> <!-- End table -->
