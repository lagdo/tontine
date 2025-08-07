<!DOCTYPE HTML>
<html>
  <head>
    <title>@yield('page-title') &mdash; Siak Tontine</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="/hyperspace/assets/css/main.css" />
    <noscript><link rel="stylesheet" href="/hyperspace/assets/css/noscript.css" /></noscript>
  </head>
  <body class="is-preload">

    <!-- Sidebar -->
    @include('tontine.web.content.sidebar')

    <!-- Wrapper -->
    <div id="wrapper">
      <!-- Intro --> <!-- The "fade-up" class was removed from the above section -->
      <section id="intro" class="wrapper style1 fullscreen">
        <div class="inner">
          <h1>@yield('page-title')</h1>

          @yield('content')
        </div>
      </section>

      @include('tontine.web.content.videos')
    </div>

    <!-- Footer -->
    <footer id="footer" class="wrapper style1-alt">
      <div class="inner">
        <ul class="menu">
          <!-- <li>&copy; Untitled. All rights reserved.</li> -->
          <li>Design: <a href="http://html5up.net">HTML5 UP</a></li>
        </ul>
      </div>
    </footer>

    <!-- Scripts -->
    <script src="/hyperspace/assets/js/jquery.min.js"></script>
    <script src="/hyperspace/assets/js/jquery.scrollex.min.js"></script>
    <script src="/hyperspace/assets/js/jquery.scrolly.min.js"></script>
    <script src="/hyperspace/assets/js/browser.min.js"></script>
    <script src="/hyperspace/assets/js/breakpoints.min.js"></script>
    <script src="/hyperspace/assets/js/util.js"></script>
    <script src="/hyperspace/assets/js/main.js"></script>

  </body>
</html>
