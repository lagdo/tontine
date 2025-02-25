      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="session-form">
          <div class="module-body">
            <div class="form-group row">
              {!! $html->label(__('common.labels.title'), 'title')->class('col-sm-3 col-form-label') !!}*
              <div class="col-sm-8">
                {!! $html->text('title', $session->title)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('tontine.session.labels.host'), 'host_id')->class('col-sm-3 col-form-label') !!}
              <div class="col-md-8">
                {!! $html->select('host_id', $members, $session->host_id)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('common.labels.date'), 'date')->class('col-sm-3 col-form-label') !!}*
              <div class="col-sm-6">
                {!! $html->date('date', $session->start_at)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('tontine.session.labels.times'), 'start')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-4">
                {!! $html->time('start', $session->start_at)->class('form-control') !!}
              </div>
              <div class="col-sm-4">
                {!! $html->time('end', $session->end_at)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('common.labels.notes'), 'notes')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $html->textarea('notes', $session->notes)->class('form-control') !!}
              </div>
            </div>
          </div>
        </form>
      </div>
