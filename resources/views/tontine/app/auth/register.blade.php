@extends('tontine.app.auth.layout')

@inject('localeService', 'Siak\Tontine\Service\LocaleService')

@section('page-title', __('Register'))

@section('css')
  <link rel="stylesheet" href="/tpl/node_modules/selectric/public/selectric.css">
@endsection

@section('content-class', 'col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4')

@section('content')
            <div class="card card-primary">
              <div class="card-header"><h4>{{ __('Register') }}</h4></div>

              <div class="card-body">
                <form method="POST" action="{{ route('register') }}">
                  @csrf

                  <div class="form-group">
                    <label for="name">{{ __('Name') }}</label>
                    <input id="name" type="text" class="form-control @error('name')is-invalid @enderror" name="name" value="{{ old('name') }}" autofocus>
                    <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                  </div>

                  <div class="form-group">
                    <label for="email">{{ __('Email') }}</label>
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
                    <label for="password" class="d-block">{{ __('Password') }}</label>
                    <input id="password" type="password" class="form-control pwstrength @error('password')is-invalid @enderror" data-indicator="pwindicator" name="password">
                    <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                    <div id="pwindicator" class="pwindicator">
                      <div class="bar"></div>
                      <div class="label"></div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="password_confirmation" class="d-block">{{ __('Confirm Password') }}</label>
                    <input id="password_confirmation" type="password" class="form-control" name="password_confirmation">
                  </div>

                  <div class="form-group">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" name="agree" class="custom-control-input @error('agree')is-invalid @enderror" id="agree">
                      <label class="custom-control-label" for="agree">{!! __('I agree to the :terms_of_service and :privacy_policy',
                        [
                          'terms_of_service' => '<a href="javascript:void(0)">' . __('Terms of Service') . '</a>',
                          'privacy_policy' => '<a href="javascript:void(0)">' . __('Privacy Policy') . '</a>',
                        ]) !!}</label>
                      <div class="invalid-feedback">{{ $errors->first('agree') }}</div>
                    </div>
                  </div>

                  <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block">{{ __('Register') }}</button>
                  </div>
                </form>
              </div>
            </div>
            <div class="mt-5 text-muted text-center">
              <a href="{{ route('login') }}">{{ __('Back to the login page') }}</a>
            </div>
@endsection
