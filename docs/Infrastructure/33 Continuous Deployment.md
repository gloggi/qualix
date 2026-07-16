# Continuous Deployment

[← Technical Documentation](../01 Technical Documentation TOC.md)

Qualix is deployed to traditional PHP shared hosting (cyon), so deployment is a file sync over SSH plus a handful of `artisan` cache/migrate commands — there is no container registry or orchestration in production. Deployment is driven entirely from GitHub Actions and is gated behind a green [CI Pipeline](../Infrastructure/31 CI Pipeline.md).

## Overview

1. A push to a `deploy-<env>` branch triggers CI ([ci.yml](../../.github/workflows/ci.yml)).
2. After the unit, frontend and E2E test jobs pass, the `deploy` job runs.
3. The `deploy` job builds a production `.env`, builds frontend assets and prod PHP dependencies, and rsyncs the tree to the server over SSH.
4. Post-upload, it runs migrations and rebuilds Laravel caches, then brings the app back up.
5. A Sentry release is created for the environment.

Production uses the `deploy-prod` branch (extracted environment name `prod`). Other `deploy-*` branches target their own GitHub environment — see [Deploy branches & environments](#deploy-branches--environments).

## Deploy branches & environments

The environment is derived entirely from the branch name: pushing to `deploy-<env>` deploys to the GitHub environment `<env>`. There is no per-environment code or config in the repo — the branch name selects a GitHub `environment:`, and that environment's scoped secrets (`SSH_HOST`, `SSH_USERNAME`, `SSH_DIRECTORY`, `APP_URL`, `DB_*`, …) point the same [deploy script](#the-deploy-script) at a different server. To add an environment you create the branch and the matching GitHub environment with its secrets; no code change is needed.

| Branch | Purpose | URL | Host |
| --- | --- | --- | --- |
| `deploy-prod` | Production | <https://qualix.flamberg.ch> | cyon (shared hosting) |
| `deploy-gryfensee` | Test environment | <https://qualix.gryfensee.ch> | cyon (shared hosting) |
| `deploy-gloggi` | Test environment | <https://qualix.gloggi.ch> | Hostpoint (shared hosting) |

The target host is not hardcoded anywhere: `SSH_HOST`/`SSH_USERNAME`/`SSH_DIRECTORY` (rsync destination) and `APP_URL` (public URL, and the base for `HITOBITO_CALLBACK_URI`) come from the selected environment's secrets. See [Environment Configuration](../Infrastructure/32 Environment Configuration.md#production-secrets-handling) for the full variable map.

## Nightly production deploy

Production is deployed once a day. The scheduled workflow [nightly.yml](../../.github/workflows/nightly.yml) does **not** deploy directly — it fast-forwards `master` onto the `deploy-prod` branch, and the resulting push triggers the CI + deploy pipeline:

```yaml
on:
  schedule:
    # 03:30 UTC is either 04:30 or 05:30 in CET (swiss time), depending on daylight savings time
    - cron: '30 3 * * *'
jobs:
  sync-deploy-branch:
    steps:
      - uses: repo-sync/github-sync@v2
        with:
          source_repo: 'gloggi/qualix'
          source_branch: 'master'
          destination_branch: 'deploy-prod'
          github_token: ${{ secrets.REPO_ACCESS_TOKEN }}
```

So: merge to `master` during the day → the nightly job promotes `master` to `deploy-prod` at ~03:30 UTC → CI runs → if green, production is updated. You can also promote manually by pushing to `deploy-prod` yourself, although it might be reverted by the next nightly job.

## The deploy job

From [ci.yml](../../.github/workflows/ci.yml), the `deploy` job:

1. `apt-get install rsync`.
2. Loads the deploy SSH key with `shimataro/ssh-key-action` (`SSH_PRIVATE_KEY`, `SSH_KNOWN_HOSTS`).
3. Runs the composite action [`./.github/actions/deploy`](../../.github/actions/deploy/action.yml), forwarding all app/DB/mail/HITOBITO/COLLABORATION/SENTRY values from GitHub secrets.
4. Creates a Sentry release with `getsentry/action-release` (only if `SENTRY_RELEASE_TRACKING_AUTH_TOKEN` is set) — see [Error Tracking](../Infrastructure/34 Error Tracking.md).

## The deploy script

The heavy lifting is in [`.github/actions/deploy/deploy.sh`](../../.github/actions/deploy/deploy.sh) (`set -e`):

### 1. Build the production `.env`

Copies `.env.example` to `.env` and `sed`-replaces each key from the forwarded environment variables (secrets). It forces `APP_ENV=production` (via the action default), `SESSION_SECURE_COOKIE=true`, derives `HITOBITO_CALLBACK_URI` from `APP_URL`, and sets both `SENTRY_RELEASE` and `VITE_SENTRY_RELEASE` to `$(git rev-parse HEAD)`. See [Environment Configuration](../Infrastructure/32 Environment Configuration.md#production-secrets-handling) for the full variable map. No secret values are ever committed.

### 2. Build assets and prod dependencies

```bash
docker compose run --no-deps --entrypoint "/bin/sh -c 'npm install && scripts/install-twemoji.sh && npm run build && npm run worker:build'" vite
docker compose run --no-deps --entrypoint "composer install --no-dev" qualix
```

This produces the compiled Vite assets and the collaboration worker bundle, plus a `vendor/` without dev dependencies. It also reads the required minimum PHP version out of `vendor/composer/platform_check.php` for the check in the next step.

### 3. Pre-flight and maintenance mode (over SSH)

`ssh-keyscan` the host, verify the server's CLI PHP is new enough (aborts with cyon-specific guidance otherwise), then put the app into maintenance mode:

```bash
APP_CONTACT_LINK=$APP_CONTACT_LINK php artisan down --render=updating
```

### 4. Upload with rsync

A single incremental `rsync` over SSH is run:

```bash
rsync -az --delete \
  --exclude=node_modules \
  --exclude=tests \
  --exclude=storage/logs \
  --exclude=storage/app \
  --exclude=storage/framework/maintenance.php \
  --exclude=storage/framework/down \
  --exclude=resources/fonts \
  --exclude=resources/images \
  --exclude=resources/js \
  --exclude=resources/sass \
  --exclude=resources/twemoji \
  --include=/.env \
  --exclude=/.* \
  ./ "$SSH_USERNAME@$SSH_HOST:$SSH_DIRECTORY/"
```

Key points:

- `-a` archive, `-z` compression, `--delete` removes files on the server that no longer exist locally (so the server tree mirrors the build output).
- Source assets that have already been compiled (`resources/js`, `resources/sass`, `resources/fonts`, `resources/images`, `resources/twemoji`) and `tests/` are excluded — only build output and runtime code ship.
- Runtime state on the server is preserved: `storage/logs`, `storage/app`, and the maintenance/down markers are excluded so they are not clobbered.
- `--include=/.env` before `--exclude=/.*` ships the freshly generated production `.env` while keeping all other dotfiles local-only.

### 5. Post-upload (over SSH)

```bash
php artisan storage:link
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan up
```

Migrations run with `--force` (non-interactive), the config/route/view caches are rebuilt against the new `.env`, and `artisan up` exits maintenance mode.

## Required secrets

Configured per GitHub environment (see the `with:` block of the `deploy` job in [ci.yml](../../.github/workflows/ci.yml)): `SSH_PRIVATE_KEY`, `SSH_KNOWN_HOSTS`, `SSH_USERNAME`, `SSH_HOST`, `SSH_DIRECTORY`, `APP_*`, `DB_*`, `MAIL_*`, `HITOBITO_*`, `COLLABORATION_*`, `SENTRY_LARAVEL_DSN`, `SENTRY_CSP_REPORT_URI`, `SENTRY_VUE_DSN`, plus `SENTRY_RELEASE_TRACKING_AUTH_TOKEN`, `SENTRY_ORG`, `SENTRY_PROJECTS` for the release step and `REPO_ACCESS_TOKEN` for the nightly sync.

For determining the value of `SSH_KNOWN_HOSTS`, see [Environment Configuration](./32 Environment Configuration.md#rotating-ssh_known_hosts).
