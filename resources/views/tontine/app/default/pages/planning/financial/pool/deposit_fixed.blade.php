      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="tontine-form">
          <div class="module-body">
            <div class="form-group row">
              <div class="col-11">{{ __('tontine.pool.help.deposit.fixed') }}</div>
            </div>
            <div class="form-group row">
              <div class="col-11">
                <div class="form-check">
                  {!! $html->checkbox('fixed', $fixed, '1')->id('pool_deposit_fixed') !!}
                  {!! $html->label(__('tontine.pool.labels.deposit.fixed'), 'fixed')->class('form-check-label') !!}
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
