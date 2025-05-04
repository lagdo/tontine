<!DOCTYPE html>
<html lang="en">

<head>
  @include('tontine::parts.header.html')

@yield('styles')
</head>

<body>
  <div id="app">
    <div class="main-wrapper">
      <div class="navbar-bg"></div>
      <nav class="navbar navbar-expand-lg main-navbar">
@include('tontine::parts.header.menu')
      </nav>

@yield('sidebar')

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          @section('page-header')
          <!-- Page heading -->
          <div class="section-header">
            <h2>@yield('section-title')</h2>
          </div>
          @show

          @include('tontine::parts.header.message')

          @yield('content')
        </section>
      </div>
    </div>
  </div>

@include('tontine::parts.content.feedback')
</body>

@include('tontine::parts.footer.html')

@yield('script')
</html>
