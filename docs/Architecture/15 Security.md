# Security

[← Technical Documentation](../01 Technical Documentation TOC.md)

The security-relevant mechanisms in Qualix are spread across middleware, the auth stack, and the E2E testing routes. This page consolidates them. For who-can-log-in and the (deliberately minimal) authorization model, see [Authentication & Authorization](../Architecture/14 Authentication and Authorization.md); this doc covers transport hardening, security headers, password handling, throttling, and the testing-route attack surface.

## Authorization is structural, not policy-based

There are no Laravel policies or gates — access control is enforced entirely by course-scoped route-model binding (the mechanics are in [Authentication & Authorization](../Architecture/14 Authentication and Authorization.md) and [Application Architecture Overview](../Architecture/11 Application Architecture Overview.md)). The security consequence worth restating here: **route nesting *is* the authorization** — an endpoint placed in the wrong route group can leak cross-course data, and mutating endpoints must additionally sit inside the `courseNotArchived` group.

## Security headers & CSP

[`SecurityHeaders`](../../app/Http/Middleware/SecurityHeaders.php) sets, on every response:

| Header | Value |
| --- | --- |
| `Strict-Transport-Security` | `max-age=31536000; includeSubDomains` — **production only** |
| `Content-Security-Policy` | full policy (see below) — `Content-Security-Policy-Report-Only` outside production |
| `X-Frame-Options` | `DENY` |
| `X-Content-Type-Options` | `nosniff` |
| `Referrer-Policy` | `same-origin` |

The CSP is assembled per-directive:

- `default-src 'self'`
- `script-src 'self' 'unsafe-eval'` — `'unsafe-eval'` is required by the bundled frontend runtime; it widens the script surface, so avoid introducing anything that would let user input reach `eval`.
- `style-src 'self' 'nonce-…'` — the nonce comes from [`Vite::cspNonce()`](../../resources/js/app.js); Vite is configured with `assetsInlineLimit: 0` so styles are served as files, not inlined without a nonce.
- `font-src 'self'`, `img-src 'self' data:`
- `connect-src` — `'self'` (http + ws), `data:`, plus the Sentry ingest origin (derived from the frontend DSN) and the collaboration signaling servers (`config('app.collaboration.signaling_servers')`) when [collaboration](../Features/24 Feedback System and Collaborative Editing.md) is enabled. Keep this in sync when adding an external endpoint the frontend must reach ([Error Tracking](../Infrastructure/34 Error Tracking.md)).
- `report-uri` — CSP violations are reported to Sentry (`config('app.sentry.csp_report_uri')`) tagged with the environment.

Outside production the policy is **report-only** and `'self'` is widened to also allow `http://localhost:5173` (the Vite dev server), so violations surface in logs/Sentry without breaking local development.

## Password hashing

Native (email/password) users are hashed with bcrypt via [`NullableBcryptHasher`](../../app/Services/Hashing/NullableBcryptHasher.php), registered in [`AppServiceProvider`](../../app/Providers/AppServiceProvider.php) by overriding the `bcrypt` hash driver. It is a thin subclass whose only change is tolerating a **null** stored hash: OAuth-only ([HitobitoUser](../Architecture/14 Authentication and Authorization.md)) accounts have no password, so `null` must verify as "no valid password" rather than throwing. Everything else is stock Laravel bcrypt.

## Login & verification throttling

Native login throttling is Laravel's standard `ThrottlesLogins`, pulled in via the `AuthenticatesUsers` trait on [`LoginController`](../../app/Http/Controllers/Auth/LoginController.php) — the framework default of **5 failed attempts per minute** per email+IP, no custom override. Email verification and resend are rate-limited with `throttle:6,1` in [`VerificationController`](../../app/Http/Controllers/Auth/VerificationController.php).

> **Dead code:** the [`LoginAttempt`](../../app/Models/LoginAttempt.php) and [`RecoveryAttempt`](../../app/Models/RecoveryAttempt.php) models and their tables (migrations from 2019) still exist, but no live code references them beyond the Eloquent relations on `NativeUser` — the actual throttling is the framework mechanism above. They appear to be leftovers from a previous custom implementation and are candidates for removal.

## E2E testing routes — an intentional, gated backdoor

The `/__e2e__/*` routes ([`routes/e2e.php`](../../routes/e2e.php), [`E2ETestingController`](../../app/Http/Controllers/E2ETestingController.php)) exist to let the [E2E suite](../Testing/51 Testing Strategy.md) log in as arbitrary users, run factories, execute artisan commands, and snapshot/restore the database. Two endpoints are especially dangerous:

- `POST /__e2e__/run-php` — runs `eval()` on the request body.
- `POST /__e2e__/artisan` — runs an arbitrary artisan command.

The **only** thing protecting these is a guard at the top of the controller:

```php
abort_unless(app()->environment('testing', 'local'), 404);
```

So the entire surface is inert in `production`. The security invariant is therefore: **production `APP_ENV` must be `production`** (never `local`/`testing`), and this guard must never be weakened. See [Environment Configuration](../Infrastructure/32 Environment Configuration.md).

## Related

- [Authentication & Authorization](../Architecture/14 Authentication and Authorization.md) — login providers, the STI user model, account linking.
- [Error Tracking](../Infrastructure/34 Error Tracking.md) — Sentry, including CSP violation reports.
- [Testing Strategy](../Testing/51 Testing Strategy.md) — how the `/__e2e__/*` routes are used.
