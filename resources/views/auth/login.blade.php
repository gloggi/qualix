@extends('layouts.default')

@section('content')

    <b-card>
        <template #header>{{__('Login')}}</template>
        <div class="form-group row">
            <div class="col-md-6 offset-md-3">
                <a
                    class="w-100 btn btn-hitobito form-control{{ $errors->has('hitobito') ? ' is-invalid' : '' }}"
                    href="{{ route('login.hitobito') }}">
                    {{ __('t.views.login.via_midata') }}
                </a>
                @if ($errors->has('hitobito'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('hitobito') }}</strong>
                    </span>
                @endif
            </div>
        </div>
        <div class="hr-label">{{ __('t.global.or') }}</div>
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group row required">
                <label for="email" class="col-md-3 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                <div class="col-md-6">
                    <input id="email" type="email"
                           class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
                           value="{{ old('email') }}" required autofocus v-focus>

                    @if ($errors->has('email'))
                        <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                    @endif
                </div>
            </div>

            <div class="form-group row required">
                <label for="password" class="col-md-3 col-form-label text-md-right">{{ __('Password') }}</label>

                <div class="col-md-6">
                    <input id="password" type="password"
                           class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password"
                           required>

                    @if ($errors->has('password'))
                        <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                    @endif
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-6 offset-md-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember"
                               id="remember" {{ old('remember') ? 'checked' : '' }}>

                        <label class="form-check-label" for="remember">
                            {{ __('Remember Me') }}
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group row mb-0">
                <div class="col-md-8 offset-md-3">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Login') }}
                    </button>

                    <a class="btn btn-link" href="{{ route('password.request') }}">
                        {{ __('Forgot Your Password?') }}
                    </a>

                    <a class="btn btn-link" href="{{ route('register') }}">
                        {{ __('Register') }}
                    </a>
                </div>
            </div>
        </form>
    </b-card>
@endsection
