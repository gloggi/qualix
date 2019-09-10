<?php

namespace App\Auth;

use App\Exceptions\InvalidLoginProviderException;
use App\Models\HitobitoUser;
use App\Models\User;
use Illuminate\Http\Request;
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
    protected function mapUserToObject(array $user)
    {
        return HitobitoUser::where('email', $user['email'])->firstOr(function() use($user) {
            if (User::where('email', $user['email'])->exists()) {
                throw new InvalidLoginProviderException;
            }
            return HitobitoUser::create(['email' => $user['email'], 'name' => $user['nickname']]);
        });
    }
}
