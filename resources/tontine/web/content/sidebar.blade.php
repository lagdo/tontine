      <section id="sidebar">
        <div class="inner">
          <nav>
            <ul>
              <li><a href="#intro">Tutoriels</a></li>
@foreach ($videos as $name => $part)
              <li><a href="#{{ $name }}">{{ $part['menu'] }}</a></li>
@endforeach
            </ul>
          </nav>
        </div>
      </section>
