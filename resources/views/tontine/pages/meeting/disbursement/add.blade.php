      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="disbursement-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('amount', __('common.labels.amount'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-5">
                {!! Form::text('amount', '', ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('category', __('meeting.labels.category'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-8">
                {!! Form::select('category', $categories, 0, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('charge', __('meeting.labels.charge'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-8">
                {!! Form::select('charge', $charges, 0, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('member', __('meeting.labels.member'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-8">
                {!! Form::select('member', $members, 0, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('comment', __('common.labels.comment'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-8">
                {!! Form::text('comment', '', ['class' => 'form-control']) !!}
              </div>
            </div>
          </div>
        </form>
      </div>
