@php
  $rqMenu = rq(Ajax\Page\Sidebar\Menu::class);
@endphp
<div class="main-sidebar">
  <aside id="sidebar-wrapper">
    <div class="sidebar-brand">
      <a href="/">Siak Tontine</a>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
      <a href="/">Siak</a>
    </div>

    <div @jxnBind($rqMenu)>
      @jxnHtml($rqMenu)
    </div>
  </aside>
</div>
