      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="fund-amount-form">
          <div class="module-body">
            <div class="form-group row">
              {!! $html->label(__('common.labels.amount'), 'amount')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-5">
                {!! $html->text('amount', $locale->getMoneyValue($amount))->class('form-control') !!}
              </div>
            </div>
          </div>
        </form>
      </div>
