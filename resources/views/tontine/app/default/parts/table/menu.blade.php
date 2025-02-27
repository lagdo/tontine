                        <div class="dropdown float-right">
                          <button class="btn btn-primary {{ $btnSize ?? 'btn-sm' }} dropdown-toggle" type="button" id="{{ $dataIdKey }}-{{
                            $dataIdValue }}-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-bars"></i>
                          </button>
                          <div {{ $dataIdKey }}="{{ $dataIdValue }}" class="dropdown-menu" aria-labelledby="{{
                            $dataIdKey }}-{{ $dataIdValue }}-menu">
@foreach ($menus ?? [] as $menu)
@if(!$menu)
                            <div class="dropdown-divider"></div>
@else
                            <button class="dropdown-item {{ $menu['class'] }}" type="button">{!! $menu['text'] !!}</button>
@endif
@endforeach
@foreach ($links ?? [] as $link)
@if(!$link)
                            <div class="dropdown-divider"></div>
@else
                            <a class="dropdown-item" href="{!! $link['url'] !!}" target="_blank">{!! $link['text'] !!}</a>
@endif
@endforeach
                          </div>
                        </div>
