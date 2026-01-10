@extends('tontine_app::base.layout')

@section('page-title', 'Siak Tontine')

@section('section-title', __('user.titles.profile'))

@section('sidebar')
          @include('tontine_app::parts.sidebar.menu')
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
                      {!! $html->label(__('common.labels.name'), 'name') !!}
                      {!! $html->text('name', old('name', $user->name))->class($errors->profile->has('name') ? 'form-control is-invalid' : 'form-control') !!}
@if($errors->profile->has('name'))
                      <div class="invalid-feedback">
                        {{ $errors->profile->first('name') }}
                      </div>
@endif
                    </div>
                    <div class="form-group">
                      {!! $html->label(__('common.labels.city'), 'city') !!}
                      {!! $html->text('city', old('city', $user->city))->class($errors->profile->has('city') ? 'form-control is-invalid' : 'form-control') !!}
@if($errors->profile->has('city'))
                      <div class="invalid-feedback">
                        {{ $errors->profile->first('city') }}
                      </div>
@endif
                    </div>
                    <div class="form-group">
                      {!! $html->label(__('common.labels.country'), 'country') !!}
                      {!! $html->select('country', $countries, old('country', $user->country_code))
                        ->class($errors->profile->has('country') ? 'form-control is-invalid' : 'form-control') !!}
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
                      {!! $html->label(__('user.password.labels.current'), 'current_password') !!}
                      {!! $html->password('current_password')
                        ->class($errors->password->has('current_password') ? 'form-control is-invalid' : 'form-control') !!}
@if($errors->password->has('current_password'))
                      <div class="invalid-feedback">
                        {{ $errors->password->first('current_password') }}
                      </div>
@endif
                    </div>
                    <div class="form-group">
                      {!! $html->label(__('user.password.labels.new'), 'password') !!}
                      {!! $html->password('password')
                        ->class($errors->password->has('password') ? 'form-control is-invalid' : 'form-control') !!}
@if($errors->password->has('password'))
                      <div class="invalid-feedback">
                        {{ $errors->password->first('password') }}
                      </div>
@endif
                    </div>
                    <div class="form-group">
                      {!! $html->label(__('user.password.labels.confirm'), 'password_confirmation') !!}
                      {!! $html->password('password_confirmation')
                        ->class($errors->password->has('password_confirmation') ? 'form-control is-invalid' : 'form-control') !!}
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
