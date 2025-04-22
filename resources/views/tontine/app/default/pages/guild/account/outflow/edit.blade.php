      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="account-form">
          <div class="module-body">
            <div class="form-group row">
              {!! $html->label(__('common.labels.name'), 'name')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $html->text('name', $account->name)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('common.labels.type'), 'type')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $html->select('item_type', $types, $account->item_type)->class('form-control') !!}
              </div>
            </div>
          </div>
        </form>
      </div>
