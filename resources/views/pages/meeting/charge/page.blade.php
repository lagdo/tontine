                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.title') !!}</th>
                          <th>&nbsp;</th>
                          <th>&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($charges as $charge)
@if($session->closed)
                        @include('pages.meeting.charge.closed', compact('charge', 'session'))
@elseif($session->pending)
                        @include('pages.meeting.charge.pending', compact('charge'))
@elseif($charge->is_fee)
                        @include('pages.meeting.charge.fee', compact('charge'))
@elseif($charge->is_fine)
                        @include('pages.meeting.charge.fine', compact('charge'))
@endif
@endforeach
@if($session->closed)
                        <tr>
                          <td colspan="2">{!! __('common.labels.total') !!}</td>
                          <td>{{ $summary['sum']['settlements'] }}</td>
                        </tr>
@endif
                      </tbody>
                    </table>
                    {!! $pagination !!}
