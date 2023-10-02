      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="session-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('title', __('common.labels.title'), ['class' => 'col-sm-3 col-form-label text-right']) !!}*
              <div class="col-sm-8">
                {!! Form::text('title', $session->title, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('host_id', __('tontine.session.labels.host'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-md-8">
                {!! Form::select('host_id', $members, $session->host_id, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('date', __('common.labels.date'), ['class' => 'col-sm-3 col-form-label text-right']) !!}*
              <div class="col-sm-6">
                {!! Form::date('date', $session->start_at, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('start', __('tontine.session.labels.times'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-4">
                {!! Form::time('start', $session->start_at, ['class' => 'form-control']) !!}
              </div>
              <div class="col-sm-4">
                {!! Form::time('end', $session->end_at, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('notes', __('common.labels.notes'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-8">
                {!! Form::textarea('notes', $session->notes, ['class' => 'form-control']) !!}
              </div>
            </div>
          </div>
        </form>
      </div>
