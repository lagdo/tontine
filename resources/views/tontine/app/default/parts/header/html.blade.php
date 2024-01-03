  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <meta content="{!! csrf_token() !!}" name="csrf-token" />

  <title>@yield('page-title')</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css" integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css" integrity="sha512-6S2HWzVFxruDlZxI3sXOZZ4/eJ8AcxkQH1+JjSe/ONCEqR9L4Ysq5JdT5ipqtzU7WHalNwzwBv+iE51gNHJNqQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Template CSS -->
  <link rel="stylesheet" href="/tpl/assets/css/style.css">
  <link rel="stylesheet" href="/tpl/assets/css/components-v1.4.css">
  <link rel="stylesheet" href="/tpl/assets/css/custom.css">
  <style>
    /* Fix for template style */
    @media (max-width: 575.98px) {
      .table-responsive table {
          min-width: 400px;
      }
    }

    /* Make the Kustomer component appear on top of the page elements */
    .kustomer-feedback-component {
      z-index: 100;
    }
    .kustomer-feedback-component .kustomer-popup .kustomer-header h1 {
      margin-top: 0 !important;
      font-size: 15px !important;
    }
    .kustomer-popup .kustomer-container section.kustomer-form h2 {
      text-transform: none !important;
    }
    /* Hide the feedback popup logo */
    .kustomer-popup .kustomer-header .kustomer-logo {
      display: none;
    }
    .kustomer-feedback-component .kustomer-popup .kustomer-container section.kustomer-form form textarea {
      resize: vertical;
    }
  </style>
