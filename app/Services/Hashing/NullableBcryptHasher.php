<?php

namespace App\Services\Hashing;

use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Hashing\BcryptHasher;
use RuntimeException;

/**
 * Extend Laravel's native Bcrypt hasher, to allow for null passwords. We need this, because of our
 * Socialite OAuth login. Users who only have an OAuth login will have a null password in the database.
 */
class NullableBcryptHasher extends BcryptHasher implements HasherContract
{
    /**
     * Verify the hashed value's algorithm.
     *
     * @param  string  $hashedValue
     * @return bool
     */
    protected function isUsingCorrectAlgorithm($hashedValue)
    {
        return null === $hashedValue || $this->info($hashedValue)['algoName'] === 'bcrypt';
    }
}
