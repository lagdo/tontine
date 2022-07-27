        <form class="form-inline mr-auto">
          <ul class="navbar-nav mr-3">
            <li><a href="javascript:void(0)" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fa fa-bars"></i></a></li>
          </ul>
          <ul class="navbar-nav">
            <li class="dropdown language">
              <a href="javascript:void(0)" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                <span class="flag-icon flag-icon-{{ currentLocalizedIcon() }}"> </span>
                <div class="d-sm-none d-lg-inline-block">{{ $localeNative }}</div>
              </a>
              <div class="dropdown-menu dropdown-menu-left">
@foreach ($locales as $locale => $properties)
                <a class="dropdown-item" href="{{ localizedUrl($locale) }}">
                  <span class="flag-icon flag-icon-{{ localizedIcon($locale) }}"></span> {{ $properties['native'] }}
                </a>
@endforeach
              </div>
            </li>
          </ul>
        </form>
        <ul class="navbar-nav navbar-right">
          {{-- <li>
            <span class="nav-link nav-link-lg">
              <div class="d-sm-none d-lg-inline-block">{{ $tontine->name }} - {{ $tontine->city }} - {{ $country->name }}</div>
            </span>
          </li> --}}
          <li class="dropdown">
            <a href="javascript:void(0)" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
              <img alt="image" src="/tpl/assets/img/avatar/avatar-1.png" class="rounded-circle mr-1">
              <div class="d-sm-none d-lg-inline-block">Hi, {{ $user->name }}</div>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
              <a href="javascript:void(0)" class="dropdown-item has-icon" id="user-menu-profile">
                <i class="far fa-user"></i> Tontines
              </a>
              <div class="dropdown-divider"></div>
              <a href="javascript:void(0)" class="dropdown-item has-icon text-danger" id="user-menu-logout">
                <i class="fa fa-sign-out-alt"></i> Logout
              </a>
            </div>
          </li>
        </ul>
