# Authentication & Authorization

[← Technical Documentation](../01 Technical Documentation TOC.md)

Qualix supports two login methods against a single `users` table: email/password ("native") and OAuth via **hitobito/MiData** (the Swiss scouting association's identity provider). Authorization is deliberately minimal — there are no Laravel policies or gates; access control is enforced through course-scoped route bindings.

## User model — STI via Parental

[User.php](../../app/Models/User.php) is a single base model backed by the `users` table, split into two child classes using [`tightenco/parental`](https://github.com/tighten/parental):

```php
protected $childColumn = 'login_provider';
protected $childTypes = [
    'hitobito' => HitobitoUser::class,
    'qualix'   => NativeUser::class,
];
```

The `login_provider` column decides which subclass Eloquent instantiates. Both subclasses share the `users` table — **do not add parallel tables or duplicate auth logic**; branch on the child class or `login_provider` instead.

- **[NativeUser](../../app/Models/NativeUser.php)** (`login_provider = 'qualix'`) — email/password. Implements `CanResetPassword` + `MustVerifyEmail`, sends custom `ResetPasswordNotification` / `VerifyEmailNotification`, and `hasMany` `LoginAttempt` / `RecoveryAttempt` (brute-force/recovery throttling records). `email_verified_at` is cast to datetime.
- **[HitobitoUser](../../app/Models/HitobitoUser.php)** (`login_provider = 'hitobito'`) — OAuth. Adds the `hitobito_id` column to `$fillable` and sets `email_verified_at = now()` on construction (hitobito accounts are considered pre-verified, so they skip email verification).

The base `User` implements `Authenticatable`, `Authorizable`, `CanResetPassword`, `MustVerifyEmail`, and relates users to courses through the `trainers` pivot: `courses()`, plus `nonArchivedCourses()` / `archivedCourses()` and a `last_accessed_course` accessor (most-recently-accessed via the pivot's `last_accessed`).

## Native login

Wired by the standard `Auth::routes(['verify' => true])` in [routes/web.php](../../routes/web.php), so registration, login, password reset, and email verification all work out of the box. [LoginController](../../app/Http/Controllers/Auth/LoginController.php) uses Laravel's `AuthenticatesUsers` trait, guarded by `guest` middleware except for logout (`guest` = the custom [RedirectIfAuthenticated](../../app/Http/Middleware/RedirectIfAuthenticated.php)).

Password hashing is customized: `AppServiceProvider` swaps in `NullableBcryptHasher`, which tolerates users with no password (i.e. hitobito users who never set one).

## Hitobito / MiData OAuth

Uses `laravel/socialite` with a custom driver. Two routes in [routes/web.php](../../routes/web.php):

```php
Route::get('login/hitobito', [LoginController::class, 'redirectToHitobitoOAuth'])->name('login.hitobito');
Route::get('login/hitobito/callback', [LoginController::class, 'handleHitobitoOAuthCallback'])->name('login.hitobito.callback');
```

The driver is registered in [AppServiceProvider::bootHitobitoSocialite()](../../app/Providers/AppServiceProvider.php), extending Socialite with the `hitobito` driver backed by [App\Auth\HitobitoProvider](../../app/Auth/HitobitoProvider.php). Configuration lives in [config/services.php](../../config/services.php) under `services.hitobito`, driven by env vars:

| Env var | Config key | Notes |
| --- | --- | --- |
| `HITOBITO_BASE_URL` | `base_url` | Defaults to `http://demo.hitobito.ch`. |
| `HITOBITO_CLIENT_UID` | `client_id` | OAuth client id. |
| `HITOBITO_CLIENT_SECRET` | `client_secret` | OAuth secret. |
| `HITOBITO_CALLBACK_URI` | `redirect` | Callback URL; may be relative (resolved via `url->to()`). |

The OAuth integration requests only the `name` scope, which provides email, first_name, last_name, nickname and address.

### Callback logic

`handleHitobitoOAuthCallback()` ([LoginController](../../app/Http/Controllers/Auth/LoginController.php)) handles the account-linking rules:

- **Access denied / invalid state** (reused or tampered link) → redirect back to login with a translated error.
- **Existing user** (matched by `hitobito_id`) → log in; also update the stored email from hitobito, *but only* if the new email isn't already taken by another account. This way, the hitobito login is an alternative way of verifying a user's email address, in case the verification email never arrives.
- **New user** → create a `HitobitoUser` from `hitobito_id` / email / nickname — unless that email already belongs to an existing account, in which case an `InvalidLoginProviderException` is thrown and the user is told to use their normal credentials. This prevents silently merging a native account into an OAuth one via a shared email.

## Authorization

There is **no `app/Policies` directory and no gates**. Authorization is structural:

1. **Authentication** — the main route group requires `auth` + `verified` middleware ([routes/web.php](../../routes/web.php)).
2. **Course access** — the `{course}` route binding in [AppServiceProvider](../../app/Providers/AppServiceProvider.php) resolves a course *only through the current user's own courses*:

   ```php
   Route::bind('course', function($id) {
       return Auth::user()->courses()->findOrFail($id);
   });
   ```

   A user who is not a trainer of the course gets a 404 — that single binding is the app's course-level authorization.
3. **Nested resources** — `scopeBindings()` on the route group constrains child models (observations, blocks, participants, …) to the bound course, so users can never reach another course's data. See [Application Architecture Overview](../Architecture/11 Application Architecture Overview.md).
4. **Archived courses** — the `courseNotArchived` middleware blocks mutations on archived courses (also in [Application Architecture Overview](../Architecture/11 Application Architecture Overview.md)).

There is no role hierarchy: every trainer on a course has the same rights. Course membership is managed via invitations ([InvitationController](../../app/Http/Controllers/InvitationController.php)) and the equipe screen ([EquipeController](../../app/Http/Controllers/EquipeController.php)).
