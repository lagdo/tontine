      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="tontine-form">
          <div class="module-body">
            <div class="form-group row">
              <div class="col-11">{{ __('tontine.pool.help.remit.auction') }}</div>
            </div>
            <div class="form-group row">
              <div class="col-11">
                <div class="form-check">
                  {!! $htmlBuilder->checkbox('auction', $auction, '1')->id('pool_remit_auction') !!}
                  {!! $htmlBuilder->label(__('tontine.pool.labels.remit.auction'), 'auction')->class('form-check-label') !!}
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
