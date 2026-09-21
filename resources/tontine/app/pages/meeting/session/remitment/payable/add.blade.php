      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="remitment-form">
          <div class="module-body">
            <div class="form-group row">
              {!! $html->label(__('meeting.remitment.labels.beneficiary'), 'payable')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $html->select('payable', $members, $payableId)->class('form-control') !!}
              </div>
            </div>
@if ($pool->remit_auction)
            <div class="form-group row">
              {!! $html->label(__('meeting.remitment.labels.auction'), 'auction')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-5">
                {!! $html->text('auction', '')->class('form-control') !!}
              </div>
            </div>
@endif
          </div>
        </form>
      </div>
