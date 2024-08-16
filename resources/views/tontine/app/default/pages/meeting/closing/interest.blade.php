@inject('locale', 'Siak\Tontine\Service\LocaleService')
      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="closing-form">
          <div class="module-body">
            <div class="form-group row">
              <div class="col-sm-11">
                <p class="lead">{!! __('meeting.closing.labels.fund') !!}: {!! $fund->title !!}</p>
              </div>
            </div>
@if( $closing !== null )
            <div class="form-group row">
              <div class="col-sm-11">
                {!! __('meeting.closing.labels.interest') !!}
              </div>
            </div>
@endif
          </div>
        </form>
      </div>
