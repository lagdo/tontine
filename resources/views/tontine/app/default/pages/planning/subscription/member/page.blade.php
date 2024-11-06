@php
  $memberId = Jaxon\jq()->parent()->parent()->attr('data-member-id')->toInt();
  $rqMember = Jaxon\rq(App\Ajax\Web\Planning\Subscription\Member::class);
  $rqMemberPage = Jaxon\rq(App\Ajax\Web\Planning\Subscription\MemberPage::class);
@endphp
                  <div class="table-responsive" id="pool-subscription-members-page" @jxnTarget()>
                    <div @jxnOn(['.btn-subscription-member-add', 'click', ''], $rqMember->create($memberId))></div>
                    <div @jxnOn(['.btn-subscription-member-del', 'click', ''], $rqMember->delete($memberId)
                      ->confirm(__('tontine.session.questions.disable')))></div>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{{ __('common.labels.name') }}</th>
                          <th>&nbsp;</th>
                        </tr>
                      </thead>
                    <tbody>
@foreach ($members as $member)
                        <tr>
                          <td>{{ $member->name }}</td>
                          <td class="table-item-counter">
                            <div class="input-group float-right" data-member-id="{{ $member->id }}" style="width:auto;">
                              <div class="input-group-prepend">
                                <button type="button" class="btn btn-primary btn-sm btn-subscription-member-del"><i class="fas fa-minus"></i></button>
                              </div>
                              <span class="input-group-text">{{ $member->subscriptions_count }}</span>
                              <div class="input-group-append">
                                <button type="button" class="btn btn-primary btn-sm btn-subscription-member-add"><i class="fas fa-plus"></i></button>
                              </div>
                            </div>
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqMemberPage)>
                    </nav>
                  </div> <!-- End table -->
