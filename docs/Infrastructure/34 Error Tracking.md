# Error Tracking

[← Technical Documentation](../01 Technical Documentation TOC.md)

Qualix reports runtime errors to [Sentry](https://sentry.io) from both the Laravel backend and the Vue frontend, plus Content-Security-Policy violation reports. Everything is DSN-gated: with no DSN configured (the default in local/testing), no data is sent. See [Environment Configuration](../Infrastructure/32 Environment Configuration.md#error-tracking-sentry--vite_sentry) for the variables and [Continuous Deployment](../Infrastructure/33 Continuous Deployment.md) for how releases are cut.

## Backend (Laravel)

Uses the `sentry/sentry-laravel` SDK. Configuration is in [config/sentry.php](../../config/sentry.php):

```php
'dsn' => env('SENTRY_LARAVEL_DSN', env('SENTRY_DSN')),
'release' => env('SENTRY_RELEASE'),
'breadcrumbs' => [
    'logs' => true,
    'sql_queries' => true,
    'sql_bindings' => true,
    'queue_info' => true,
],
```

- **DSN**: `SENTRY_LARAVEL_DSN`, falling back to `SENTRY_DSN`. Empty ⇒ disabled.
- **Release**: `SENTRY_RELEASE`, set to the git SHA at deploy time (see below).
- **Breadcrumbs**: Laravel logs, SQL queries (with bindings), and queue job info are captured as breadcrumbs to give errors context.

### Where exceptions are captured

Exception reporting is wired up in [bootstrap/app.php](../../bootstrap/app.php):

```php
$exceptions->reportable(function (Throwable $exception) {
    if (!config('app.debug') && app()->bound('sentry')) {
        Integration::captureUnhandledException($exception);
    }
});
```

Two guards matter: errors are only sent when **`APP_DEBUG` is false** (i.e. production, not local dev) and when the Sentry service is actually bound (a DSN is configured). This prevents noise from local/dev environments.

## Frontend (Vue)

Uses `@sentry/vue`, initialized in [resources/js/app.js](../../resources/js/app.js):

```js
if (import.meta.env.VITE_SENTRY_VUE_DSN && import.meta.env.VITE_SENTRY_VUE_DSN !== 'null') {
  Sentry.init({
    app: app,
    dsn: import.meta.env.VITE_SENTRY_VUE_DSN,
    release: import.meta.env.VITE_SENTRY_RELEASE || "dev",
    logErrors: true,
  })
}
```

- **DSN**: `VITE_SENTRY_VUE_DSN` (baked into the bundle at build time by Vite). Absent or the literal string `"null"` ⇒ Sentry is never initialized.
- **Release**: `VITE_SENTRY_RELEASE`, or `"dev"` when unset.
- `logErrors: true` also forwards captured errors to the browser console.

The Vue DSN is additionally exposed to the backend via [config/app.php](../../config/app.php) (`sentry.frontend.vue_dsn`) so the CSP layer can allowlist Sentry's ingest host (next section).

## CSP violation reporting

The `SecurityHeaders` middleware ([app/Http/Middleware/SecurityHeaders.php](../../app/Http/Middleware/SecurityHeaders.php)) ties Sentry into the Content-Security-Policy:

- **`connect-src`**: the Vue DSN's origin (scheme + host + optional port, parsed from `sentry.frontend.vue_dsn`) is added so the browser SDK is allowed to POST events. The collaboration signaling server is allowlisted the same way.
- **`report-uri`**: when `sentry.csp_report_uri` (from `SENTRY_CSP_REPORT_URI`) is set, browsers post CSP violations to it, with the current app environment appended as a query param:

  ```php
  if ($reportUri) return "report-uri $reportUri&sentry_environment=" . App::environment();
  ```

## Release and environment tagging

Releases are created during deployment in [ci.yml](../../.github/workflows/ci.yml), so Sentry can associate errors with a specific commit and environment. See the [Continuous Deployment Documentation](./33 Continuous Deployment.md) for details. The environment for CSP reports is tagged separately via the `sentry_environment` query param above.

## Quick reference

| Concern | Variable | Config location |
| --- | --- | --- |
| Backend DSN | `SENTRY_LARAVEL_DSN` (→ `SENTRY_DSN`) | [config/sentry.php](../../config/sentry.php) |
| Backend release | `SENTRY_RELEASE` | [config/sentry.php](../../config/sentry.php) |
| Frontend DSN | `VITE_SENTRY_VUE_DSN` | [resources/js/app.js](../../resources/js/app.js), [config/app.php](../../config/app.php) |
| Frontend release | `VITE_SENTRY_RELEASE` | [resources/js/app.js](../../resources/js/app.js) |
| CSP report endpoint | `SENTRY_CSP_REPORT_URI` | [app/Http/Middleware/SecurityHeaders.php](../../app/Http/Middleware/SecurityHeaders.php) |
| Release registration | `SENTRY_RELEASE_TRACKING_AUTH_TOKEN`, `SENTRY_ORG`, `SENTRY_PROJECTS` | [ci.yml](../../.github/workflows/ci.yml) |
