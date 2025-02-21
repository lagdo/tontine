      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="session-form">
          <div class="module-body">
            <div class="form-group row">
              {!! $htmlBuilder->label(__('common.labels.title'), 'title')->class('col-sm-3 col-form-label') !!}*
              <div class="col-sm-8">
                {!! $htmlBuilder->text('title', '')->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $htmlBuilder->label(__('tontine.session.labels.host'), 'host_id')->class('col-sm-3 col-form-label') !!}
              <div class="col-md-8">
                {!! $htmlBuilder->select('host_id', $members, 0)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $htmlBuilder->label(__('common.labels.date'), 'date')->class('col-sm-3 col-form-label') !!}*
              <div class="col-sm-6">
                {!! $htmlBuilder->date('date', '')->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $htmlBuilder->label(__('tontine.session.labels.times'), 'start')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-4">
                {!! $htmlBuilder->time('start', '00:00')->class('form-control') !!}
              </div>
              <div class="col-sm-4">
                {!! $htmlBuilder->time('end', '00:00')->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $htmlBuilder->label(__('common.labels.notes'), 'notes')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $htmlBuilder->textarea('notes', '')->class('form-control') !!}
              </div>
            </div>
          </div>
        </form>
      </div>
