@php
  $formValues = Jaxon\form('guest-guild-access-form');
  $rqHostAccessFunc = rq(Ajax\App\Admin\User\Host\AccessFunc::class);
@endphp
                <div class="section-body">
                  <div class="row mb-2">
                    <div class="col-auto">
                      <h2 class="section-title">{!! $guild->name !!}</h2>
                    </div>
                    <div class="col-auto ml-auto">
                      <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqHostAccessFunc->saveAccess($formValues))><i class="fa fa-save"></i></button>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="card shadow">
                  <div class="card-body">
                    <form class="form-horizontal" role="form" id="guest-guild-access-form">
                      <div class="row">
@foreach (['admin', 'round'] as $page)
                        <div class="col-md-6 col-sm-12">
@php
  $sections = config("tontine.access.$page");
@endphp
                          <div class="module-body">
@foreach ($sections as $section => $entries)
                            <div class="form-group row">
                              <div class="col-md-12">{{ __(config("tontine.access.labels.$section")) }}</div>
@foreach ($entries as $entry)
                              <div class="col-md-11 offset-md-1">
                                {!! $html->checkbox("access[$section][$entry]", $access[$section][$entry] ?? false, '1') !!}
                                {!! $html->label(__(config("tontine.access.labels.{$section}_{$entry}")), '')->class('form-check-label') !!}
                              </div>
@endforeach
                            </div>
@endforeach
                          </div>
                        </div>
@endforeach
                      </div>
                    </form>
                  </div>
                </div>
