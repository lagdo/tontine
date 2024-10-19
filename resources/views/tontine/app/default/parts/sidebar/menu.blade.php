@if (!isset($hideSidebar) || !$hideSidebar)
@php
  $rqMenuTontine = Jaxon\rq(App\Ajax\Web\SidebarMenuTontine::class);
  $rqMenuRound = Jaxon\rq(App\Ajax\Web\SidebarMenuRound::class);
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
            <li class="menu-header">{{ __('tontine.menus.tontines') }}</li>
            <li><a class="nav-link" id="tontine-menu-tontines" href="{{ $ajax ? 'javascript:void(0)' : route('tontine.home') }}">
              <i class="fa fa-fw fa-user-shield"></i> <span>{{ __('tontine.menus.tontines') }}</span>
            </a></li>
            <li><a class="nav-link" id="tontine-menu-users" role="link">
              <i class="fa fa-fw fa-users-cog"></i> <span>{{ __('tontine.menus.admins') }}</span>
            </a></li>
          </ul>
          <div @jxnShow($rqMenuTontine) id="sidebar-menu-tontine">
            @jxnHtml($rqMenuTontine)
          </div>
          <div @jxnShow($rqMenuRound) id="sidebar-menu-round">
            @jxnHtml($rqMenuRound)
          </div>
        </aside>
      </div>
@endif
