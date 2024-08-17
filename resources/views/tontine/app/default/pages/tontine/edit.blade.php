      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="tontine-form">
          <div class="module-body">
            <div class="form-group row">
              {!! $htmlBuilder->label(__('common.labels.name'), 'name')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-9">
                {!! $htmlBuilder->text('name', $tontine->name)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $htmlBuilder->label(__('common.labels.shortname'), 'shortname')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-7">
                {!! $htmlBuilder->text('shortname', $tontine->shortname)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $htmlBuilder->label(__('common.labels.city'), 'city')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-7">
                {!! $htmlBuilder->text('city', $tontine->city)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $htmlBuilder->label(__('common.labels.country'), 'country_code')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-9">
                {!! $htmlBuilder->select('country_code', $countries, $tontine->country_code)->class('form-control')->id('select_country_dropdown') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $htmlBuilder->label(__('common.labels.currency'), 'currency_code')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-9" id="select_currency_container">
@include('tontine.app.default.pages.tontine.currency')
              </div>
            </div>
          </div>
        </form>
      </div>
