            <ul class="sidebar-menu" id="admin-menu">
              <li class="menu-header">{{ __('tontine.menus.admin') }}</li>

              <li><a class="nav-link" id="admin-menu-tontines" role="link" {!!
                $ajax ? ' tabindex="0"' : 'href=' . route('tontine.home') !!}>
                <i class="fa fa-fw fa-user-shield"></i> <span>{{ __('tontine.menus.tontines') }}</span>
              </a></li>
              <li><a class="nav-link" id="admin-menu-users" role="link" tabindex="0">
                <i class="fa fa-fw fa-users-cog"></i> <span>{{ __('tontine.menus.users') }}</span>
              </a></li>
            </ul>

            <ul class="sidebar-menu" id="tontine-menu">
              <li class="menu-header">{{ __('tontine.menus.tontine') }}</li>

              <li><a class="nav-link" id="tontine-menu-members" role="link" tabindex="0">
                <i class="fa fa-fw fa-users"></i> <span>{{ __('tontine.menus.members') }}</span>
              </a></li>
              <li><a class="nav-link" id="tontine-menu-categories" role="link" tabindex="0">
                <i class="fa fa-fw fa-cogs"></i> <span>{{ __('tontine.menus.categories') }}</span>
              </a></li>
              <li><a class="nav-link" id="tontine-menu-calendar" role="link" tabindex="0">
                <i class="fa fa-fw fa-calendar-day"></i> <span>{{ __('tontine.menus.calendar') }}</span>
              </a></li>
            </ul>
