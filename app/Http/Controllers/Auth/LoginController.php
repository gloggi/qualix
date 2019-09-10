<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\HitobitoUser;
use Exception;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirect the user to the hitobito authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToHitobitoOAuth()
    {
        return Socialite::driver('hitobito')->setScopes(['name'])->redirect();
    }

    /**
     * Obtain the user information from hitobito.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function handleHitobitoOAuthCallback(Request $request)
    {
        try {
            $user = Socialite::driver('hitobito')->setScopes(['name'])->user();
        } catch (Exception $exception) {
            return $this->sendFailedLoginResponse($request);
        }

        if (!$user instanceof HitobitoUser) {
            // Block impersonation via OAuth
            return $this->sendFailedLoginResponse($request);
        }

        $this->guard()->login($user);
        return $this->sendLoginResponse($request);
    }
}
