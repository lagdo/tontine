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
            <ul class="sidebar-menu" id="finance-menu">
              <li class="menu-header">{{ __('tontine.menus.finance') }}</li>

              <li><a class="nav-link" id="finance-menu-charges" role="link" tabindex="0">
                <i class="fa fa-fw fa-money-check"></i> <span>{{ __('tontine.menus.charges') }}</span>
              </a></li>
              <li><a class="nav-link" id="finance-menu-accounts" role="link" tabindex="0">
                <i class="fa fa-fw fa-wallet"></i> <span>{{ __('tontine.menus.accounts') }}</span>
              </a></li>
              <li><a class="nav-link" id="finance-menu-pools" role="link" tabindex="0">
                <i class="fa fa-fw fa-coins"></i> <span>{{ __('tontine.menus.pools') }}</span>
              </a></li>
            </ul>
            <ul class="sidebar-menu" id="tontine-menu">
              <li class="menu-header">{{ __('tontine.menus.tontine') }}</li>

              <li><a class="nav-link" id="tontine-menu-members" role="link" tabindex="0">
                <i class="fa fa-fw fa-users"></i> <span>{{ __('tontine.menus.members') }}</span>
              </a></li>
              <li><a class="nav-link" id="tontine-menu-calendar" role="link" tabindex="0">
                <i class="fa fa-fw fa-calendar-day"></i> <span>{{ __('tontine.menus.calendar') }}</span>
              </a></li>
            </ul>
