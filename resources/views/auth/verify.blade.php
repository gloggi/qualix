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
            {{ __('If you did not receive the email') }}
            <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <button type="submit" class="btn btn-link p-0 m-0 align-baseline">
                    {{ __('click here to request another') }}
                </button>.
            </form>
        </div>
    </div>
@endsection
