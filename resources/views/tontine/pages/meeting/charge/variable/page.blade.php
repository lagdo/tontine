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
@if($session->pending)
                        @include('tontine.pages.meeting.charge.pending', compact('charge', 'bills', 'settlements'))
@else
                        @include('tontine.pages.meeting.charge.variable.item', compact('charge', 'bills', 'settlements'))
@endif
@endforeach
                      </tbody>
                    </table>
                    {!! $pagination !!}
