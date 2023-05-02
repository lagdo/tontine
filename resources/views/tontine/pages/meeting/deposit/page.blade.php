                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>{!! __('common.labels.name') !!}</th>
                      <th>{!! __('common.labels.paid') !!}</th>
                    </tr>
                  </thead>
                  <tbody>
@foreach ($receivables as $receivable)
                    <tr>
                      <td>{{ $receivable->subscription->member->name }}</td>
                      <td data-receivable-id="{{ $receivable->id }}">
                        {!! paymentLink($receivable->deposit, 'deposit', $session->closed) !!}
                      </td>
                    </tr>
@endforeach
                  </tbody>
                </table>
                <nav>{!! $pagination !!}</nav>
