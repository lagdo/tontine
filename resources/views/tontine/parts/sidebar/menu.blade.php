@if (!isset($hideSidebar) || !$hideSidebar)
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
@if ($ajax)
            <li><a class="nav-link" id="tontine-menu-tontines" href="javascript:void(0)">
              <i class="fa fa-landmark"></i> <span>{{ __('tontine.menus.tontines') }}</span>
            </a></li>
@else
            <li><a class="nav-link" id="tontine-menu-tontines" href="{{ route('tontine.home') }}">
              <i class="fa fa-landmark"></i> <span>{{ __('tontine.menus.tontines') }}</span>
            </a></li>
@endif
          </ul>
          <div id="sidebar-menu-tontine">
@include('tontine.parts.sidebar.tontine')
          </div>
          <div id="sidebar-menu-round">
@include('tontine.parts.sidebar.round')
          </div>
        </aside>
      </div>
@endif
