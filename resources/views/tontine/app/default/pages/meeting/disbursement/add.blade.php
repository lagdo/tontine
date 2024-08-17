      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="disbursement-form">
          <div class="module-body">
            <div class="form-group row">
              {!! $htmlBuilder->label(__('common.labels.amount'), 'amount')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-5">
                {!! $htmlBuilder->text('amount', '')->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $htmlBuilder->label(__('meeting.labels.category'), 'category')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $htmlBuilder->select('category', $categories, 0)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $htmlBuilder->label(__('meeting.labels.charge'), 'charge')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $htmlBuilder->select('charge', $charges, 0)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $htmlBuilder->label(__('meeting.labels.member'), 'member')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $htmlBuilder->select('member', $members, 0)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $htmlBuilder->label(__('common.labels.comment'), 'comment')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $htmlBuilder->text('comment', '')->class('form-control') !!}
              </div>
            </div>
          </div>
        </form>
      </div>
