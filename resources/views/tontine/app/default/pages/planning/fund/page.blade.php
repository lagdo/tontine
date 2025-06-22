@php
  $defId = jq()->parent()->attr('data-def-id')->toInt();
  $fundId = jq()->parent()->attr('data-fund-id')->toInt();
  $rqFundFunc = rq(Ajax\App\Planning\Fund\FundFunc::class);
  $rqFundPage = rq(Ajax\App\Planning\Fund\FundPage::class);
  $rqSession = rq(Ajax\App\Planning\Fund\Session::class);
@endphp
                  <div class="table-responsive" id="content-fund-page" @jxnEvent([
                    ['.btn-fund-sessions', 'click', $rqSession->fund($fundId)],
                    ['.btn-fund-enable', 'click', $rqFundFunc->enable($defId)],
                    ['.btn-fund-disable', 'click', $rqFundFunc->disable($defId)
                      ->confirm(__('tontine.fund.questions.disable'))]])>

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
  $toggleClass = $def->funds_count > 0 ? 'btn-fund-disable' : 'btn-fund-enable';
  $toggleIcon = $def->funds_count > 0 ? 'fa fa-toggle-on' : 'fa fa-toggle-off';
@endphp
                        <tr>
                          <td>{!! $def->type_user ? $def->title : __('tontine.fund.labels.default') !!}</td>
                          <td class="table-item-toggle" data-def-id="{{ $def->id }}">
@if ($def->type_user)
                            <a role="link" tabindex="0" class="{{ $toggleClass }}"><i class="{{ $toggleIcon }}"></i></a>
@else
                            <i class="{{ $toggleIcon }}"></i>
@endif
@if ($def->funds_in_round_count > $def->funds_count)
                            <i class="fa fa-compress-alt fa-rotate-by rotate-by-45deg"></i>
@endif
                          </td>
                          <td class="table-item-menu">
@if($def->funds_count > 0)
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
