      <div class="portlet-body form">
        <form id="session-form">
          <div class="form-group row">
            {!! Form::label('host_id', __('tontine.session.labels.host')) !!}
            {!! Form::select('host_id', $members, $session->host_id, ['class' => 'form-control']) !!}
          </div>
          <div class="form-group row">
            {!! Form::label('notes', __('common.labels.notes')) !!}
            {!! Form::textarea('notes', $session->notes, ['class' => 'form-control', 'id' => 'text-session-notes']) !!}
          </div>
        </form>
      </div>
