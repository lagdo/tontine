@php
  $sessionId = pm()->select('select-session')->toInt();
  $memberId = jq()->parent()->attr('data-member-id')->toInt();
  $rqPayable = rq(Ajax\App\Meeting\Payment\Payable::class);
  $rqPaymentPage = rq(Ajax\App\Meeting\Payment\PaymentPage::class);
@endphp
                  <div class="table-responsive" id="content-payment-page" @jxnTarget()>
@if ($sessions->count() > 0)
                    <div @jxnEvent(['.btn-member-payables', 'click'], $rqPayable->show($memberId, $sessionId))></div>
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
