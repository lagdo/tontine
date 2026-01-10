                        <div class="dropdown float-right">
                          <button class="btn btn-primary dropdown-toggle {{ $btnSize ?? 'btn-sm'
                            }}" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            @isset($btnText) {{ $btnText }} @else <i class="fa {{ $btnIcon ?? 'fa-bars' }}"></i> @endisset
                          </button>
                          <div @isset($dataIdKey) {{ $dataIdKey }}="{{ $dataIdValue }}" @endif class="dropdown-menu dropdown-menu-right">
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
