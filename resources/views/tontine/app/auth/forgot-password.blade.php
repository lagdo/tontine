@extends('tontine.app.auth.layout')

@section('page-title', __('Reset Password'))

@section('content-class', 'col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4')

@section('content')
            <div class="card card-primary">
              <div class="card-header"><h4>{{ __('Reset Password') }}</h4></div>

              <div class="card-body">
                <p class="text-muted">{{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}</p>
                <form method="POST" action="{{ route('password.email') }}">
                  @csrf

                  <div class="form-group">
                    <label for="email">{{ __('Email') }}</label>
                    <input id="email" type="email" class="form-control" name="email" tabindex="1" required autofocus>
                  </div>

                  <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">{{ __('Email Password Reset Link') }}</button>
                  </div>
                </form>
              </div>
            </div>
            <div class="mt-5 text-muted text-center">
              <a href="{{ route('login') }}">{{ __('Back to the login page') }}</a>
            </div>
@endsection
