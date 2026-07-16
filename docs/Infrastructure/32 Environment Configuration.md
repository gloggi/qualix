# Environment Configuration

[← Technical Documentation](../01 Technical Documentation TOC.md)

Qualix runs entirely in Docker for local development — there is no expectation of a local PHP/Node/Playwright install. This page documents the Compose services, the various `.env` files, the environment variables grouped by concern, and how production secrets are injected at deploy time.

Related: [CI Pipeline](../Infrastructure/31 CI Pipeline.md) (uses the same stack), [Continuous Deployment](../Infrastructure/33 Continuous Deployment.md) (how the production `.env` is built), [Error Tracking](../Infrastructure/34 Error Tracking.md) (Sentry vars).

## Docker Compose services

Defined in [docker-compose.yml](../../docker-compose.yml). Start everything with `docker compose up`.

| Service | Image / build | Purpose | Ports |
| --- | --- | --- | --- |
| `qualix` | builds [`.docker`](../../.docker) | The Laravel app (Apache + PHP). Mounts the repo at `/var/www`. | `80:80` |
| `db` | `mariadb:10.6` | MariaDB database `qualix`. Root password is randomized; app user is `qualix`/`qualix`. | `3306:3306` |
| `vite` | `node:22` | Vite dev server. Runs `npm install`, `scripts/install-twemoji.sh`, then `vite`. | `5173:5173` |
| `worker-build` | `node:22` | Watches and builds the Yjs collaboration web-worker bundle (`vite build --config vite-workers.config.js`). | — |
| `mail` | `schickling/mailcatcher` | Catches outgoing dev mail; web UI on port 1080. | `1080:1080` |
| `e2e` | `mcr.microsoft.com/playwright` | Runs the Playwright E2E suite (`docker compose run e2e run` or `open`). | `1100:1100` |
| `e2e-mocks` | `mockserver/mockserver` | Mock hitobito OAuth provider for E2E. | `1090:1080` |
| `docker-host` | `qoomon/docker-host` | Network helper for reaching host services from containers. | — |

Handy endpoints in local dev: app at <http://localhost>, phpMyAdmin at <http://localhost:8081> (see project README/AGENTS for the phpMyAdmin service if enabled), Mailcatcher at <http://localhost:1080>, Playwright UI at <http://localhost:1100>.

Run backend commands in the `qualix` container and frontend commands in the `vite` container:

```
docker compose exec qualix php artisan tinker
docker compose exec vite npm run test
```

## The `.env` files

| File | `APP_ENV` | Used by | Notes |
| --- | --- | --- | --- |
| [.env.example](../../.env.example) | `local` | template | Copied to `.env` for local dev, and also the base for the production `.env` built during deploy. |
| `.env` | `local` | `docker compose up` | Your working local config (not committed). |
| [.env.testing](../../.env.testing) | `testing` | PHPUnit | Committed. Contains a dummy `APP_KEY` and dummy Sentry DSNs. `config:clear --env=testing` must be run once so it is picked up. |
| [.env.e2e](../../.env.e2e) | `testing` | Playwright E2E | Committed. Points `HITOBITO_BASE_URL` at the `e2e-mocks` MockServer (`http://e2e-mocks:1080`) and disables collaboration. Swapped in by the E2E global setup/teardown. |

> `.env.testing` and `.env.e2e` deliberately contain dummy, non-secret placeholder values (fake DSNs, a throwaway `APP_KEY`) so CI can run without real secrets.

## Environment variables by concern

### Application

- `APP_NAME`, `APP_ENV` (`local`/`testing`/`production`), `APP_KEY`, `APP_DEBUG`, `APP_URL`, `APP_TIMEZONE`
- `APP_LOCALE=de`, `APP_FALLBACK_LOCALE=de`, `APP_FAKER_LOCALE=de_CH` — German is primary; see [Translation Workflow](../Internationalization/41 Translation Workflow.md)
- `APP_CONTACT_LINK` / `APP_CONTACT_TEXT` — optional footer contact link
- `SESSION_*`, `CACHE_STORE`, `QUEUE_CONNECTION`, `LOG_*` — Laravel infrastructure. In production the deploy forces `SESSION_SECURE_COOKIE=true`.

### Database

`DB_CONNECTION=mysql`, `DB_HOST` (`db` locally), `DB_PORT=3306`, `DB_DATABASE=qualix`, `DB_USERNAME=qualix`, `DB_PASSWORD=qualix`. Local credentials match the `db` service's `MYSQL_*` variables.

### Mail

`MAIL_MAILER` (`smtp` locally, can be `sendmail`/`log`), `MAIL_HOST` (`mail`), `MAIL_PORT` (`1025` → Mailcatcher), `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`. In production the deploy script switches SMTP fields off when `MAIL_MAILER=sendmail`.

### Hitobito / MiData OAuth (`HITOBITO_*`)

Consumed in [config/services.php](../../config/services.php):

- `HITOBITO_BASE_URL` — the hitobito/MiData instance (e.g. `https://pbs.puzzle.ch`; `http://e2e-mocks:1080` in E2E)
- `HITOBITO_CLIENT_UID` → `client_id`, `HITOBITO_CLIENT_SECRET` → `client_secret`
- `HITOBITO_CALLBACK_URI` → `redirect` (`<APP_URL>/login/hitobito/callback`)

See [Authentication & Authorization](../Architecture/14 Authentication and Authorization.md).

### Collaboration (`COLLABORATION_*`)

Real-time feedback editing via Yjs/y-webrtc (see [Feedback System & Collaborative Editing](../Features/24 Feedback System and Collaborative Editing.md)):

- `COLLABORATION_ENABLED` (`true`/`false`; `false` in E2E)
- `COLLABORATION_SIGNALING_SERVERS` — WebRTC signaling server(s), e.g. `"wss://y-webrtc-eu.fly.dev"`

The signaling server URL is also allowlisted in the CSP `connect-src` header (see `app/Http/Middleware/SecurityHeaders.php`).

### Error tracking (`SENTRY_*` / `VITE_SENTRY_*`)

- `SENTRY_LARAVEL_DSN` — backend DSN (read by [config/sentry.php](../../config/sentry.php), falling back to `SENTRY_DSN`)
- `SENTRY_RELEASE` — release identifier (set to the git SHA at deploy time)
- `SENTRY_CSP_REPORT_URI` — endpoint for CSP violation reports
- `VITE_SENTRY_VUE_DSN` — frontend DSN, exposed to the Vue bundle (read in [config/app.php](../../config/app.php) `sentry.frontend.vue_dsn` and in `resources/js/app.js`)
- `VITE_SENTRY_RELEASE` — frontend release identifier

Full details in [Error Tracking](../Infrastructure/34 Error Tracking.md).

### Translation / Phrase

Translations are managed in Phrase; the CLI config lives in [.phraseapp.yml](../../.phraseapp.yml) (project id and push/pull mappings for `lang/`). The Phrase access token is not committed — it is supplied to the Phrase CLI out of band. Runtime locale is controlled by the `APP_LOCALE`/`APP_FALLBACK_LOCALE` variables above. See [Translation Workflow](../Internationalization/41 Translation Workflow.md).

## Production secrets handling

Nothing sensitive is committed. In production the `.env` is generated during deployment by [`.github/actions/deploy/deploy.sh`](../../.github/actions/deploy/deploy.sh): it copies `.env.example` and `sed`-replaces each value from environment variables that the deploy composite action ([action.yml](../../.github/actions/deploy/action.yml)) receives from **GitHub Actions secrets** (scoped to the target GitHub environment). Notable transforms:

- `APP_ENV=production`, `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`
- `HITOBITO_CALLBACK_URI` derived from `APP_URL`
- `SENTRY_RELEASE` / `VITE_SENTRY_RELEASE` set to `git rev-parse HEAD`

The generated `.env` is then rsync'd to the server (it is explicitly `--include`d while other dotfiles are excluded). See [Continuous Deployment](../Infrastructure/33 Continuous Deployment.md) for the full flow.

### Per-environment secrets

There is **one set of these secrets per GitHub environment** — `prod`, `gryfensee`, `gloggi`, etc. (see the [deploy branches table](../Infrastructure/33 Continuous Deployment.md#deploy-branches--environments)). The deploy job binds to the environment matching its `deploy-<env>` branch, so each environment defines its own values; they are the only thing that makes one branch deploy to cyon and another to Hostpoint. The composite action ([action.yml](../../.github/actions/deploy/action.yml)) forwards them into `deploy.sh`. Beyond the app/DB/mail/HITOBITO/COLLABORATION/SENTRY variables above, each environment must provide the **deploy target**, which is *not* part of the app `.env`:

- `ssh-host` (`SSH_HOST`) — the server to deploy to (selects cyon vs. Hostpoint), **required**
- `ssh-username` (`SSH_USERNAME`) — SSH login user (default `root`)
- `ssh-directory` (`SSH_DIRECTORY`) — target directory on the server (default `.`)

Because `HITOBITO_CALLBACK_URI` is derived from `APP_URL`, a new environment also needs its callback URI allowlisted in the corresponding hitobito/MiData instance. Adding an environment therefore means: create the `deploy-<env>` branch, create the matching GitHub environment, and populate its secrets (at minimum `SSH_HOST`, `APP_URL`, `APP_KEY`, `DB_*`).

### Rotating `SSH_KNOWN_HOSTS`

When the hosting provider rotates the server's SSH host keys, deploys start failing at the pre-flight SSH step with `Host key verification failed`, even though the server is reachable. Regenerate the secret with `ssh-keyscan` — but scan **the exact string in `SSH_HOST`, not the `APP_URL` domain**. On these hostings `SSH_HOST` is the bare domain without the `qualix.` subdomain, and a `known_hosts` entry only matches the name ssh actually connects to:

```bash
ssh-keyscan flamberg.ch      # deploy-prod       — NOT qualix.flamberg.ch
ssh-keyscan gryfensee.ch     # deploy-gryfensee
ssh-keyscan gloggi.ch        # deploy-gloggi
```

Paste the output verbatim into the environment's `SSH_KNOWN_HOSTS` secret. (GitHub masks the value if you try to echo it from a workflow; regenerating locally against the known host is simpler than extracting it.)
