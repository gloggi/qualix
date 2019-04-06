<nav class="container navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="/"><img class="avatar-small" src="{{ asset('images/was-gaffsch.svg') }}" />Qualix</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        @auth
            <ul class="navbar-nav">
                @if(Auth::user()->kurse()->count())
                    <li>
                        @component('components.form', ['route' => 'admin.kurs.select'])
                            <select name="kursId" class="custom-select" onchange="this.form.submit()">
                                @foreach(Auth::user()->kurse as $kurs)
                                    <option value="{{ $kurs->id }}"{{ Auth::user()->current_kurs->id === $kurs->id ? ' selected' : '' }}>{{ $kurs->name }}</option>
                                @endforeach
                            </select>
                        @endcomponent
                    </li>
                    <li class="nav-item{{ Route::currentRouteName() == 'bloecke' ? ' active' : '' }}">
                        <a class="nav-link" href="{{ route('bloecke') }}">Blöck</a>
                    </li>
                    <li class="nav-item{{ Route::currentRouteName() == 'tn' ? ' active' : '' }}">
                        <a class="nav-link" href="{{ route('tn') }}">TN</a>
                    </li>
                    <li class="nav-item{{ Route::currentRouteName() == 'ma' ? ' active' : '' }}">
                        <a class="nav-link" href="{{ route('ma') }}">Mindestaforderige</a>
                    </li>
                    <li class="nav-item{{ Route::currentRouteName() == 'tagesspick' ? ' active' : '' }}">
                        <a class="nav-link" href="{{ route('tagesspick') }}">Tagesspick</a>
                    </li>
                    <li class="nav-item dropdown{{ substr( Route::currentRouteName(), 0, 5 ) == 'admin' ? ' active' : '' }}">
                        <a class="nav-link dropdown-toggle" id="navbarKursadmin" role="button"
                           data-toggle="dropdown"
                           aria-haspopup="true" aria-expanded="false">
                            Kursadmin
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item{{ Route::currentRouteName() == 'admin.kurs' ? ' active' : '' }}"
                               href="{{ route('admin.kurs') }}">Kursdetails</a>
                            <a class="dropdown-item{{ Route::currentRouteName() == 'admin.equipe' ? ' active' : '' }}"
                               href="{{ route('admin.equipe') }}">Equipe</a>
                            <a class="dropdown-item{{ Route::currentRouteName() == 'admin.tn' ? ' active' : '' }}"
                               href="{{ route('admin.tn') }}">TN</a>
                            <a class="dropdown-item{{ Route::currentRouteName() == 'admin.bloecke' ? ' active' : '' }}"
                               href="{{ route('admin.bloecke') }}">Blöck</a>
                            <a class="dropdown-item{{ Route::currentRouteName() == 'admin.ma' ? ' active' : '' }}"
                               href="{{ route('admin.ma') }}">Mindestaforderige</a>
                            <a class="dropdown-item{{ Route::currentRouteName() == 'admin.qk' ? ' active' : '' }}"
                               href="{{ route('admin.qk') }}">Qualikategorien</a>
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
