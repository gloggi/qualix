<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\InvalidLoginProviderException;
use App\Http\Controllers\Controller;
use App\Models\HitobitoUser;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Laravel\Socialite\AbstractUser as SocialiteUser;
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
            return $this->redirectWithError(__('t.views.login.midata.user_has_denied_access'));
        }
        try {
            $socialiteUser = Socialite::driver('hitobito')->setRequest($request)->setScopes(['name'])->user();
            $user = $this->findOrCreateSocialiteUser($socialiteUser);
        } catch (InvalidStateException $exception) {
            // User has reused an old link or modified the redirect?
            return $this->redirectWithError(__('t.views.login.midata.error_please_retry'));
        } catch (InvalidLoginProviderException $exception) {
            return $this->redirectWithError(__('t.views.login.midata.use_normal_credentials'));
        } catch (Exception $exception) {
            return $this->redirectWithError(__('t.views.login.midata.error_retry_later'));
        }

        $this->guard()->login($user);
        return $this->sendLoginResponse($request);
    }

    private function redirectWithError($error) {
        return Redirect::route('login')->withErrors([
            'hitobito' => [$error],
        ]);
    }

    private function findOrCreateSocialiteUser(SocialiteUser $socialiteUser)
    {
        if ($userFromDB = HitobitoUser::where('hitobito_id', $socialiteUser->getId())->first()) {
            // User is logging in
            return $this->updateEmailIfAppropriate($userFromDB, $socialiteUser);
        } else {
            // User is registering
            return $this->createNewHitobitoUser($socialiteUser);
        }
    }

    private function updateEmailIfAppropriate(HitobitoUser $user, SocialiteUser $socialiteUser) {
        $hitobitoEmail = $socialiteUser->getEmail();
        if ($user->email != $hitobitoEmail && User::where('email', $hitobitoEmail)->doesntExist()) {
            // Update email only if it is not occupied by someone else
            $user->email = $hitobitoEmail;
            $user->save();
        }
        return $user;
    }

    private function createNewHitobitoUser(SocialiteUser $socialiteUser) {
        if (User::where('email', $socialiteUser->getEmail())->exists()) {
            // Don't register a new user if another account already uses the same email address
            throw new InvalidLoginProviderException;
        }
        $created = HitobitoUser::create(['hitobito_id' => $socialiteUser->getId(), 'email' => $socialiteUser->getEmail(), 'name' => $socialiteUser->getNickname()]);
        return $created;
    }
}
