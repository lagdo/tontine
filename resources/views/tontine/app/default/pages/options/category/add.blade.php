      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="category-form">
          <div class="module-body">
            <div class="form-group row">
              {!! $htmlBuilder->label(__('common.labels.name'), 'name')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $htmlBuilder->text('name', '')->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $htmlBuilder->label(__('common.labels.type'), 'type')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $htmlBuilder->select('item_type', $types, '')->class('form-control') !!}
              </div>
            </div>
          </div>
        </form>
      </div>
