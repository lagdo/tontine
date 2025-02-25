      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="tontine-form">
          <div class="module-body">
            <div class="form-group row">
              <div class="col-11">{{ __('tontine.pool.help.deposit.lendable') }}</div>
            </div>
            <div class="form-group row">
              <div class="col-11">
                <div class="form-check">
                  {!! $html->checkbox('lendable', $lendable, '1')->id('pool_deposit_lendable') !!}
                  {!! $html->label(__('tontine.pool.labels.deposit.lendable'), 'lendable')->class('form-check-label') !!}
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
