                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.title') !!}</th>
                          <th>&nbsp;</th>
                          <th>&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($fines as $charge)
@if($session->closed)
                        @include('tontine.pages.meeting.charge.closed', compact('charge', 'amounts', 'zero'))
@elseif($session->pending)
                        @include('tontine.pages.meeting.charge.pending', compact('charge'))
@else
                        @include('tontine.pages.meeting.charge.fine', compact('charge'))
@endif
@endforeach
                      </tbody>
                    </table>
                    {!! $pagination !!}
