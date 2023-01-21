      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="tontine-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('type', trans('common.labels.type'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-5">
                {!! Form::select('type', $types, 0, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('name', trans('common.labels.name'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-9">
                {!! Form::text('name', '', ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('shortname', trans('common.labels.shortname'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-5">
                {!! Form::text('shortname', '', ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('city', trans('common.labels.city'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-7">
                {!! Form::text('city', '', ['class' => 'form-control']) !!}
              </div>
            </div>
          </div>
        </form>
      </div>
