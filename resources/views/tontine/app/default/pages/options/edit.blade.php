      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="options-form">
          <div class="module-body">
            <div class="form-group row">
              {!! $htmlBuilder->label(__('tontine.options.labels.report.template'), 'reports[template]')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $htmlBuilder->select('reports[template]', $templates, $template)->class('form-control') !!}
              </div>
            </div>
          </div>
        </form>
      </div>
