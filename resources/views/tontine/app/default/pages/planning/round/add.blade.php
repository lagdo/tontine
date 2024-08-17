      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="round-form">
          <div class="module-body">
            <div class="form-group row">
              {!! $htmlBuilder->label(__('common.labels.title'), 'title')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $htmlBuilder->text('title', '')->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $htmlBuilder->label(__('common.labels.notes'), 'notes')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $htmlBuilder->text('notes', '')->class('form-control') !!}
              </div>
            </div>
          </div>
        </form>
      </div>
