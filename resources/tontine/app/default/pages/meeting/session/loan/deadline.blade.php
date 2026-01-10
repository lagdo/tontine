@php
  $deadline = $loan->deadline_session !== null ?
    $loan->deadline_session->day_date : ($loan->deadline_date ?? '');
@endphp
      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="loan-edit-deadline">
          <div class="module-body">
            <div class="form-group row">
              {!! $html->label(__('common.labels.date'), 'deadline')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-7">
                {!! $html->date('deadline', $deadline)->class('form-control') !!}
              </div>
            </div>
          </div>
        </form>
      </div>
