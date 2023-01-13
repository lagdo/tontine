      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="member-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('gender', trans('common.labels.gender'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-md-4">
                {!! Form::select('gender', $genders, $member->gender, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('name', trans('common.labels.name'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-md-8">
                {!! Form::text('name', $member->name, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('birthday', trans('common.labels.birthday'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-md-6">
                {!! Form::date('birthday', $member->birthday, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('email', trans('common.labels.email'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-md-8">
                {!! Form::text('email', $member->email, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('phone', trans('common.labels.phone'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-md-6">
                {!! Form::text('phone', $member->phone, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('city', trans('common.labels.city'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-md-8">
                {!! Form::text('city', $member->city, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('address', trans('common.labels.address'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-md-8">
                {!! Form::textarea('address', $member->address, ['class' => 'form-control']) !!}
              </div>
            </div>
          </div>
        </form>
      </div>
