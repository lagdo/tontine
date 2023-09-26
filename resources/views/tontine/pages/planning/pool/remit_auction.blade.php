      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="tontine-form">
          <div class="module-body">
            <div class="form-group row">
              <div class="col-11">{{ __('tontine.pool.help.remit.auction') }}</div>
            </div>
            <div class="form-group row">
              <div class="col-11">
                <div class="form-check">
                  {!! Form::checkbox('auction', '1', $auction, ['id' => 'pool_remit_auction']) !!}
                  {!! Form::label('auction', __('tontine.pool.labels.remit.auction'), ['class' => 'form-check-label']) !!}
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
