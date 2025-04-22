@php
  $defId = jq()->parent()->attr('data-def-id')->toInt();
  $fundId = jq()->parent()->attr('data-fund-id')->toInt();
  $rqFundFunc = rq(Ajax\App\Planning\Finance\Fund\FundFunc::class);
  $rqFundPage = rq(Ajax\App\Planning\Finance\Fund\FundPage::class);
  $rqSession = rq(Ajax\App\Planning\Finance\Fund\Session::class);
@endphp
                  <div class="table-responsive" id="content-fund-page" @jxnTarget()>
                    <div @jxnEvent(['.btn-fund-sessions', 'click'], $rqSession->fund($fundId))></div>
                    <div @jxnEvent(['.btn-fund-enable', 'click'], $rqFundFunc->enable($defId))></div>
                    <div @jxnEvent(['.btn-fund-disable', 'click'], $rqFundFunc->disable($defId)
                      ->confirm(__('tontine.fund.questions.disable')))></div>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.title') !!}</th>
                          <th class="table-menu">&nbsp;</th>
                          <th class="table-menu">&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($defs as $def)
@php
  $count = $def->funds->count();
  $toggleClass = $count > 0 ? 'btn-fund-disable' : 'btn-fund-enable';
  $toggleIcon = $count > 0 ? 'fa fa-toggle-on' : 'fa fa-toggle-off';
@endphp
                        <tr>
                          <td>{{ $def->title }}</td>
                          <td class="table-item-toggle" data-def-id="{{ $def->id }}">
                            <a role="link" tabindex="0" class="{{ $toggleClass }}"><i class="{{ $toggleIcon }}"></i></a>
                          </td>
                          <td class="table-item-menu">
@if($count > 0)
@include('tontine::parts.table.menu', [
  'dataIdKey' => 'data-fund-id',
  'dataIdValue' => $def->funds->first()->id,
  'menus' => [[
    'class' => 'btn-fund-sessions',
    'text' => __('tontine.actions.sessions'),
  ]],
])
@endif
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqFundPage)>
                    </nav>
                  </div> <!-- End table -->
