      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="charge-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('name', trans('common.labels.name'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-md-8">
                {!! Form::text('name', $charge->name, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('type', trans('common.labels.type'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-md-5">
                {!! Form::select('type', $types, $charge->type, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('period', trans('common.labels.period'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-md-5">
                {!! Form::select('period', $periods, $charge->period, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('amount', trans('common.labels.amount'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-md-5">
                {!! Form::text('amount', $charge->amount_value, ['class' => 'form-control']) !!}
              </div>
            </div>
          </div>
        </form>
      </div>
