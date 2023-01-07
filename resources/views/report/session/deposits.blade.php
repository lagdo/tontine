          <div class="row align-items-center">
            <div class="col-auto">
              <h6 class="section-title mt-0">{!! __('meeting.titles.deposits') !!}</h6>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>{!! __('common.labels.title') !!}</th>
                  <th>&nbsp;</th>
                  <th>&nbsp;</th>
                  <th>&nbsp;</th>
                </tr>
              </thead>
              <tbody>
@foreach($pools as $pool)
@if($session->disabled($pool))
                <tr style="background-color:rgba(0, 0, 0, 0.02)">
                  <td>{{ $pool->title }}</td>
                  <td>{{ $pool->money('amount') }}</td>
                  <td></td>
                  <td></td>
                </tr>
@else
                <tr>
                  <td>{{ $pool->title }}</td>
                  <td>{{ $pool->money('amount') }}</td>
                  <td>{{ $pool->recv_paid }}/{{ $pool->recv_count }}</td>
                  <td>{{ $summary[$pool->id] ?? 0 }}</td>
                </tr>
@endif
@endforeach
                <tr>
                  <th colspan="3">{!! __('common.labels.total') !!}</th>
                  <th>{{ $sum }}</th>
                </tr>
              </tbody>
            </table>
          </div> <!-- End table -->
