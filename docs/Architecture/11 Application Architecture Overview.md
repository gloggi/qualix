# Application Architecture Overview

[← Technical Documentation](../01 Technical Documentation TOC.md)

Qualix is a **Laravel 11** (PHP >= 8.2) web application. It is server-rendered with Blade views that mount **Vue 3** components (built with Vite). This page covers the request lifecycle, how resources are scoped to a `Course`, and the middleware that enforces that scoping. For the frontend details see [Frontend Architecture](../Architecture/13 Frontend Architecture.md); for the data model see [Domain Model & Database Schema](../Architecture/12 Domain Model and Database Schema.md).

## Request lifecycle

Standard Laravel 11 flow, configured in [bootstrap/app.php](../../bootstrap/app.php):

- Routing comes from [routes/web.php](../../routes/web.php); console routes from `routes/console.php`; a health check is exposed at `/up`.
- Global/aliased middleware is registered in the `withMiddleware()` closure (see below).
- Exception handling (`withExceptions()`) reports uncaught exceptions to Sentry (unless `app.debug`) and contains the form-restoration logic described under `restoreFormData`.

## Blade + Vue hybrid

Pages are Blade views under [resources/views](../../resources/views). The layout [layouts/default.blade.php](../../resources/views/layouts/default.blade.php) renders a single `<div id="app" v-cloak>` into which one global Vue app is mounted ([resources/js/app.js](../../resources/js/app.js)). Laravel route metadata, old input, and validation errors are serialized into a `#laravel-data` element (see [layouts/master.blade.php](../../resources/views/layouts/master.blade.php)) and parsed into `window.Laravel`. This is covered in detail in [Frontend Architecture](../Architecture/13 Frontend Architecture.md).

## Course-scoped resources & route nesting

Almost every resource belongs to a `Course` (see [Course.php](../../app/Models/Course.php)). Routes are nested under `/course/{course}/...` inside one group in [routes/web.php](../../routes/web.php):

```php
Route::middleware(['auth', 'verified', 'restoreFormData'])->scopeBindings()->group(function () {
    Route::get('/course/{course}', [HomeController::class, 'index'])->name('index');
    Route::get('/course/{course}/observation/{observation}', [ObservationController::class, 'edit'])->name('observation.edit');
    // ...
});
```

`scopeBindings()` is the key: it constrains child route-model bindings to their parent `{course}`. When a route has both `{course}` and, e.g., `{observation}`, Laravel automatically resolves the observation *through* the course's relationship, returning 404 if the observation does not belong to that course. This removes the need for manual "does this belong to my course?" checks in controllers.

The `{course}` binding itself is defined in [AppServiceProvider::boot()](../../app/Providers/AppServiceProvider.php):

```php
Route::bind('course', function($id) {
    return Auth::user()->courses()->findOrFail($id);
});
```

So a course is only resolvable if the authenticated user is one of its trainers — this is where course-level authorization actually happens (see [Authentication & Authorization](../Architecture/14 Authentication and Authorization.md)). Custom bindings for `requirement` and `feedback_data` are defined there too (the latter eager-loads feedbacks/participants/requirements/users).

### Controller organization

Controllers live in [app/Http/Controllers](../../app/Http/Controllers), one per resource (`ObservationController`, `BlockController`, `FeedbackController`, …). "Admin" (course-configuration) routes are grouped under `/course/{course}/admin/...` with `admin.*` route names, while day-to-day usage routes (observations, overview, feedback content) sit at the course root. Some resources are split into a mutating controller and a read/list controller — e.g. `ParticipantController` (admin CRUD) vs. `ParticipantListController`/`ParticipantDetailController` (viewing), and `FeedbackController` (data) vs. `FeedbackListController`/`FeedbackContentController` (progress + Tiptap content).

## Key middleware

Registered in [bootstrap/app.php](../../bootstrap/app.php). Route-group middleware:

| Alias / name | Class | Purpose |
| --- | --- | --- |
| `auth` | Laravel default | Requires a logged-in user. |
| `verified` | Laravel default | Requires a verified email (`Auth::routes(['verify' => true])`). |
| `restoreFormData` | [RestoreFormDataFromExpiredSession](../../app/Http/Middleware/RestoreFormDataFromExpiredSession.php) | If a user was logged out mid-form, their submitted data was stashed in the session on the failed request; this restores it as `_old_input` after they log back in, and flashes a "please submit again" warning. The stashing side lives in the CSRF-mismatch handler in `withExceptions()`. |
| `courseNotArchived` | [CourseMustNotBeArchived](../../app/Http/Middleware/CourseMustNotBeArchived.php) | Archiving a course turns it read-only and deletes any personally identifiable information such as participants, pictures, observations, feedbacks and so on. All routes that should not be accessible (including any mutating routes) go into the `courseNotArchived` group. Read-only routes (crib, print, index, participant/feedback listings) sit *outside* these `courseNotArchived` sub-groups so archived courses stay viewable. |

Global / web middleware (appended in the same file): `SecurityHeaders`, `Localization` (see [Translation Workflow](../Internationalization/41 Translation Workflow.md)), and `UpdateLeiterLastAccessed` (updates the trainer's `last_accessed` pivot on the course). `TrimStrings` and `RedirectIfAuthenticated` (`guest`) replace their Laravel defaults. CSRF validation is skipped for `/__e2e__/*` (see [Testing Strategy](../Testing/51 Testing Strategy.md)).

When adding an endpoint, decide first: is it course-scoped (goes in the `scopeBindings()` group), and is it mutating or about personally identifiable information (goes inside a `courseNotArchived` sub-group)?

## Authentication routes

Outside the main group, [routes/web.php](../../routes/web.php) wires `Auth::routes(['verify' => true])`, the two hitobito OAuth routes (`login.hitobito`, `login.hitobito.callback`), and `locale.select`. See [Authentication & Authorization](../Architecture/14 Authentication and Authorization.md).
