      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="tontine-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('name', __('common.labels.name'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-9">
                {!! Form::text('name', $tontine->name, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('shortname', __('common.labels.shortname'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-7">
                {!! Form::text('shortname', $tontine->shortname, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('city', __('common.labels.city'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-7">
                {!! Form::text('city', $tontine->city, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('country_code', __('common.labels.country'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-9">
                {!! Form::select('country_code', $countries, $tontine->country_code, ['class' => 'form-control', 'id' => 'select_country_dropdown']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('currency_code', __('common.labels.currency'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-9" id="select_currency_container">
@include('tontine.app.default.pages.tontine.currency')
              </div>
            </div>
          </div>
        </form>
      </div>
