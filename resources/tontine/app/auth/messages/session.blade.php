@if (session('status'))
              <div class="alert alert-success" role="alert">
                <p>{!! session('status') !!}</p>
              </div>
@elseif (session('success'))
              <div class="alert alert-success" role="alert">
                <h4 class="alert-heading">{{ __('common.titles.success') }}</h4>
                <p>{!! session('success') !!}</p>
              </div>
@elseif (session('error'))
              <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">{{ __('common.titles.error') }}</h4>
                <p>{!! session('error') !!}</p>
              </div>
@elseif (session('info'))
              <div class="alert alert-info" role="alert">
                <h4 class="alert-heading">{{ __('common.titles.info') }}</h4>
                <p>{!! session('info') !!}</p>
              </div>
@elseif (session('warning'))
              <div class="alert alert-warning" role="alert">
                <h4 class="alert-heading">{{ __('common.titles.warning') }}</h4>
                <p>{!! session('warning') !!}</p>
              </div>
@endif
