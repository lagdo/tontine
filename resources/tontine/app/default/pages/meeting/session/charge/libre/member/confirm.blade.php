      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="bill-all-form">
          <div class="module-body">
@if (!$charge->has_amount)
            <div class="form-group row">
              {!! $html->label(__('common.labels.amount'), 'amount')->class('col-sm-3 col-form-label') !!}
              <div class="col-md-8">
                {!! $html->text('amount', '')->class('form-control') !!}
              </div>
            </div>
@endif
          </div>
        </form>
      </div>
