      <div class="portlet-body form">
        <form class="form-horizontal" role="form">
          <div class="module-body">
            <div class="form-group row">
              {!! $html->label(__('number.charge.type'), 'group')->class('col-md-8 col-form-label') !!}
              <div class="col-md-4">
                {!! $html->select('group', $groups, 0)->class('form-control')->id('charge-group') !!}
              </div>
            </div>
          </div>
        </form>
      </div>
