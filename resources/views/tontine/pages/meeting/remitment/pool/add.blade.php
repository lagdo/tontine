      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="remitment-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('payable', __('meeting.labels.member'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-8">
                {!! Form::select('payable', $members, 0, ['class' => 'form-control']) !!}
              </div>
            </div>
@if (!$pool->remit_fixed)
            <div class="form-group row">
              {!! Form::label('amount', __('meeting.remitment.labels.amount'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-5">
                {!! Form::text('amount', '', ['class' => 'form-control']) !!}
              </div>
            </div>
@endif
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
