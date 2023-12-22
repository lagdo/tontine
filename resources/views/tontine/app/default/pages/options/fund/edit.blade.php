      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="fund-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('title', __('common.labels.title'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-8">
                {!! Form::text('title', $fund->title, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('notes', __('common.labels.notes'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-8">
                {!! Form::text('notes', $fund->notes, ['class' => 'form-control']) !!}
              </div>
            </div>
          </div>
        </form>
      </div>
