@if (!isset($hideSidebar) || !$hideSidebar)
@php
  $rqTontineMenu = Jaxon\rq(Ajax\App\Sidebar\TontineMenu::class);
  $rqRoundMenu = Jaxon\rq(Ajax\App\Sidebar\RoundMenu::class);
@endphp
      <div class="main-sidebar">
        <aside id="sidebar-wrapper">
          <div class="sidebar-brand">
            <a href="/">Siak Tontine</a>
          </div>
          <div class="sidebar-brand sidebar-brand-sm">
            <a href="/">Siak</a>
          </div>
          <ul class="sidebar-menu">
            <li class="menu-header">{{ __('tontine.menus.admin') }}</li>
            <li><a class="nav-link" id="tontine-menu-tontines" role="link" {{ $ajax ? 'tabindex="0"' : 'href=' . route('tontine.home') }}>
              <i class="fa fa-fw fa-user-shield"></i> <span>{{ __('tontine.menus.tontines') }}</span>
            </a></li>
            <li><a class="nav-link" id="tontine-menu-users" role="link" tabindex="0">
              <i class="fa fa-fw fa-users-cog"></i> <span>{{ __('tontine.menus.users') }}</span>
            </a></li>
          </ul>
          <div @jxnBind($rqTontineMenu) id="sidebar-menu-tontine">
            @jxnHtml($rqTontineMenu)
          </div>
          <div @jxnBind($rqRoundMenu) id="sidebar-menu-round">
            @jxnHtml($rqRoundMenu)
          </div>
        </aside>
      </div>
@endif
