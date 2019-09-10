<?php

namespace App\Models;

use Tightenco\Parental\HasParent;

/**
 * @property int $id
 * @property int $course_id
 * @property string $name
 * @property string $group
 * @property string $password
 * @property string $email
 * @property string $salt
 * @property string $image_url
 * @property string $type
 * @property Observation[] $observations
 * @property Course[] $courses
 * @property Course[] $nonArchivedCourses
 * @property Course[] $archivedCourses
 * @property Course $last_accessed_course
 * @property LoginAttempt[] $loginAttempts
 * @property RecoveryAttempt[] $recoveryAttempts
 */
class HitobitoUser extends User {
    use HasParent;

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
