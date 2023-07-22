@inject('tenantService', 'Siak\Tontine\Service\TenantService')

            <ul class="sidebar-menu">
              <li class="menu-header">{{ __('tontine.menus.planning') }}</li>
              <li><a class="nav-link" id="planning-menu-sessions" href="javascript:void(0)">
                <i class="fa fa-calendar-day"></i> <span>{{ __('tontine.menus.sessions') }}</span>
              </a></li>
              <li><a class="nav-link" id="planning-menu-subscriptions" href="javascript:void(0)">
                <i class="fa fa-user"></i> <span>{{ __('tontine.menus.subscriptions') }}</span>
              </a></li>
              <li><a class="nav-link" id="planning-menu-beneficiaries" href="javascript:void(0)">
                <i class="fa fa-user"></i> <span>{{ __('tontine.menus.beneficiaries') }}</span>
              </a></li>
@if ($tontine !== null && !$tontine->is_libre)
              <li><a class="nav-link" id="planning-menu-balance" href="javascript:void(0)">
                <i class="fa fa-calendar-week"></i> <span>{{ __('tontine.menus.balance') }}</span>
              </a></li>
@endif

              <li class="menu-header">{{ __('tontine.menus.meeting') }}</li>
              <li><a class="nav-link" id="meeting-menu-sessions" href="javascript:void(0)">
                <i class="fa fa-calendar-day"></i> <span>{{ __('tontine.menus.sessions') }}</span>
              </a></li>
              <li class="menu-header">{{ __('tontine.menus.balance') }}</li>
              <li><a class="nav-link" id="balance-menu-session" href="javascript:void(0)">
                <i class="fa fa-calendar-day"></i> <span>{{ __('tontine.menus.sessions') }}</span>
              </a></li>
              <li><a class="nav-link" id="balance-menu-round" href="javascript:void(0)">
                <i class="fa fa-calendar-week"></i> <span>{{ __('tontine.menus.rounds') }}</span>
              </a></li>
            </ul>
