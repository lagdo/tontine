@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0">{{ $charge->name }}</div>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-fee-target-back"><i class="fa fa-arrow-left"></i></button>
@if ($charge->is_active)
@if (!$target)
                        <button type="button" class="btn btn-primary" id="btn-fee-target-add"><i class="fa fa-plus"></i></button>
@else
                        <button type="button" class="btn btn-primary" id="btn-fee-target-edit"><i class="fa fa-edit"></i></button>
                        <button type="button" class="btn btn-primary" id="btn-fee-target-remove"><i class="fa fa-trash"></i></button>
@endif
@endif
                      </div>
                    </div>
                  </div>
@if (($target))
                  <div class="row">
                    <div class="col">
                      {{ __('meeting.target.titles.summary', [
                          'deadline' => $target->deadline->title,
                      ]) }}
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                      <div class="input-group">
                        {!! Form::text('search', '', ['class' => 'form-control', 'id' => 'txt-fee-member-search']) !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" id="btn-fee-libre-search"><i class="fa fa-search"></i></button>
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      {{ __('common.labels.amount') }}<br/>{{ $locale->formatMoney($target->amount, true) }}
                    </div>
                  </div>
@endif
                  <div class="table-responsive" id="meeting-fee-libre-target">
                  </div> <!-- End table -->
