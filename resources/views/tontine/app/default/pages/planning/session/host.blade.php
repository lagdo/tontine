      <div class="portlet-body form">
        <form id="session-form">
          <div class="form-group row">
            {!! $htmlBuilder->label(__('tontine.session.labels.host'), 'host_id') !!}
            {!! $htmlBuilder->select('host_id', $members, $session->host_id)->class('form-control') !!}
          </div>
          <div class="form-group row">
            {!! $htmlBuilder->label(__('common.labels.notes'), 'notes') !!}
            {!! $htmlBuilder->textarea('notes', $session->notes)->class('form-control')->id('text-session-notes') !!}
          </div>
        </form>
      </div>
