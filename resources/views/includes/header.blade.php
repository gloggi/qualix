<nav class="container navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="/"><img class="avatar-small" src="{{ asset('images/was-gaffsch.svg') }}" />@lang('t.header.qualix')</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        @auth
            <ul class="navbar-nav">
                @if($course)
                    <li>
                        <select id="globalCourseSelect" class="custom-select" onchange="window.location = this.value">
                            @foreach(Auth::user()->nonArchivedCourses as $c)
                                <option value="{{ route('index', ['course' => $c->id]) }}"{{ $course->id === $c->id ? ' selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                            @if(Auth::user()->archivedCourses()->count())
                                <optgroup label="@lang('t.header.course.archived')">
                                    @foreach(Auth::user()->archivedCourses as $c)
                                        <option value="{{ route('index', ['course' => $c->id]) }}"{{ $course->id === $c->id ? ' selected' : '' }}>{{ $c->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>
                    </li>
                    <li class="nav-item{{ Route::currentRouteName() == 'blocks' ? ' active' : '' }}">
                        <a class="nav-link" href="{{ route('blocks', ['course' => $course->id]) }}">@lang('t.views.blocks.title')</a>
                    </li>

                    @if(!$course->archived)
                        <li class="nav-item{{ Route::currentRouteName() == 'participants' ? ' active' : '' }}">
                            <a class="nav-link" href="{{ route('participants', ['course' => $course->id]) }}">@lang('t.views.participants.title')</a>
                        </li>
                        <li class="nav-item{{ Route::currentRouteName() == 'overview' ? ' active' : '' }}">
                            <a class="nav-link" href="{{ route('overview', ['course' => $course->id]) }}">@lang('t.views.overview.title')</a>
                        </li>
                    @endif
                    <li class="nav-item{{ Route::currentRouteName() == 'crib' ? ' active' : '' }}">
                        <a class="nav-link" href="{{ route('crib', ['course' => $course->id]) }}">@lang('t.views.crib.title')</a>
                    </li>
                    <li class="nav-item dropdown{{ substr( Route::currentRouteName(), 0, 5 ) == 'admin' ? ' active' : '' }}">
                        <a class="nav-link dropdown-toggle" id="navbarCourseAdmin" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            @lang('t.header.course_admin')
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarCourseAdmin">
                            <a class="dropdown-item{{ Route::currentRouteName() == 'admin.course' ? ' active' : '' }}"
                               href="{{ route('admin.course', ['course' => $course->id]) }}">@lang('t.views.admin.course_settings.title')</a>
                            <a class="dropdown-item{{ Route::currentRouteName() == 'admin.equipe' ? ' active' : '' }}"
                               href="{{ route('admin.equipe', ['course' => $course->id]) }}">@lang('t.views.admin.equipe.title')</a>
                            <a class="dropdown-item{{ Route::currentRouteName() == 'admin.requirements' ? ' active' : '' }}"
                               href="{{ route('admin.requirements', ['course' => $course->id]) }}">@lang('t.views.admin.requirements.title')</a>
                            <a class="dropdown-item{{ Route::currentRouteName() == 'admin.categories' ? ' active' : '' }}"
                               href="{{ route('admin.categories', ['course' => $course->id]) }}">@lang('t.views.admin.categories.title')</a>
                            <a class="dropdown-item{{ Route::currentRouteName() == 'admin.blocks' ? ' active' : '' }}"
                               href="{{ route('admin.blocks', ['course' => $course->id]) }}">@lang('t.views.admin.blocks.title')</a>
                            @if(!$course->archived)
                                <a class="dropdown-item{{ Route::currentRouteName() == 'admin.participants' ? ' active' : '' }}"
                                   href="{{ route('admin.participants', ['course' => $course->id]) }}">@lang('t.views.admin.participants.title')</a>
                            @endif
                        </div>
                    </li>
                @endif
                <li class="nav-item{{ Route::currentRouteName() == 'admin.newcourse' ? ' active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.newcourse') }}">@lang('t.views.admin.new_course.title')</a>
                </li>
            </ul>
        @endauth
        <ul class="nav navbar-nav navbar-right ml-auto align-items-center-lg">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarLocaleSelect" role="button"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ App::getLocale() }}
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarLocaleSelect">
                    @foreach(array_diff(Config::get('app.supported_locales'), [App::getLocale()]) as $l)
                        <a class="dropdown-item"
                           href="{{ route('locale.select', ['locale' => $l]) }}">{{ $l }}</a>
                    @endforeach
                </div>
            </li>
            @auth
                <li class="nav-item{{ Route::currentRouteName() == 'benutzer' ? ' active' : '' }}">
                    <a class="nav-link" href="{{ route('user') }}">
                        @lang('t.header.welcome', [ 'user' => Auth::user()->name ])
                        <img class="avatar-small" src="{{ Auth::user()->image_url != null ? asset(Storage::url(Auth::user()->image_url)) : asset('images/was-gaffsch.svg') }}">
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        @lang('Logout')
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            @else
                <li>
                    <a class="nav-link" href="{{ route('login') }}">@lang('Login')</a>
                </li>
                <li>
                    <a class="nav-link" href="{{ route('register') }}">@lang('Register')</a>
                </li>
            @endif
        </ul>
    </div>
</nav>
