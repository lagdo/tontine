      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="session-form">
          <div class="module-body">
            <div class="form-group row">
              {!! $html->label(__('common.labels.title'), 'title')->class('col-sm-3 col-form-label') !!}*
              <div class="col-sm-8">
                {!! $html->text('title', '')->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('tontine.session.labels.host'), 'host_id')->class('col-sm-3 col-form-label') !!}
              <div class="col-md-8">
                {!! $html->select('host_id', $members, 0)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('common.labels.date'), 'day_date')->class('col-sm-3 col-form-label') !!}*
              <div class="col-sm-6">
                {!! $html->date('day_date', '')->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('tontine.session.labels.times'), 'start_time')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-4">
                {!! $html->time('start_time', '00:00')->class('form-control') !!}
              </div>
              <div class="col-sm-4">
                {!! $html->time('end_time', '00:00')->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('common.labels.notes'), 'notes')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $html->textarea('notes', '')->class('form-control') !!}
              </div>
            </div>
          </div>
        </form>
      </div>
