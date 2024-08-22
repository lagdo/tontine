                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.title') !!}</th>
                          <th>&nbsp;</th>
                          <th>&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($charges as $charge)
@if($session->pending)
                        @include('tontine.app.default.pages.meeting.summary.charge.pending', compact('charge', 'bills', 'settlements'))
@else
                        @include('tontine.app.default.pages.meeting.summary.charge.fixed.item', compact('charge', 'bills', 'settlements'))
@endif
@endforeach
                      </tbody>
                    </table>
                    <nav>{!! $pagination !!}</nav>
