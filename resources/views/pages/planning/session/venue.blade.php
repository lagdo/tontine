      <div class="portlet-body form">
        <form id="session-form">
          <div class="form-group row">
            {!! Form::label('venue', trans('tontine.session.labels.address')) !!}
            {!! Form::textarea('venue', $venue, ['class' => 'form-control', 'id' => 'text-session-venue']) !!}
          </div>
          <div class="form-group row">
            {!! Form::label('notes', trans('common.labels.notes')) !!}
            {!! Form::textarea('notes', $session->notes, ['class' => 'form-control', 'id' => 'text-session-notes']) !!}
          </div>
        </form>
      </div>
