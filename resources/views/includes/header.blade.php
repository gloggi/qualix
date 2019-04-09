<nav class="container navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="/"><img class="avatar-small" src="{{ asset('images/was-gaffsch.svg') }}" />Qualix</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        @auth
            <ul class="navbar-nav">
                @if($kurs)
                    <li>
                        <select class="custom-select" onchange="window.location = this.value">
                            @foreach(Auth::user()->kurse as $k)
                                <option value="{{ route('index', ['kurs' => $k->id]) }}"{{ $kurs->id === $k->id ? ' selected' : '' }}>{{ $k->name }}</option>
                            @endforeach
                        </select>
                    </li>
                    <li class="nav-item{{ Route::currentRouteName() == 'bloecke' ? ' active' : '' }}">
                        <a class="nav-link" href="{{ route('bloecke', ['kurs' => $kurs->id]) }}">Blöck</a>
                    </li>
                    <li class="nav-item{{ Route::currentRouteName() == 'tn' ? ' active' : '' }}">
                        <a class="nav-link" href="{{ route('tn', ['kurs' => $kurs->id]) }}">TN</a>
                    </li>
                    <li class="nav-item{{ Route::currentRouteName() == 'ma' ? ' active' : '' }}">
                        <a class="nav-link" href="{{ route('ma', ['kurs' => $kurs->id]) }}">Mindestanforderungen</a>
                    </li>
                    <li class="nav-item{{ Route::currentRouteName() == 'tagesspick' ? ' active' : '' }}">
                        <a class="nav-link" href="{{ route('tagesspick', ['kurs' => $kurs->id]) }}">Tagesspick</a>
                    </li>
                    <li class="nav-item dropdown{{ substr( Route::currentRouteName(), 0, 5 ) == 'admin' ? ' active' : '' }}">
                        <a class="nav-link dropdown-toggle" id="navbarKursadmin" role="button"
                           data-toggle="dropdown"
                           aria-haspopup="true" aria-expanded="false">
                            Kursadmin
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item{{ Route::currentRouteName() == 'admin.kurs' ? ' active' : '' }}"
                               href="{{ route('admin.kurs', ['kurs' => $kurs->id]) }}">Kursdetails</a>
                            <a class="dropdown-item{{ Route::currentRouteName() == 'admin.equipe' ? ' active' : '' }}"
                               href="{{ route('admin.equipe', ['kurs' => $kurs->id]) }}">Equipe</a>
                            <a class="dropdown-item{{ Route::currentRouteName() == 'admin.tn' ? ' active' : '' }}"
                               href="{{ route('admin.tn', ['kurs' => $kurs->id]) }}">TN</a>
                            <a class="dropdown-item{{ Route::currentRouteName() == 'admin.bloecke' ? ' active' : '' }}"
                               href="{{ route('admin.bloecke', ['kurs' => $kurs->id]) }}">Blöck</a>
                            <a class="dropdown-item{{ Route::currentRouteName() == 'admin.ma' ? ' active' : '' }}"
                               href="{{ route('admin.ma', ['kurs' => $kurs->id]) }}">Mindestanforderungen</a>
                            <a class="dropdown-item{{ Route::currentRouteName() == 'admin.qk' ? ' active' : '' }}"
                               href="{{ route('admin.qk', ['kurs' => $kurs->id]) }}">Qualikategorien</a>
                        </div>
                    </li>
                @endif
                <li class="nav-item{{ Route::currentRouteName() == 'admin.neuerkurs' ? ' active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.neuerkurs') }}">Neuen Kurs erstellen</a>
                </li>
            </ul>
        @endauth
        <ul class="nav navbar-nav navbar-right ml-auto align-items-center-lg">
            @auth
                <li class="nav-item{{ Route::currentRouteName() == 'benutzer' ? ' active' : '' }}">
                    <a class="nav-link" href="{{ route('user') }}">
                        Willkommä, {{ Auth::user()->name }}
                        <img class="avatar-small" src="{{ Auth::user()->bild_url ?: "http://rs775.pbsrc.com/albums/yy35/PhoenyxStar/link-1.jpg~c200" }}">
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Uslogge
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            @else
                <li>
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                </li>
                <li>
                    <a class="nav-link" href="{{ route('register') }}">Registriere</a>
                </li>
            @endif
        </ul>
    </div>
</nav>
