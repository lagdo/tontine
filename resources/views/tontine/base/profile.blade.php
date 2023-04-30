@extends('tontine.base.layout')

@section('page-title', 'Siak Tontine')

@section('section-title', __('user.titles.profile'))

@section('sidebar')
          @include('tontine.parts.sidebar.menu', ['ajax' => false])
@endsection

@section('content')
          <div class="row">
@if (session('status'))
            <div class="col-12">
              <div class="alert alert-success alert-dismissible show fade">
                <div class="alert-body">
                  <button class="close" data-dismiss="alert"><span>×</span></button>
                  {{ session('status') }}
                </div>
              </div>
            </div>
@endif
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
                      {!! Form::text('name', old('name', $user->name), [
                        'class' => $errors->profile->has('name') ? 'form-control is-invalid' : 'form-control',
                      ]) !!}
@if($errors->profile->has('name'))
                      <div class="invalid-feedback">
                        {{ $errors->profile->first('name') }}
                      </div>
@endif
                    </div>
                    <div class="form-group">
                      {!! Form::label('city', __('common.labels.city')) !!}
                      {!! Form::text('city', old('city', $user->profile->city), [
                        'class' => $errors->profile->has('city') ? 'form-control is-invalid' : 'form-control',
                      ]) !!}
@if($errors->profile->has('city'))
                      <div class="invalid-feedback">
                        {{ $errors->profile->first('city') }}
                      </div>
@endif
                    </div>
                    <div class="form-group">
                      {!! Form::label('country', __('common.labels.country')) !!}
                      {!! Form::select('country', $countries, old('country', $user->profile->country_code), [
                        'class' => $errors->profile->has('country') ? 'form-control is-invalid' : 'form-control'
                      ]) !!}
@if($errors->profile->has('country'))
                      <div class="invalid-feedback">
                        {{ $errors->profile->first('country') }}
                      </div>
@endif
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
                      {!! Form::password('current_password', [
                        'class' => $errors->password->has('current_password') ? 'form-control is-invalid' : 'form-control',
                      ]) !!}
@if($errors->password->has('current_password'))
                      <div class="invalid-feedback">
                        {{ $errors->password->first('current_password') }}
                      </div>
@endif
                    </div>
                    <div class="form-group">
                      {!! Form::label('password', __('user.password.labels.new')) !!}
                      {!! Form::password('password', [
                        'class' => $errors->password->has('password') ? 'form-control is-invalid' : 'form-control',
                      ]) !!}
@if($errors->password->has('password'))
                      <div class="invalid-feedback">
                        {{ $errors->password->first('password') }}
                      </div>
@endif
                    </div>
                    <div class="form-group">
                      {!! Form::label('password_confirmation', __('user.password.labels.confirm')) !!}
                      {!! Form::password('password_confirmation', [
                        'class' => $errors->password->has('password_confirmation') ? 'form-control is-invalid' : 'form-control',
                      ]) !!}
@if($errors->password->has('password_confirmation'))
                      <div class="invalid-feedback">
                        {{ $errors->password->first('password_confirmation') }}
                      </div>
@endif
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
