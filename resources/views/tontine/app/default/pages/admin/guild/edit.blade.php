@php
  $rqLocaleFunc = rq(Ajax\App\LocaleFunc::class);
@endphp
      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="guild-form">
          <div class="module-body">
            <div class="form-group row">
              {!! $html->label(__('common.labels.name'), 'name')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-9">
                {!! $html->text('name', $guild->name)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('common.labels.shortname'), 'shortname')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-7">
                {!! $html->text('shortname', $guild->shortname)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('common.labels.city'), 'city')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-7">
                {!! $html->text('city', $guild->city)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('common.labels.country'), 'country_code')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-9" @jxnEvent(['select', 'change', $rqLocaleFunc->selectCurrency(jq()->val())])>
                {!! $html->select('country_code', $countries, $guild->country_code)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('common.labels.currency'), 'currency_code')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-9" id="select_currency_container">
@include('tontine::pages.admin.guild.currency')
              </div>
            </div>
          </div>
        </form>
      </div>
