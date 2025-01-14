@extends('tontine.app.auth.layout')

@section('page-title', __('Login'))

@section('content-class', 'col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4')

@section('content')
            @include(config('tontine.templates.login.form'))
@endsection

@section('js')
  <script>
    $(document).ready(function() {
      $(".toggle-password").click(function() {
        $('i', $(this)).toggleClass("fa-eye fa-eye-slash");
        const input = $('#password');
        input.attr("type", input.attr("type") === "password" ? "text" : "password");
      });
    });
  </script>
@endsection
