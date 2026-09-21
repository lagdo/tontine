      <div class="portlet-body form">
        <form>
          <div class="form-group">
            {!! $html->label(__('common.labels.title'), 'notes') !!}
            {!! $html->textarea('notes', $notes)->class('form-control')->id('text-notes') !!}
          </div>
        </form>
      </div>
