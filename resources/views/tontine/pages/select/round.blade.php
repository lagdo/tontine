      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="select-round-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('round_id', __('tontine.labels.round'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-8">
                {!! Form::select('round_id', $rounds, 0, ['class' => 'form-control', 'id' => 'round_id']) !!}
              </div>
            </div>
        </form>
      </div>
