      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="member-form">
          <div class="module-body">
            <div class="form-group row">
              {!! $html->label(__('common.labels.name'), 'name')->class('col-sm-3 col-form-label') !!}*
              <div class="col-md-8">
                {!! $html->text('name', '')->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('common.labels.email'), 'email')->class('col-sm-3 col-form-label') !!}
              <div class="col-md-8">
                {!! $html->text('email', '')->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('common.labels.phone'), 'phone')->class('col-sm-3 col-form-label') !!}
              <div class="col-md-6">
                {!! $html->text('phone', '')->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('common.labels.city'), 'city')->class('col-sm-3 col-form-label') !!}
              <div class="col-md-8">
                {!! $html->text('city', '')->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('common.labels.address'), 'address')->class('col-sm-3 col-form-label') !!}
              <div class="col-md-8">
                {!! $html->textarea('address', '')->class('form-control') !!}
              </div>
            </div>
          </div>
        </form>
      </div>
