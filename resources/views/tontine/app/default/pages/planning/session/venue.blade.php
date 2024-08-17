      <div class="portlet-body form">
        <form id="session-form">
          <div class="form-group row">
            {!! $htmlBuilder->label(__('tontine.session.labels.address'), 'venue') !!}
            {!! $htmlBuilder->textarea('venue', $venue)->class('form-control')->id('text-session-venue') !!}
          </div>
          <div class="form-group row">
            {!! $htmlBuilder->label(__('common.labels.notes'), 'notes') !!}
            {!! $htmlBuilder->textarea('notes', $session->notes)->class('form-control')->id('text-session-notes') !!}
          </div>
        </form>
      </div>
