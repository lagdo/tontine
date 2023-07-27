          <div class="section-body">
            <div class="row align-items-center">
              <div class="col-sm-8">
                <h2 class="section-title">{{ __('tontine.charge.titles.add') }} ({{ $label }})</h2>
              </div>
              <div class="col-sm-4">
                <div class="btn-group float-right" role="group" aria-label="">
                  <button type="button" class="btn btn-primary" id="btn-cancel"><i class="fa fa-arrow-left"></i></button>
@if($useFaker)
                  <button type="button" class="btn btn-primary" id="btn-fakes"><i class="fa fa-fill"></i></button>
@endif
                  <button type="button" class="btn btn-primary" id="btn-save"><i class="fa fa-save"></i></button>
                </div>
              </div>
            </div>
          </div>

          <!-- Data tables -->
          <div class="card shadow mb-4">
            <div class="card-body" id="content-page">
              <div class="portlet-body form">
                <form class="form-horizontal" role="form" id="charge-form">
                  <div class="module-body">
                    <div class="form-group row">
@if ($fixed)
                      {!! Form::label('period', trans('common.labels.period'), ['class' => 'col-sm-2 col-form-label']) !!}
@else
                      {!! Form::label('type', trans('common.labels.type'), ['class' => 'col-sm-2 col-form-label']) !!}
@endif
                      {!! Form::label('name', trans('common.labels.name'), ['class' => 'col-sm-6 col-form-label']) !!}
                      {!! Form::label('amount', trans('common.labels.amount') . " ($currency)", ['class' => 'col-sm-2 col-form-label']) !!}
                    </div>
@for($i = 0; $i < $count; $i++)
                    <div class="form-group row">
                      <div class="col-sm-2">
@if ($fixed)
                        {!! Form::select('charges[' . $i . '][period]', $periods, '', ['class' => 'form-control', 'id' => "charge_period_$i"]) !!}
@else
                        {!! Form::select('charges[' . $i . '][type]', $types, '', ['class' => 'form-control', 'id' => "charge_type_$i"]) !!}
@endif
                      </div>
                      <div class="col-sm-6">
                        {!! Form::text('charges[' . $i . '][name]', '', ['class' => 'form-control', 'id' => "charge_name_$i"]) !!}
                      </div>
                      <div class="col-sm-2">
@if ($fixed)
                        {!! Form::text('charges[' . $i . '][amount]', '', ['class' => 'form-control', 'id' => "charge_amount_$i"]) !!}
                        {!! Form::hidden('charges[' . $i . '][fixed]', '1') !!}
@else
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <div class="input-group-text">
                              {!! Form::checkbox('charges[' . $i . '][fixed]', '1', true) !!}
                            </div>
                          </div>
                          {!! Form::text('charges[' . $i . '][amount]', '', ['class' => 'form-control', 'id' => "charge_amount_$i"]) !!}
                        </div>
@endif
                      </div>
                    </div>
@endfor
                  </div>
                </form>
              </div>
            </div>
          </div>
