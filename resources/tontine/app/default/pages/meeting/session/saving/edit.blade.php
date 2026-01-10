      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="saving-form">
          <div class="module-body">
            <div class="form-group row">
              {!! $html->label(__('meeting.labels.member'), 'member')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $html->select('member', $members, $saving->member_id)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('common.labels.amount'), 'amount')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-5">
                {!! $html->text('amount', $locale->getMoneyValue($saving->amount))->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('tontine.fund.labels.fund'), 'fund')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $html->select('fund', $funds, $saving->fund_id)->class('form-control') !!}
              </div>
            </div>
          </div>
        </form>
      </div>
