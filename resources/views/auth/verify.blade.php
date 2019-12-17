@extends('layouts.default')

@section('content')
    @if (session('resent'))
        <div class="alert alert-success" role="alert">
            {{ __('A fresh verification link has been sent to your email address.') }}
        </div>
    @endif
    <div class="card">
        <div class="card-header">{{ __('Verify Your Email Address') }}</div>

        <div class="card-body">

            {{ __('Before proceeding, please check your email at :email for a verification link.', ['email' => auth()->user()->email ]) }}
            {{ __('If you did not receive the email') }} <a
                href="{{ route('verification.resend') }}">{{ __('click here to request another') }}</a>.
        </div>
    </div>
@endsection
