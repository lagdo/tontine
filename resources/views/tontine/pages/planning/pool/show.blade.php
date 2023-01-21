      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="campaign-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('title', trans('common.labels.title'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-8">
                {!! Form::text('title', $campaign->title, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('start_at', trans('common.labels.dates'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-4">
                {!! Form::text('start_at', $campaign->start_at, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
              </div>
              <div class="col-sm-4">
                {!! Form::text('end_at', $campaign->end_at, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('numbers', trans('common.labels.numbers'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-9">
                {!! Form::text('numbers', $campaign->raw_numbers, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
              </div>
            </div>
            <div class="form-group row">
              <div class="col-sm-12">
                <ul class="nav nav-tabs">
@foreach ($locales as $locale => $properties)
                  <li class="nav-item">
                    <a class="nav-link @if ($loop->first)active @endif" href="#locale_{{ $locale
                      }}" data-toggle="tab">{{ $properties['native'] }}</a>
                  </li>
@endforeach
                </ul>
              </div>
            </div>
            <div class="tab-content">
@foreach ($locales as $locale => $properties)
              <div class="tab-pane @if ($loop->first)active @endif" id="locale_{{ $locale }}">
                <div class="form-group row">
                  {!! Form::label('label', trans('common.labels.title'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
                  <div class="col-sm-8">
                    {!! Form::text('label', $campaign->translate($locale)->label, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                  </div>
                </div>
                <div class="form-group row">
                  {!! Form::label('description', trans('common.labels.description'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
                  <div class="col-sm-9">
                    {!! Form::text('description', $campaign->translate($locale)->description, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                  </div>
                </div>
              </div>
@endforeach
            </div>
          </div>
        </form>
      </div>
