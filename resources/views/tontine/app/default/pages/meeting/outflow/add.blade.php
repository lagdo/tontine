      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="outflow-form">
          <div class="module-body">
            <div class="form-group row">
              {!! $html->label(__('common.labels.amount'), 'amount')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-5">
                {!! $html->text('amount', '')->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('meeting.labels.category'), 'category')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $html->select('category', $categories, 0)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('meeting.labels.charge'), 'charge')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $html->select('charge', $charges, 0)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('meeting.labels.member'), 'member')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $html->select('member', $members, 0)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('common.labels.comment'), 'comment')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $html->text('comment', '')->class('form-control') !!}
              </div>
            </div>
          </div>
        </form>
      </div>
