      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="round-form">
          <div class="module-body">
            <div class="form-group row">
              {!! $html->label(__('common.labels.title'), 'title')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $html->text('title', $round->title)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('common.labels.notes'), 'notes')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $html->text('notes', $round->notes)->class('form-control') !!}
              </div>
            </div>
          </div>
        </form>
      </div>
