@php
  $sessionId = jq()->parent()->attr('data-session-id')->toInt();
  $rqSessionFunc = rq(Ajax\App\Planning\Fund\SessionFunc::class);
  $rqSessionPage = rq(Ajax\App\Planning\Fund\SessionPage::class);
  $fundSessionIds = $fund->sessions->pluck('id', 'id');
@endphp
                      <div class="table-responsive" id="content-planning-sessions-page" @jxnEvent([
                        ['.fund-session-enable', 'click', $rqSessionFunc->enableSession($sessionId)],
                        ['.fund-session-disable', 'click', $rqSessionFunc->disableSession($sessionId)
                          ->confirm(__('tontine.session.questions.disable'))]])>

                        <form id="fund-session-form">
                          <table class="table table-bordered responsive">
                            <thead>
                              <tr>
                                <th>{!! __('common.labels.title') !!}</th>
                                <th class="table-menu">{!! __('tontine.session.labels.start') !!}</th>
                                <th class="table-menu">{!! __('tontine.session.labels.end') !!}</th>
                                <th class="table-menu">{!! __('tontine.session.labels.interest') !!}</th>
                              </tr>
                            </thead>
                            <tbody>
@foreach ($sessions as $session)
                              <tr>
                                <td>
                                  <div>{{ $session->title }}</div>
                                  <div>{{ $session->date('day_date') }}</div>
                                </td>
                                <td class="table-item-menu">
                                  {!! $html->radio('start_sid', $session->id === $fund->start_sid, $session->id) !!}
                                </td>
                                <td class="table-item-menu">
                                  {!! $html->radio('end_sid', $session->id === $fund->end_sid, $session->id) !!}
                                </td>
                                <td class="table-item-menu">
@if($fundSessionIds->has($session->id))
                                  {!! $html->radio('interest_sid', $session->id === $fund->interest_sid, $session->id) !!}
@else
                                  &nbsp;
@endif
                                </td>
                              </tr>
@endforeach
                            </tbody>
                          </table>
                        </form>
                        <nav @jxnPagination($rqSessionPage)>
                        </nav>
                      </div> <!-- End table -->
