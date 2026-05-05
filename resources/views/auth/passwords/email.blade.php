@extends('layouts.default')

@section('content')
    <div class="card">
        <div class="card-header">{{ __('Reset Password') }}</div>

        <div class="card-body">

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="form-group row required">
                    <label for="email" class="col-md-3 col-form-label text-md-end">{{ __('t.models.user.email') }}</label>

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

                <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-3">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Send Password Reset Link') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
