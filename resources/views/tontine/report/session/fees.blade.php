          <div class="row align-items-center">
            <div class="col-auto">
              <h6 class="section-title mt-0">{!! __('meeting.titles.fees') !!}</h6>
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
@foreach ($fees as $charge)
                <tr>
                  <td>{{ $charge->name }}</td>
                  <td>
                    {{ $settlements['total']['current'][$charge->id] ?? 0 }}/{{ $bills['total']['current'][$charge->id] ?? 0 }}
                  </td>
                  <td>{{ $charge->money('amount') }}</td>
                  <td>
                    {{ $settlements['amount']['current'][$charge->id] ?? $zero }}
                  </td>
                </tr>
@endforeach
                <tr>
                  <th colspan="3">{!! __('common.labels.total') !!}</th>
                  <th></th>
                </tr>
              </tbody>
            </table>
          </div> <!-- End table -->
