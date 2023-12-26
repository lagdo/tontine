      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="remitment-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('payable', __('meeting.remitment.labels.beneficiary'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-8">
                {!! Form::select('payable', $members, $payableId, ['class' => 'form-control']) !!}
              </div>
            </div>
@if ($pool->remit_auction)
            <div class="form-group row">
              {!! Form::label('auction', __('meeting.remitment.labels.auction'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-5">
                {!! Form::text('auction', '', ['class' => 'form-control']) !!}
              </div>
            </div>
@endif
          </div>
        </form>
      </div>
