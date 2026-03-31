@php
  $defId = jq()->parent()->attr('data-def-id')->toInt();
  $rqMemberFunc = rq(Ajax\App\Planning\Member\MemberFunc::class);
  $rqMemberPage = rq(Ajax\App\Planning\Member\MemberPage::class);
@endphp
                  <div class="table-responsive" id="content-planning-member-page" @jxnEvent([
                    ['.btn-member-enable', 'click', $rqMemberFunc->enable($defId)],
                    ['.btn-member-disable', 'click', $rqMemberFunc->disable($defId)
                      ->confirm(__('tontine.member.questions.disable'))]])>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.name') !!}</th>
                          <th class="table-menu">&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($defs as $def)
@php
  $toggleClass = $def->members_count > 0 ? 'btn-member-disable' : 'btn-member-enable';
  $toggleIcon = $def->members_count > 0 ? 'fa fa-toggle-on' : 'fa fa-toggle-off';
@endphp
                        <tr>
                          <td>{!! $def->name !!}</td>
                          <td class="table-item-toggle" data-def-id="{{ $def->id }}">
                            <a role="link" tabindex="0" class="{{ $toggleClass }}"><i class="{{ $toggleIcon }}"></i></a>
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqMemberPage)>
                    </nav>
                  </div> <!-- End table -->
