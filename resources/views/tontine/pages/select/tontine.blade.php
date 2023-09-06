      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="select-tontine-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('tontine_id', __('tontine.labels.tontine'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-8">
                {!! Form::select('tontine_id', $tontines, $default, ['class' => 'form-control', 'id' => 'tontine_id']) !!}
              </div>
            </div>
        </form>
      </div>
