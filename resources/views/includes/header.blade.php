<nav class="container navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="/"><img class="avatar-small" src="{{ asset('images/was-gaffsch.svg') }}" />Qualix</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        @auth
            <ul class="navbar-nav">
                @if($course)
                    <li>
                        <select class="custom-select" onchange="window.location = this.value">
                            @foreach(Auth::user()->courses as $k)
                                <option value="{{ route('index', ['course' => $k->id]) }}"{{ $course->id === $k->id ? ' selected' : '' }}>{{ $k->name }}</option>
                            @endforeach
                        </select>
                    </li>
                    <li class="nav-item{{ Route::currentRouteName() == 'blocks' ? ' active' : '' }}">
                        <a class="nav-link" href="{{ route('blocks', ['course' => $course->id]) }}">Blöcke</a>
                    </li>
                    @if(!$course->archived)
                        <li class="nav-item{{ Route::currentRouteName() == 'participants' ? ' active' : '' }}">
                            <a class="nav-link" href="{{ route('participants', ['course' => $course->id]) }}">TN</a>
                        </li>
                        <li class="nav-item{{ Route::currentRouteName() == 'overview' ? ' active' : '' }}">
                            <a class="nav-link" href="{{ route('overview', ['course' => $course->id]) }}">Überblick</a>
                        </li>
                    @endif
                    <li class="nav-item dropdown{{ substr( Route::currentRouteName(), 0, 5 ) == 'admin' ? ' active' : '' }}">
                        <a class="nav-link dropdown-toggle" id="navbarCourseAdmin" role="button"
                           data-toggle="dropdown"
                           aria-haspopup="true" aria-expanded="false">
                            Kursadmin
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item{{ Route::currentRouteName() == 'admin.course' ? ' active' : '' }}"
                               href="{{ route('admin.course', ['course' => $course->id]) }}">{{__('Kurseinstellungen')}}</a>
                            <a class="dropdown-item{{ Route::currentRouteName() == 'admin.equipe' ? ' active' : '' }}"
                               href="{{ route('admin.equipe', ['course' => $course->id]) }}">Equipe</a>
                            <a class="dropdown-item{{ Route::currentRouteName() == 'admin.requirements' ? ' active' : '' }}"
                               href="{{ route('admin.requirements', ['course' => $course->id]) }}">Mindestanforderungen</a>
                            <a class="dropdown-item{{ Route::currentRouteName() == 'admin.categories' ? ' active' : '' }}"
                               href="{{ route('admin.categories', ['course' => $course->id]) }}">Kategorien</a>
                            <a class="dropdown-item{{ Route::currentRouteName() == 'admin.blocks' ? ' active' : '' }}"
                               href="{{ route('admin.blocks', ['course' => $course->id]) }}">Blöcke</a>
                            @if(!$course->archived)
                                <a class="dropdown-item{{ Route::currentRouteName() == 'admin.participants' ? ' active' : '' }}"
                                   href="{{ route('admin.participants', ['course' => $course->id]) }}">TN</a>
                            @endif
                        </div>
                    </li>
                @endif
                <li class="nav-item{{ Route::currentRouteName() == 'admin.newcourse' ? ' active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.newcourse') }}">Neuen Kurs erstellen</a>
                </li>
            </ul>
        @endauth
        <ul class="nav navbar-nav navbar-right ml-auto align-items-center-lg">
            @auth
                <li class="nav-item{{ Route::currentRouteName() == 'benutzer' ? ' active' : '' }}">
                    <a class="nav-link" href="{{ route('user') }}">
                        Willkommä, {{ Auth::user()->name }}
                        <img class="avatar-small" src="{{ Auth::user()->image_url != null ? asset(Storage::url(Auth::user()->image_url)) : asset('images/was-gaffsch.svg') }}">
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
