      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="charge-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('type', trans('common.labels.type'), ['class' => 'col-sm-4 col-form-label text-right']) !!}
              <div class="col-md-5">
                {{-- {!! Form::select('type', $types, $charge->type, ['class' => 'form-control', 'disabled' => 'disabled']) !!} --}}
                {!! Form::label('type', $types[$charge->type], ['class' => 'col-sm-4 col-form-label text-right']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('period', trans('common.labels.period'), ['class' => 'col-sm-4 col-form-label text-right']) !!}
              <div class="col-md-5">
                {{-- {!! Form::select('period', $periods, $charge->period, ['class' => 'form-control', 'disabled' => 'disabled']) !!} --}}
                {!! Form::label('period', $periods[$charge->period], ['class' => 'col-sm-4 col-form-label text-right']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('name', trans('common.labels.name'), ['class' => 'col-sm-4 col-form-label text-right']) !!}
              <div class="col-md-7">
                {!! Form::text('name', $charge->name, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('amount', trans('common.labels.amount') . " ($currency)", ['class' => 'col-sm-4 col-form-label text-right']) !!}
              <div class="col-md-5">
@if ($charge->is_fixed)
                {!! Form::text('amount', $charge->amount_value, ['class' => 'form-control']) !!}
                {!! Form::hidden('fixed', '1') !!}
@else
                <div class="input-group">
                  <div class="input-group-prepend">
                    <div class="input-group-text">
                      {!! Form::checkbox('fixed', '1', $charge->has_amount) !!}
                    </div>
                  </div>
                  {!! Form::text('amount', $charge->has_amount ? $charge->amount_value : '', ['class' => 'form-control']) !!}
                </div>
@endif
              </div>
            </div>
          </div>
        </form>
      </div>
