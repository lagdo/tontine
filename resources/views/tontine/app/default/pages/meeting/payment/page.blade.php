@php
  $sessionId = Jaxon\pm()->select('select-session')->toInt();
  $memberId = Jaxon\jq()->parent()->attr('data-member-id')->toInt();
  $rqPayable = Jaxon\rq(App\Ajax\Web\Meeting\Payment\Payable::class);
  $rqPaymentPage = Jaxon\rq(App\Ajax\Web\Meeting\Payment\PaymentPage::class);
@endphp
                  <div class="table-responsive" id="payment-members-page" @jxnTarget()>
@if ($sessions->count() > 0)
                    <div @jxnOn(['.btn-member-payables', 'click', ''], $rqPayable->show($memberId, $sessionId))></div>
@endif

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.name') !!}</th>
                          <th class="table-menu"></th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($members as $member)
                        <tr>
                          <td>{{ $member->name }}</td>
                          <td class="table-item-menu" data-member-id="{{ $member->id }}">
                            <button type="button" class="btn btn-primary btn-member-payables"><i class="fa fa-arrow-circle-right"></i></button>
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqPaymentPage)>
                    </nav>
                  </div> <!-- End table -->
