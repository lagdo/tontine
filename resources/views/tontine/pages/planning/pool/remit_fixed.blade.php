      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="tontine-form">
          <div class="module-body">
            <div class="form-group row">
              <div class="col-11">{{ __('tontine.pool.help.remit.fixed') }}</div>
            </div>
            <div class="form-group row">
              <div class="col-11">
                <div class="form-check">
                  {!! Form::checkbox('fixed', '1', $fixed, ['id' => 'pool_remit_fixed']) !!}
                  {!! Form::label('fixed', __('tontine.pool.labels.remit.fixed'), ['class' => 'form-check-label']) !!}
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
