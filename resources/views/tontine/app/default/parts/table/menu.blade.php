                        <div class="dropdown float-right">
                          <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="{{ $dataIdKey }}-{{
                            $dataIdValue }}-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-bars"></i>
                          </button>
                          <div {{ $dataIdKey }}="{{ $dataIdValue }}" class="dropdown-menu" aria-labelledby="{{
                            $dataIdKey }}-{{ $dataIdValue }}-menu">
@foreach ($menus ?? [] as $menu)
                            <button class="dropdown-item {{ $menu['class'] }}" type="button">{{ $menu['text'] }}</button>
@endforeach
@foreach ($links ?? [] as $link)
                            <a class="dropdown-item" href="{!! $link['url'] !!}" target="_blank">{{ $link['text'] }}</a>
@endforeach
                          </div>
                        </div>
