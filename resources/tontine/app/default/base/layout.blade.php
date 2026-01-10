<!DOCTYPE html>
<html lang="en">

<head>
  @include('tontine_app::parts.header.html')

@yield('styles')
</head>

<body>
  <div id="app">
    <div class="main-wrapper">
      <div class="navbar-bg"></div>
      <nav class="navbar navbar-expand-lg main-navbar">
@include('tontine_app::parts.header.menu')
      </nav>

@yield('sidebar')

      <!-- Main Content -->
      <div class="main-content">
        <!-- Page heading -->
        <section class="section">
          @section('page-header')
          <div class="section-header">
            <h2>@yield('section-title')</h2>
          </div>
          @show {{-- end @section('page-header') --}}

          @include('tontine_app::parts.header.learn')

          @yield('content')
        </section>
      </div>
    </div>
  </div>

@include('tontine_app::parts.content.feedback')
</body>

@include('tontine_app::parts.footer.html')

@yield('script')
</html>
