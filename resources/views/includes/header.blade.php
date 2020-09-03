<b-navbar toggleable="lg" class="px-0">
    <b-navbar-brand href="{{ route('home') }}" class="navbar-brand">
        <img class="avatar-small" src="{{ asset('images/was-gaffsch.svg') }}" />{{__('t.header.qualix')}}
    </b-navbar-brand>
    <b-navbar-toggle target="navbar-collapse-mobile"></b-navbar-toggle>
    <b-collapse id="navbar-collapse-mobile" is-nav>
        @auth
            <b-navbar-nav>
                @if($course)
                    <b-nav-form>
                        <b-form-select
                            id="global-course-select"
                            ref="global-course-select"
                            @change="$window.location = $refs['global-course-select']._data.localValue"
                            value="{{ route('index', ['course' => $course->id]) }}">
                            @foreach(Auth::user()->nonArchivedCourses as $c)
                                <b-form-select-option value="{{ route('index', ['course' => $c->id]) }}">{{ $c->name }}</b-form-select-option>
                            @endforeach
                            @if(Auth::user()->archivedCourses()->count())
                                <b-form-select-option-group label="{{__('t.header.archived')}}">
                                    @foreach(Auth::user()->archivedCourses as $c)
                                        <b-form-select-option value="{{ route('index', ['course' => $c->id]) }}">{{ $c->name }}</b-form-select-option>
                                    @endforeach
                                </b-form-select-option-group>
                            @endif
                        </b-form-select>
                    </b-nav-form>
                    <b-nav-item href="{{ route('blocks', ['course' => $course->id]) }}" {{ Route::currentRouteName() == 'blocks' ? ' active' : '' }}>
                        {{__('t.views.blocks.menu_name')}}
                    </b-nav-item>

                    @if(!$course->archived)
                        <b-nav-item href="{{ route('participants', ['course' => $course->id]) }}" {{ Route::currentRouteName() == 'participants' ? ' active' : '' }}>
                            {{__('t.views.participants.menu_name')}}
                        </b-nav-item>
                        <b-nav-item href="{{ route('overview', ['course' => $course->id]) }}" {{ Route::currentRouteName() == 'overview' ? ' active' : '' }}>
                            {{__('t.views.overview.menu_name')}}
                        </b-nav-item>
                    @endif
                    <b-nav-item href="{{ route('crib', ['course' => $course->id]) }}" {{ Route::currentRouteName() == 'crib' ? ' active' : '' }}>
                        {{__('t.views.crib.menu_name')}}
                    </b-nav-item>
                    <b-nav-item-dropdown text="{{__('t.header.course_admin')}}" {{ substr( Route::currentRouteName(), 0, 5 ) == 'admin' ? ' active' : '' }}>
                        <b-dropdown-item {{ Route::currentRouteName() == 'admin.course' ? ' active' : '' }}
                           href="{{ route('admin.course', ['course' => $course->id]) }}">{{__('t.views.admin.course_settings.menu_name')}}</b-dropdown-item>
                        <b-dropdown-item {{ Route::currentRouteName() == 'admin.equipe' ? ' active' : '' }}
                           href="{{ route('admin.equipe', ['course' => $course->id]) }}">{{__('t.views.admin.equipe.menu_name')}}</b-dropdown-item>
                        <b-dropdown-item {{ Route::currentRouteName() == 'admin.blocks' ? ' active' : '' }}
                           href="{{ route('admin.blocks', ['course' => $course->id]) }}">{{__('t.views.admin.blocks.menu_name')}}</b-dropdown-item>
                        @if(!$course->archived)
                            <b-dropdown-item dropdown-item{{ Route::currentRouteName() == 'admin.participants' ? ' active' : '' }}
                               href="{{ route('admin.participants', ['course' => $course->id]) }}">{{__('t.views.admin.participants.menu_name')}}</b-dropdown-item>
                        @endif
                        <b-dropdown-item {{ Route::currentRouteName() == 'admin.requirements' ? ' active' : '' }}
                           href="{{ route('admin.requirements', ['course' => $course->id]) }}">{{__('t.views.admin.requirements.menu_name')}}</b-dropdown-item>
                        <b-dropdown-item {{ Route::currentRouteName() == 'admin.categories' ? ' active' : '' }}
                           href="{{ route('admin.categories', ['course' => $course->id]) }}">{{__('t.views.admin.categories.menu_name')}}</b-dropdown-item>
                        @if(!$course->archived)
                            <b-dropdown-item dropdown-item{{ Route::currentRouteName() == 'admin.participantGroups.index' ? ' active' : '' }}
                                             href="{{ route('admin.participantGroups.index', ['course' => $course->id]) }}">{{__('t.views.admin.participant_groups.menu_name')}}</b-dropdown-item>
                        @endif
                    </b-nav-item-dropdown>
                @endif
                <b-nav-item href="{{ route('admin.newcourse') }}" {{ Route::currentRouteName() == 'admin.newcourse' ? ' active' : '' }}>
                    {{__('t.views.admin.new_course.menu_name')}}
                </b-nav-item>
            </b-navbar-nav>
        @endauth
        <b-navbar-nav align="end" class="flex-grow-1 align-items-center-lg">
            <b-nav-item-dropdown id="navbar-locale-select" text="{{ App::getLocale() }}" title="{{__('t.header.language_switch')}}">
                @foreach(array_diff(Config::get('app.supported_locales'), [App::getLocale()]) as $l)
                    <b-dropdown-item href="{{ route('locale.select', ['locale' => $l]) }}">{{ $l }}</b-dropdown-item>
                @endforeach
            </b-nav-item-dropdown>
            @auth
                <b-nav-item-dropdown right title="{{ Auth::user()->name }}">
                    <template #button-content>
                        <img class="avatar-small" src="{{ Auth::user()->image_url != null ? asset(Storage::url(Auth::user()->image_url)) : asset('images/was-gaffsch.svg') }}">
                    </template>

                    <b-dropdown-item {{ Route::currentRouteName() == 'user' ? ' active' : '' }} href="{{ route('user') }}">{{ Auth::user()->name }}</b-dropdown-item>
                    <b-dropdown-form action="{{ route('logout') }}" method="POST" form-class="p-0">
                        <b-button type="submit" class="btn-link dropdown-item">{{__('Logout')}}</b-button>
                        @csrf
                    </b-dropdown-form>
                </b-nav-item-dropdown>
            @else
                <b-nav-item href="{{ route('login') }}">{{__('Login')}}</b-nav-item>
                <b-nav-item href="{{ route('register') }}">{{__('Register')}}</b-nav-item>
            @endif
        </b-navbar-nav>
    </b-collapse>

</b-navbar>
