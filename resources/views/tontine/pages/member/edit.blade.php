      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="member-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('name', __('common.labels.name'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-md-8">
                {!! Form::text('name', $member->name, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('birthday', __('common.labels.birthday'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-md-6">
                {!! Form::date('birthday', $member->birthday, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('email', __('common.labels.email'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-md-8">
                {!! Form::text('email', $member->email, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('phone', __('common.labels.phone'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-md-6">
                {!! Form::text('phone', $member->phone, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('city', __('common.labels.city'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-md-8">
                {!! Form::text('city', $member->city, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('address', __('common.labels.address'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-md-8">
                {!! Form::textarea('address', $member->address, ['class' => 'form-control']) !!}
              </div>
            </div>
          </div>
        </form>
      </div>
