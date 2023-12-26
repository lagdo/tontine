      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="pool-form">
          <div class="module-body">
            <div class="form-group row">
              <div class="col-md-11">
                {!! Form::checkbox('', '1', $options['deposit']['fixed'], ['disabled' => 'disabled']) !!}
                {!! Form::label('', __('tontine.pool.labels.deposit.fixed'), ['class' => 'form-check-label']) !!}
              </div>
            </div>
            <div class="form-group row">
              <div class="col-md-11">
                {!! Form::checkbox('', '1', $options['remit']['planned'], ['disabled' => 'disabled']) !!}
                {!! Form::label('', __('tontine.pool.labels.remit.planned'), ['class' => 'form-check-label']) !!}
              </div>
            </div>
            <div class="form-group row">
              <div class="col-md-11">
                {!! Form::checkbox('', '1', $options['remit']['auction'], ['disabled' => 'disabled']) !!}
                {!! Form::label('', __('tontine.pool.labels.remit.auction'), ['class' => 'form-check-label']) !!}
              </div>
            </div>
            <div class="form-group row">
              <div class="col-md-11">
                {!! Form::checkbox('', '1', $options['remit']['lendable'], ['disabled' => 'disabled']) !!}
                {!! Form::label('', __('tontine.pool.labels.remit.lendable'), ['class' => 'form-check-label']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('title', __('common.labels.title'), ['class' => 'col-sm-2 col-form-label text-right']) !!}
              <div class="col-md-9">
                {!! Form::text('title', '', ['class' => 'form-control']) !!}
              </div>
            </div>
@if ($options['deposit']['fixed'])
            <div class="form-group row">
              {!! Form::label('amount', __('common.labels.amount'), ['class' => 'col-sm-2 col-form-label text-right']) !!}
              <div class="col-md-6">
                {!! Form::text('amount', '', ['class' => 'form-control']) !!}
              </div>
            </div>
@else
            {!! Form::hidden('amount', '0') !!}
@endif
            <div class="form-group row">
              {!! Form::label('notes', __('common.labels.notes'), ['class' => 'col-sm-2 col-form-label text-right']) !!}
              <div class="col-md-9">
                {!! Form::textarea('notes', '', ['class' => 'form-control']) !!}
              </div>
            </div>
          </div>
        </form>
      </div>
