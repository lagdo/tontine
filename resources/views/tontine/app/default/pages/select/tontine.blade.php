      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="select-tontine-form">
          <div class="module-body">
            <div class="form-group row">
              {!! $htmlBuilder->label(__('tontine.labels.tontine'), 'tontine_id')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $htmlBuilder->select('tontine_id', $tontines, $default)->class('form-control')->id('tontine_id') !!}
              </div>
            </div>
        </form>
      </div>
