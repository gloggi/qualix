<nav class="container navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="/"><img class="avatar-small" src="{{ asset('images/was-gaffsch.svg') }}"/>Qualix</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        @if ($navigation)
            <ul class="navbar-nav">
                <li class="nav-item{{ Route::currentRouteName() == 'kitchensink' ? ' active' : '' }}">
                    <a class="nav-link" href="#">Home</a>
                </li>
                <li class="nav-item{{ Route::currentRouteName() == 'bloecke' ? ' active' : '' }}">
                    <a class="nav-link" href="#">Blöck</a>
                </li>
                <li class="nav-item{{ Route::currentRouteName() == 'tn' ? ' active' : '' }}">
                    <a class="nav-link" href="#">TN</a>
                </li>
                <li class="nav-item{{ Route::currentRouteName() == 'ma' ? ' active' : '' }}">
                    <a class="nav-link" href="#">Mindestaforderige</a>
                </li>
                <li class="nav-item{{ Route::currentRouteName() == 'tagesspick' ? ' active' : '' }}">
                    <a class="nav-link" href="#">Tagesspick</a>
                </li>
                <li class="nav-item dropdown{{ substr( Route::currentRouteName(), 0, 5 ) == 'admin' ? ' active' : '' }}">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarKursadmin" role="button"
                       data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                        Kursadmin
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item{{ Route::currentRouteName() == 'admin.kurs' ? ' active' : '' }}"
                           href="#">Kursdetails</a>
                        <a class="dropdown-item{{ Route::currentRouteName() == 'admin.equipe' ? ' active' : '' }}"
                           href="#">Equipe</a>
                        <a class="dropdown-item{{ Route::currentRouteName() == 'admin.tn' ? ' active' : '' }}" href="#">TN</a>
                        <a class="dropdown-item{{ Route::currentRouteName() == 'admin.bloecke' ? ' active' : '' }}"
                           href="#">Blöck</a>
                        <a class="dropdown-item{{ Route::currentRouteName() == 'admin.ma' ? ' active' : '' }}" href="#">Mindestaforderige</a>
                        <a class="dropdown-item{{ Route::currentRouteName() == 'admin.qk' ? ' active' : '' }}" href="#">Qualikategoriä</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item{{ Route::currentRouteName() == 'admin.neuerkurs' ? ' active' : '' }}"
                           href="#">Neue Kurs erstelle</a>
                    </div>
                </li>
                <li>
                    <form>
                        <select class="custom-select">
                            <option value="1234" selected>Ufbau 2019</option>
                            <option value="1234">Basiskurs '18</option>
                            <option disabled>──────────</option>
                            <option value="">Neue Kurs erstelle</option>
                        </select>
                    </form>
                </li>
            </ul>
        @endif
        <ul class="nav navbar-nav navbar-right ml-auto">
            {{--@auth--}}
                <li class="nav-item{{ Route::currentRouteName() == 'benutzer' ? ' active' : '' }}">
                    <a class="nav-link" href="#">
                        Willkommä, @auth{{ Auth::user()->name --}}@else Link @endauth
                        <img class="avatar-small" src="http://rs775.pbsrc.com/albums/yy35/PhoenyxStar/link-1.jpg~c200">
                    </a>
                </li>
            {{--@else
                @if (Route::has('login'))
                    <li>
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                @endif
                @if (Route::has('register'))
                    <li>
                        <a class="nav-link" href="{{ route('register') }}">Registriere</a>
                    </li>
                @endif
            @endif--}}
        </ul>
    </div>
</nav>
