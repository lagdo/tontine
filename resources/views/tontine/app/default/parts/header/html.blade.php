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

<link rel="stylesheet" href="/jaxon/app.3.1.0.css">
<style>
  .table {
    width: 100%;
  }
  .table td.table-item-toggle {
    width: 90px;
  }
@media only screen and (max-width: 700px) {
  .table.responsive,
  .table.responsive tbody {
    display: block;
  }
  .table.responsive tr {
    display: flex;
    flex-direction: column;
    border-top: 1px solid #f6f6f6;
  }
  .table.responsive thead {
    display: none;
  }
  .table.responsive td {
    display: flex;
    align-items: center;
    padding: 0 15px 0 130px !important;
    position: relative;
  }
  .table.responsive td::before {
    padding: 10px;
    content: attr(data-label);
    position: absolute;
    top: 0;
    left: 0;
    width: 120px;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.04);
    color: #666;
    display: flex;
    align-items: center;
    font-weight: bold;
    border-bottom: 1px solid #f6f6f6;
  }
  .table.responsive td.table-item-menu,
  .table.responsive td.table-item-toggle,
  .table.responsive td.table-item-counter,
  .table.responsive td.table-item-currency {
    width: auto;
  }
  .table.responsive td.currency {
    text-align: left;
  }
  .table.responsive tr {
    margin-bottom: 1rem;
  }
  .table.responsive th + td {
    padding-left: 10px;
  }
  .table.responsive td select {
    display: block;
  }
}
</style>

@include('tontine.app.default.parts.header.custom')
