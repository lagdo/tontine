@extends('tontine.base.layout')

@section('page-title', 'Siak Tontine')

@section('section-title', __('user.titles.profile'))

@section('sidebar')
          @include('tontine.parts.sidebar.menu', ['ajax' => false])
@endsection

@section('content')
          <div class="row">
            <div class="col-md-6 col-sm-12">
              <div class="card">
                <form method="POST" action="{{ route('user-profile-information.update') }}" class="needs-validation" novalidate="">
                  @csrf
                  <div class="card-header">
                    <h4>{{ __('user.profile.actions.edit') }}</h4>
                  </div>
                  <div class="card-body">
                    <div class="form-group">
                      {!! Form::label('name', __('common.labels.name')) !!}
                      {!! Form::text('name', old('name', $user->name), ['class' => 'form-control']) !!}
                      <div class="invalid-feedback">
                        Please fill in the name
                      </div>
                    </div>
                    <div class="form-group">
                      {!! Form::label('city', __('common.labels.city')) !!}
                      {!! Form::text('city', old('city', $user->city), ['class' => 'form-control']) !!}
                      <div class="invalid-feedback">
                        Please fill in the city
                      </div>
                    </div>
                    <div class="form-group">
                      {!! Form::label('country_code', __('common.labels.country')) !!}
                      {!! Form::select('country_code', $countries, old('country_code', $user->country_code), ['class' => 'form-control']) !!}
                    </div>
                  </div>
                  <div class="card-footer text-right">
                    <button class="btn btn-primary">{{ __('common.actions.save') }}</button>
                  </div>
                </form>
              </div>
            </div>
            <div class="col-md-6 col-sm-12">
              <div class="card">
                <form method="POST" action="{{ route('user-password.update') }}" class="needs-validation" novalidate="">
                  @csrf
                  <div class="card-header">
                    <h4>{{ __('user.password.actions.change') }}</h4>
                  </div>
                  <div class="card-body">
                    <div class="form-group">
                      {!! Form::label('current_password', __('user.password.labels.current')) !!}
                      <input type="password" class="form-control" name="current_password" value="" required="">
                      <div class="invalid-feedback">
                        Please fill in the current password
                      </div>
                    </div>
                    <div class="form-group">
                      {!! Form::label('password', __('user.password.labels.new')) !!}
                      <input type="password" class="form-control" name="password" value="" required="">
                      <div class="invalid-feedback">
                        Please fill in the new password
                      </div>
                    </div>
                    <div class="form-group">
                      {!! Form::label('password_confirmation', __('user.password.labels.confirm')) !!}
                      <input type="password" class="form-control" name="password_confirmation" value="" required="">
                      <div class="invalid-feedback">
                        Please fill in the password confirmation
                      </div>
                    </div>
                  </div>
                  <div class="card-footer text-right">
                    <button class="btn btn-primary">{{ __('common.actions.save') }}</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
@endsection

@section('script')
<script type="text/javascript">
</script>
@endsection
