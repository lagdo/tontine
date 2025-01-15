      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="select-round-form">
          <div class="module-body">
            <div class="form-group row">
              {!! $htmlBuilder->label(__('tontine.labels.round'), 'round_id')
                ->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $htmlBuilder->select('round_id', $rounds, $current)
                  ->class('form-control')->id('round_id') !!}
              </div>
            </div>
        </form>
      </div>
