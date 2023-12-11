      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="closing-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('amount', __('common.labels.amount'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-5">
                {!! Form::text('amount', $hasClosing ? $profitAmount : '', ['class' => 'form-control']) !!}
              </div>
            </div>
          </div>
        </form>
      </div>
