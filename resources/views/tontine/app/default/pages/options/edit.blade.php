      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="options-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('reports[template]', __('tontine.options.labels.report.template'),
                ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-8">
                {!! Form::select('reports[template]', $templates, $template, ['class' => 'form-control']) !!}
              </div>
            </div>
          </div>
        </form>
      </div>
