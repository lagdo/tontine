                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.name') !!}</th>
                          <th>{!! __('common.labels.paid') !!}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($bills as $bill)
                        <tr>
                          <td>{{ $bill->member->name }}@if ($charge->is_fine) <br/>{{ $bill->session->title }} @endif</td>
                          <td data-bill-id="{{ $bill->bill->id }}">
                            {!! paymentLink($bill->bill->settlement, 'settlement', $session->closed) !!}
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav>{!! $pagination !!}</nav>
