      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="tontine-form">
          <div class="module-body">
            <div class="form-group row">
              <div class="col-11">{{ __('tontine.descriptions.types.help') }}</div>
            </div>
            <div class="form-group row">
              <div class="col-11">
@foreach ($types as $type => $label)
                <div class="form-check">
                  {!! Form::radio('type', $type, $type === 'l', ['class' => 'form-check-input', 'id' => 'tontine_type_' . $type]) !!}
                  {!! Form::label('type', $label . ': ' . $descriptions[$type], ['class' => 'form-check-label']) !!}
                </div>
@endforeach
              </div>
            </div>
          </div>
        </form>
      </div>
