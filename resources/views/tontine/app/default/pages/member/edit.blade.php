      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="member-form">
          <div class="module-body">
            <div class="form-group row">
              {!! $htmlBuilder->label(__('common.labels.name'), 'name')->class('col-sm-3 col-form-label') !!}
              <div class="col-md-8">
                {!! $htmlBuilder->text('name', $member->name)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $htmlBuilder->label(__('common.labels.birthday'), 'birthday')->class('col-sm-3 col-form-label') !!}
              <div class="col-md-6">
                {!! $htmlBuilder->date('birthday', $member->birthday)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $htmlBuilder->label(__('common.labels.email'), 'email')->class('col-sm-3 col-form-label') !!}
              <div class="col-md-8">
                {!! $htmlBuilder->text('email', $member->email)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $htmlBuilder->label(__('common.labels.phone'), 'phone')->class('col-sm-3 col-form-label') !!}
              <div class="col-md-6">
                {!! $htmlBuilder->text('phone', $member->phone)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $htmlBuilder->label(__('common.labels.city'), 'city')->class('col-sm-3 col-form-label') !!}
              <div class="col-md-8">
                {!! $htmlBuilder->text('city', $member->city)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $htmlBuilder->label(__('common.labels.address'), 'address')->class('col-sm-3 col-form-label') !!}
              <div class="col-md-8">
                {!! $htmlBuilder->textarea('address', $member->address)->class('form-control') !!}
              </div>
            </div>
          </div>
        </form>
      </div>
