      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="tontine-form">
          <div class="module-body">
            <div class="form-group row">
              <div class="col-11">{{ __('tontine.pool.help.deposit.lendable') }}</div>
            </div>
            <div class="form-group row">
              <div class="col-11">
                <div class="form-check">
                  {!! Form::checkbox('lendable', '1', $lendable, ['id' => 'pool_deposit_lendable']) !!}
                  {!! Form::label('lendable', __('tontine.pool.labels.deposit.lendable'), ['class' => 'form-check-label']) !!}
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
