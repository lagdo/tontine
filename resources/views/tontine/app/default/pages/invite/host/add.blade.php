      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="invite-form">
          <div class="module-body">
            <div class="form-group row">
              <div class="col-md-11">{!! __('tontine.invite.titles.add_desc') !!}</div>
            </div>
            <div class="form-group row">
              {!! Form::label('email', __('common.labels.email'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-md-8">
                {!! Form::text('email', '', ['class' => 'form-control']) !!}
              </div>
            </div>
          </div>
        </form>
      </div>
