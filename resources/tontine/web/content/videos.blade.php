@foreach ($videos as $name => $part)
      <section id="{{ $name }}" class="wrapper style2 spotlights">
        <section>
          <div class="content">
            <div class="inner">
              <h2>{{ $part['title'] }}</h2>
@foreach ($part['items'] as $item)
              <p>{{ $item['title'] }}</p>
              <p>
                <iframe width="560" height="315" src="{{ $item['url'] }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
              </p>
@endforeach
            </div>
          </div>
        </section>
      </section>
@endforeach
