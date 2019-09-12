<?php

namespace App\Models;

use Carbon\Carbon;
use Tightenco\Parental\HasParent;

/**
 * @property int $id
 * @property string $name
 * @property string $group
 * @property string $password
 * @property string $email
 * @property string $image_url
 * @property string $login_provider
 * @property int $hitobito_id
 * @property Observation[] $observations
 * @property Course[] $courses
 * @property Course[] $nonArchivedCourses
 * @property Course[] $archivedCourses
 * @property Course $last_accessed_course
 */
class HitobitoUser extends User {
    use HasParent;

    public function __construct(...$args) {
        $this->fillable[] = 'hitobito_id';
        parent::__construct(...$args);
    }

    public function newInstance($attributes = [], $exists = false) {
        return tap(parent::newInstance($attributes, $exists), function ($instance) {
            $instance->email_verified_at = Carbon::now();
        });
    }

    /**
     * Method stub to satisfy Socialite.
     *
     * @param string $token
     * @return $this
     */
    public function setToken($token) {
        return $this;
    }

    /**
     * Method stub to satisfy Socialite.
     *
     * @param string $refreshToken
     * @return $this
     */
    public function setRefreshToken($refreshToken) {
        return $this;
    }

    /**
     * Method stub to satisfy Socialite.
     *
     * @param int $expiresIn
     * @return $this
     */
    public function setExpiresIn($expiresIn) {
        return $this;
    }
}
