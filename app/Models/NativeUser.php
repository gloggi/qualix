<?php

namespace App\Models;

use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
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
class NativeUser extends User implements CanResetPasswordContract, MustVerifyEmailContract
{
    use HasParent, CanResetPassword, MustVerifyEmail;
}
