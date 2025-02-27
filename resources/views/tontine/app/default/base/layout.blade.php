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
        <!-- Page heading -->
        <div class="section-header">

@include('tontine::parts.header.topbar')

        </div>

        <div>
@include('tontine::parts.header.message')
        </div>

        <div id="content-home" @jxnBind(rq(Ajax\App\Page\SectionContent::class))>

@yield('content')

        </div>
        </section>
      </div>

    </div>
  </div>

@include('tontine::parts.content.feedback')
</body>

@include('tontine::parts.footer.html')

@yield('script')
</html>
