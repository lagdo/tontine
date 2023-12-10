          <div class="portlet-body form">
            <form class="form-horizontal" role="form" id="charge-form">
              <div class="module-body">
                <div class="form-group row">
@if ($fixed)
                  {!! Form::label('period', __('common.labels.period'), ['class' => 'col-sm-4 col-form-label text-right']) !!}
                  <div class="col-md-5">
                    {!! Form::select('period', $periods, '', ['class' => 'form-control']) !!}
                  </div>
@else
                  {!! Form::label('type', __('common.labels.type'), ['class' => 'col-sm-4 col-form-label text-right']) !!}
                  <div class="col-md-5">
                    {!! Form::select('type', $types, '', ['class' => 'form-control']) !!}
                  </div>
@endif
                </div>
                <div class="form-group row">
                  {!! Form::label('name', __('common.labels.name'), ['class' => 'col-sm-4 col-form-label text-right']) !!}
                  <div class="col-md-7">
                    {!! Form::text('name', '', ['class' => 'form-control']) !!}
                  </div>
                </div>
                <div class="form-group row">
                  {!! Form::label('amount', __('common.labels.amount') . " ($currency)", ['class' => 'col-sm-4 col-form-label text-right']) !!}
                  <div class="col-md-6">
@if ($fixed)
                    {!! Form::text('amount', '', ['class' => 'form-control']) !!}
                    {!! Form::hidden('fixed', '1') !!}
@else
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <div class="input-group-text">
                          {!! Form::checkbox('fixed', '1', false) !!}
                        </div>
                      </div>
                      {!! Form::text('amount', '', ['class' => 'form-control']) !!}
                    </div>
@endif
                  </div>
                </div>
                <div class="form-group row">
                  {!! Form::label('lendable', __('tontine.charge.labels.lendable'), ['class' => 'col-sm-4 col-form-label text-right']) !!}
                  <div class="col-md-3 pt-2">
                    {!! Form::checkbox('lendable', '1', false) !!}
                  </div>
                </div>
              </div>
            </form>
          </div>
