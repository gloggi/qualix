<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\InvalidLoginProviderException;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

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
        if ($request->error) {
            // User has denied access in Hitobito
            return Redirect::route('login')->withErrors([
                'hitobito' => [__('Zugriff in MiData verweigert.')],
            ]);
        }
        try {
            $user = Socialite::driver('hitobito')->setRequest($request)->setScopes(['name'])->user();
        } catch (InvalidStateException $exception) {
            // User has reused an old link or modified the redirect?
            return Redirect::route('login')->withErrors([
                'hitobito' => [__('Etwas hat nicht geklappt. Versuche es noch einmal.')],
            ]);
        } catch (InvalidLoginProviderException $exception) {
            return Redirect::route('login')->withErrors([
                'hitobito' => [__('Melde dich bitte wie üblich mit Benutzernamen und Passwort an.')],
            ]);
        } catch (Exception $exception) {
            return Redirect::route('login')->withErrors([
                'hitobito' => [__('Leider klappt es momentan gerade nicht. Versuche es später wieder, oder registriere unten einen klassischen Account.')],
            ]);
        }

        $this->guard()->login($user);
        return $this->sendLoginResponse($request);
    }
}
