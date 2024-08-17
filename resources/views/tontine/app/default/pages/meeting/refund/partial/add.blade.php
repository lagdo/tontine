      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="refund-form">
          <div class="module-body">
            <div class="form-group row">
              {!! $htmlBuilder->label(__('meeting.labels.debt'), 'debt')->class('col-sm-2 col-form-label') !!}
              <div class="col-sm-10">
                {!! $htmlBuilder->select('debt', $debts, 0)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $htmlBuilder->label(__('common.labels.amount'), 'amount')->class('col-sm-2 col-form-label') !!}
              <div class="col-sm-5">
                {!! $htmlBuilder->text('amount', '')->class('form-control') !!}
              </div>
            </div>
          </div>
        </form>
      </div>
