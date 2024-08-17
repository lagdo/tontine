      <div class="portlet-body form">
        <form class="form-horizontal" role="form">
          <div class="module-body">
            <div class="form-group row">
              {!! $htmlBuilder->label(__('number.charge.type'), 'group')->class('col-md-8 col-form-label') !!}
              <div class="col-md-4">
                {!! $htmlBuilder->select('group', $groups, 0)->class('form-control')->id('charge-group') !!}
              </div>
            </div>
          </div>
        </form>
      </div>
