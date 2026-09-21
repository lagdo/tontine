            <ul class="sidebar-menu" id="admin-menu">
              <li class="menu-header">{{ __('tontine.menus.admin') }}</li>

              <li><a class="nav-link" id="admin-menu-guilds" role="link" {!!
                $ajax ? ' tabindex="0"' : 'href=' . route('tontine.home') !!}>
                <i class="fa fa-fw fa-house-user"></i> <span>{{ __('tontine.menus.guilds') }}</span>
              </a></li>
              <li><a class="nav-link" id="admin-menu-users" role="link" tabindex="0">
                <i class="fa fa-fw fa-user-circle"></i> <span>{{ __('tontine.menus.users') }}</span>
              </a></li>
            </ul>
