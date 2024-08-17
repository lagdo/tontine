      <div class="portlet-body form">
        <form>
          <div class="form-group">
            {!! $htmlBuilder->label(__('common.labels.title'), 'notes') !!}
            {!! $htmlBuilder->textarea('notes', $notes)->class('form-control')->id('text-notes') !!}
          </div>
        </form>
      </div>
