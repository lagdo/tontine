      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="tontine-form">
          <div class="module-body">
            {!! Form::hidden('type', $type) !!}
            <div class="form-group row">
              {!! Form::label('name', trans('common.labels.name'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-9">
                {!! Form::text('name', '', ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('shortname', trans('common.labels.shortname'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-7">
                {!! Form::text('shortname', '', ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('city', trans('common.labels.city'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-7">
                {!! Form::text('city', '', ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('country_code', trans('common.labels.country'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-9">
                {!! Form::select('country_code', $countries, '', ['class' => 'form-control', 'id' => 'select_country_dropdown']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('currency_code', trans('common.labels.currency'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-9" id="select_currency_container">
@include('tontine.pages.tontine.currency')
              </div>
            </div>
          </div>
        </form>
      </div>
