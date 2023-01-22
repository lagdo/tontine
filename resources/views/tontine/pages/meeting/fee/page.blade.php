                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.title') !!}</th>
                          <th>&nbsp;</th>
                          <th>&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($fees as $charge)
@if($session->closed)
                        @include('tontine.pages.meeting.charge.closed', compact('charge', 'session'))
@elseif($session->pending)
                        @include('tontine.pages.meeting.charge.pending', compact('charge'))
@else
                        @include('tontine.pages.meeting.charge.fee', compact('charge'))
@endif
@endforeach
                      </tbody>
                    </table>
                    {!! $pagination !!}
