      <div class="portlet-body form">
        <form id="session-form">
          <div class="form-group row">
            {!! $html->label(__('tontine.session.labels.address'), 'venue') !!}
            {!! $html->textarea('venue', $venue)->class('form-control')->id('text-session-venue') !!}
          </div>
          <div class="form-group row">
            {!! $html->label(__('common.labels.notes'), 'notes') !!}
            {!! $html->textarea('notes', $session->notes)->class('form-control')->id('text-session-notes') !!}
          </div>
        </form>
      </div>
