      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="category-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('name', __('common.labels.name'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-8">
                {!! Form::text('name', $category->name, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('type', __('common.labels.type'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-8">
                {!! Form::select('item_type', $types, $category->item_type, ['class' => 'form-control']) !!}
              </div>
            </div>
          </div>
        </form>
      </div>
