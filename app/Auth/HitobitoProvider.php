<?php

namespace App\Auth;

use App\Exceptions\InvalidLoginProviderException;
use App\Models\HitobitoUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;

class HitobitoProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * The base URL under which the OAuth service is available.
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * The separating character for the requested scopes.
     *
     * @var string
     */
    protected $scopeSeparator = ' ';

    /**
     * Create a new provider instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $baseUrl
     * @param  string  $clientId
     * @param  string  $clientSecret
     * @param  string  $redirectUrl
     * @param  array  $guzzle
     * @return void
     */
    public function __construct(Request $request, $baseUrl, $clientId, $clientSecret, $redirectUrl, $guzzle = [])
    {
        parent::__construct($request, $clientId, $clientSecret, $redirectUrl, $guzzle);
        $this->baseUrl = $baseUrl . '/oauth';
    }

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase($this->baseUrl . '/authorize', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return $this->baseUrl . '/token';
    }

    /**
     * Get the POST fields for the token request.
     *
     * @param  string  $code
     * @return array
     */
    protected function getTokenFields($code)
    {
        return parent::getTokenFields($code) + ['grant_type' => 'authorization_code'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get($this->baseUrl . '/profile', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'X-Scope' => $this->formatScopes($this->getScopes(), $this->scopeSeparator)
            ],
        ]);
        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $userData)
    {
        if ($userFromDB = HitobitoUser::where('hitobito_id', $userData['id'])->first()) {
            // Login
            return $this->mapReturningUserToObject($userFromDB, $userData);
        } else {
            // Register
            return $this->mapNewUserToObject($userData);
        }
    }

    private function mapReturningUserToObject(User $user, $userData) {
        if ($user->email != $userData['email'] && User::where('email', $userData['email'])->doesntExist()) {
            // Update email only if it is not occupied by someone else
            $user->email = $userData['email'];
            $user->save();
        }
        return $user;
    }

    private function mapNewUserToObject($userData) {
        if (User::where('email', $userData['email'])->exists()) {
            // Don't register a new user if someone else already uses the same email address
            throw new InvalidLoginProviderException;
        }
        $created = HitobitoUser::create(['hitobito_id' => $userData['id'], 'email' => $userData['email'], 'name' => $userData['nickname']]);
        return $created;
    }
}
