<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Parental\HasChildren;

/**
 * @property int $id
 * @property string $name
 * @property string $group
 * @property string $password
 * @property string $email
 * @property string $image_url
 * @property string $login_provider
 * @property Observation[] $observations
 * @property Course[] $courses
 * @property Course[] $nonArchivedCourses
 * @property Course[] $archivedCourses
 * @property Course $last_accessed_course
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract, MustVerifyEmailContract
{
    use Notifiable, Authenticatable, Authorizable, HasChildren, CanResetPassword, MustVerifyEmail;

    /**
     * @var array
     */
    protected $fillable = ['name', 'group', 'password', 'email', 'image_url', 'login_provider'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['image_path'];

    /**
     * Name of the database column holding the login provider name
     *
     * @var string
     */
    protected $childColumn = 'login_provider';

    /**
     * Mapping from database value to provider specific User class
     *
     * @var array
     */
    protected $childTypes = [
        'hitobito' => HitobitoUser::class,
        'qualix' => NativeUser::class,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function observations()
    {
        return $this->hasMany('App\Models\Observation');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function observationAssignments()
    {
        return $this->belongsToMany('App\Models\ObservationAssignment', 'observation_assignment_users');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function courses()
    {
        return $this->belongsToMany('App\Models\Course', 'trainers', null, 'course_id')->withPivot('last_accessed')->orderByDesc('trainers.last_accessed');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function nonArchivedCourses() {
        return $this->courses()->where('archived', '=', false);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function archivedCourses() {
        return $this->courses()->where('archived', '=', true);
    }

    /**
     * Get the currently viewed course of the user.
     *
     * @return Course
     */
    public function getLastAccessedCourseAttribute() {
        return $this->courses()->firstOrFail();
    }

    /**
     * Get the last date value that the user entered into a block's block_date field in the given Kurs, or today if not available.
     *
     * @param Course $course
     * @return CarbonInterface
     */
    public function getLastUsedBlockDate(Course $course) {
        $date = Carbon::parse($this->courses()->withPivot('last_used_block_date')->findOrFail($course->id)->pivot->last_used_block_date);
        $carbon = $date ?? Carbon::today();
        return $carbon;
    }

    /**
     * Set the last date value that the user entered into a block's block_date field.
     *
     * @param string $value
     */
    public function setLastUsedBlockDate($value, Course $course) {
        $this->courses()->updateExistingPivot($course->id, ['last_used_block_date' => Carbon::parse($value)]);
    }

    /**
     * Get the linkable relative URL to the participant image.
     *
     * @return integer
     */
    public function getImagePathAttribute() {
        return $this->image_url ? asset(Storage::url($this->image_url)) : '/was-gaffsch.svg';
    }

}
