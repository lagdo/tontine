<!DOCTYPE html>
<html lang="en">

<head>
  @include('tontine.app.default.parts.header.html')

@yield('styles')
</head>

<body>
  <div id="app">
    <div class="main-wrapper">
      <div class="navbar-bg"></div>
      <nav class="navbar navbar-expand-lg main-navbar">
@include('tontine.app.default.parts.header.menu')
      </nav>

@yield('sidebar')

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
        <!-- Page heading -->
        <div class="section-header">

@include('tontine.app.default.parts.header.topbar')

        </div>

        <div>
@include('tontine.app.default.parts.header.message')
        </div>

        <div id="content-home" @jxnShow(Jaxon\rq(App\Ajax\Web\Component\SectionContent::class))>

@yield('content')

        </div>
        </section>
      </div>

    </div>
  </div>

@include('tontine.app.default.parts.content.feedback')
</body>

@include('tontine.app.default.parts.footer.html')

@yield('script')
</html>
