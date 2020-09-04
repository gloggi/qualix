@extends('layouts.default')

@section('content')
    <b-card>
        <template #header>{{__('Register')}}</template>
        <div class="form-group row required">
            <div class="col-md-6 offset-md-3">
                <a
                    class="w-100 btn btn-hitobito form-control{{ $errors->has('hitobito') ? ' is-invalid' : '' }}"
                    href="{{ route('login.hitobito') }}">
                    {{ __('t.views.register.via_midata') }}
                </a>
                @if ($errors->has('hitobito'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('hitobito') }}</strong>
                    </span>
                @endif
            </div>
        </div>
        <div class="hr-label">{{ __('t.global.or') }}</div>
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group row required">
                <label for="name" class="col-md-3 col-form-label text-md-right">{{ __('Name') }}</label>

                <div class="col-md-6">
                    <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                           name="name" value="{{ old('name') }}" required autofocus v-focus>

                    @if ($errors->has('name'))
                        <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                    @endif
                </div>
            </div>

            <div class="form-group row required">
                <label for="email" class="col-md-3 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                <div class="col-md-6">
                    <input id="email" type="email"
                           class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
                           value="{{ old('email') }}" required>

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

            <div class="form-group row required">
                <label for="password-confirm"
                       class="col-md-3 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                <div class="col-md-6">
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                           required>
                </div>
            </div>

            <div class="form-group row mb-0">
                <div class="col-md-6 offset-md-3">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Register') }}
                    </button>

                    <a class="btn btn-link" href="{{ route('login') }}">
                        {{ __('I already have an account') }}
                    </a>
                </div>
            </div>
        </form>
    </b-card>
@endsection
