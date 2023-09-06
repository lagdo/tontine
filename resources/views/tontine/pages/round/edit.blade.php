      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="round-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('title', __('common.labels.title'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-8">
                {!! Form::text('title', $round->title, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('dates', __('common.labels.dates'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-4">
                {!! Form::date('start_at', substr($round->start_at, 0, 10), ['class' => 'form-control']) !!}
              </div>
              <div class="col-sm-4">
                {!! Form::date('end_at', substr($round->end_at, 0, 10), ['class' => 'form-control']) !!}
              </div>
            </div>
          </div>
        </form>
      </div>
