            <div class="card card-primary">
              <div class="card-header"><h4>{{ __('Login') }}</h4></div>

              <div class="card-body">
                <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate="">
                  @csrf

                  <div class="form-group">
                    <label for="email">{{ __('Email') }}</label>
                    <input id="email" type="email" class="form-control @error('name')is-invalid @enderror" name="email" value="admin@company.com" tabindex="1" required autofocus>
                    <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                  </div>

                  <div class="form-group">
                    <div class="d-block">
                      <label for="password" class="control-label">{{ __('Password') }}</label>
                      <div class="float-right">
                        <a href="{{ route('password.request') }}" class="text-small">{{ __('Forgot Your Password?') }}</a>
                      </div>
                    </div>
                    <div class="input-group">
                      <input id="password" type="password" class="form-control @error('name')is-invalid @enderror" name="password" value="password" tabindex="2" required>
                      <div class="input-group-append">
                        <span class="input-group-text toggle-password"><a role="link"><i class="fa fa-eye"></i></a></span>
                      </div>
                    </div>
                    <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                  </div>

                  <div class="form-group">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember-me">
                      <label class="custom-control-label" for="remember-me">{{ __('Remember Me') }}</label>
                    </div>
                  </div>

                  <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">{{ __('Login') }}</button>
                  </div>

                  <div class="form-group">
                    <label class="control-label">{{ __('If you do not have an account, you may create one by clicking the button below.') }}</label>
                  </div>
                  <div class="form-group">
                    <a href="{{ route('register') }}" type="button" class="btn btn-primary btn-lg btn-block" tabindex="5">{{ __('Create Account') }}</a>
                  </div>
                </form>
              </div>
            </div>
