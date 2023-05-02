@extends('tontine.auth.layout')

@inject('localeService', 'Siak\Tontine\Service\LocaleService')

@section('page-title', 'Register')

@section('css')
  <link rel="stylesheet" href="/tpl/node_modules/selectric/public/selectric.css">
@endsection

@section('content-class', 'col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4')

@section('content')
            <div class="card card-primary">
              <div class="card-header"><h4>Register</h4></div>

              <div class="card-body">
                <form method="POST" action="{{ route('register') }}">
                  @csrf

                  <div class="form-group">
                    <label for="name">Name</label>
                    <input id="name" type="text" class="form-control @error('name')is-invalid @enderror" name="name" value="{{ old('name') }}" autofocus>
                    <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                  </div>

                  <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" class="form-control @error('email')is-invalid @enderror" name="email" value="{{ old('email') }}">
                    <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                  </div>

                  <div class="form-group">
                    {!! Form::label('country', __('common.labels.country')) !!}
                    {!! Form::select('country', $localeService->getCountries(), old('country', ''), [
                      'class' => $errors->has('country') ? 'form-control is-invalid' : 'form-control'
                    ]) !!}
                    <div class="invalid-feedback">{{ $errors->first('country') }}</div>
                  </div>

                  <div class="form-group">
                    <label for="password" class="d-block">Password</label>
                    <input id="password" type="password" class="form-control pwstrength @error('password')is-invalid @enderror" data-indicator="pwindicator" name="password">
                    <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                    <div id="pwindicator" class="pwindicator">
                      <div class="bar"></div>
                      <div class="label"></div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="password_confirmation" class="d-block">Password Confirmation</label>
                    <input id="password_confirmation" type="password" class="form-control" name="password_confirmation">
                  </div>

                  <div class="form-group">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" name="agree" class="custom-control-input @error('agree')is-invalid @enderror" id="agree">
                      <label class="custom-control-label" for="agree">I agree with the terms and conditions</label>
                      <div class="invalid-feedback">{{ $errors->first('agree') }}</div>
                    </div>
                  </div>

                  <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                      Register
                    </button>
                  </div>
                </form>
              </div>
            </div>
            <div class="mt-5 text-muted text-center">
              <a href="{{ route('login') }}">Back to login</a>
            </div>
@endsection
