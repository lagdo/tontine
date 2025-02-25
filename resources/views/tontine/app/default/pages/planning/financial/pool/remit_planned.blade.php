      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="tontine-form">
          <div class="module-body">
            <div class="form-group row">
              <div class="col-11">{{ __('tontine.pool.help.remit.planned') }}</div>
            </div>
            <div class="form-group row">
              <div class="col-11">
                <div class="form-check">
                  {!! $html->checkbox('planned', $planned, '1')->id('pool_remit_planned') !!}
                  {!! $html->label(__('tontine.pool.labels.remit.planned'), 'planned')->class('form-check-label') !!}
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
