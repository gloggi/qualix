# CI Pipeline

[← Technical Documentation](../01 Technical Documentation TOC.md)

Continuous integration runs on GitHub Actions. There is one CI workflow ([ci.yml](../../.github/workflows/ci.yml)) that runs the full test suite and, on the special `deploy-*` branches, also deploys. A separate scheduled workflow ([nightly.yml](../../.github/workflows/nightly.yml)) triggers the nightly production deployment — see [Continuous Deployment](../Infrastructure/33 Continuous Deployment.md).

The whole pipeline runs the app through the same Docker Compose stack used for local development, so CI and local behaviour match. See [Environment Configuration](../Infrastructure/32 Environment Configuration.md) for the services, and [Testing Strategy & Guide](../Testing/51 Testing Strategy.md) for how the individual test suites work.

## Triggers

```yaml
on:
  push:
  pull_request:
  workflow_dispatch:
```

CI runs on every push and every pull request (and can be started manually). There is no branch matrix — the same jobs run everywhere. Deployment steps are gated by additional `if` conditions (see below).

## Jobs

All jobs run on `ubuntu-latest`. The three test jobs are independent and run in parallel; each spins up its own Docker Compose stack and waits for it via the local composite action [`./.github/actions/wait-for-container-startup`](../../.github/actions/wait-for-container-startup/action.yml), which polls for `node_modules/.vite/deps/_metadata.json` (frontend ready) and a `.ready` marker file (backend up and DB migrated).

### `unit-tests` — "Unit tests" (PHPUnit)

```yaml
- run: docker compose up -d qualix db vite mail
- uses: ./.github/actions/wait-for-container-startup
- run: docker compose exec -T qualix php artisan config:clear --env=testing
- run: docker compose exec -T qualix vendor/bin/phpunit
```

The `config:clear --env=testing` step is required so the cached config does not shadow the `testing` environment (same step is documented for local runs in [AGENTS.md](../../AGENTS.md)). Runs both the `tests/Unit` and `tests/Feature` suites (see [phpunit.xml](../../phpunit.xml)).

### `frontend-tests` — "Frontend tests" (Vitest)

```yaml
- run: docker compose up -d qualix db vite mail
- uses: ./.github/actions/wait-for-container-startup
- run: docker compose exec -T vite npm run test
```

Runs the Vue component tests (`tests/Vue/components/*.spec.js`) in the `vite` container via Vitest + jsdom.

### `e2e-tests` — "End-to-end tests" (Playwright)

```yaml
- run: docker compose up -d qualix db vite worker-build mail e2e-mocks
- uses: ./.github/actions/wait-for-container-startup
- run: docker compose run e2e run
- uses: actions/upload-artifact@v4
  if: always()
  with:
    name: e2e-output
    path: ./tests/e2e/test-results/
```

End-to-end tests use Playwright (the `e2e` service is the `mcr.microsoft.com/playwright` image). Note the extra services this job needs: `worker-build` (builds the Yjs collaboration web-worker bundle) and `e2e-mocks` (a MockServer instance standing in for the hitobito OAuth provider). Test artifacts (traces, screenshots, videos) under `tests/e2e/test-results/` are uploaded on every run, including failures (`if: always()`).

### `ci-passed-event` — "Send out CI success event"

Runs only on `push` (`if: ${{ github.event_name == 'push' }}`) after the three test jobs pass. It fires a `repository_dispatch` event of type `ci-passed` (via `peter-evans/repository-dispatch`) carrying the ref and SHA, but only when the `REPO_ACCESS_TOKEN` secret is present. This lets external repositories and forks react to a green build in the gloggi/qualix repository.

### `get-environment-name` — "Extract environment name"

Parses the git ref with `sed` to extract an environment name from branches named `deploy-<env>`:

```bash
echo "environment=$(echo $GITHUB_REF | sed -e '/^refs\/heads\/deploy-\(.*\)$/!d;s//\1/')" >> $GITHUB_OUTPUT
```

For a push to `deploy-prod` this outputs `environment=prod`; for any other branch the output is empty, which disables the deploy job. The nightly workflow syncs `master` into `deploy-prod` to trigger this path.

### `deploy` — "Deploy"

See the dedicated [Continuous Deployment Documentation](./33 Continuous Deployment.md).

## Caching

There is no GitHub Actions dependency cache configured. Composer and npm dependencies are installed fresh inside the containers on each run (`npm install` in the `vite`/`worker-build` service command, `composer install` implied by the image). Docker layer caching is whatever the runner provides by default.

## Secrets used

The CI workflow references (only in the deploy path) `REPO_ACCESS_TOKEN`, `SSH_PRIVATE_KEY`, `SSH_KNOWN_HOSTS`, `SSH_USERNAME`, `SSH_HOST`, `SSH_DIRECTORY`, and the full set of application/DB/mail/HITOBITO/COLLABORATION/SENTRY secrets forwarded to the deploy action, plus `SENTRY_RELEASE_TRACKING_AUTH_TOKEN`, `SENTRY_ORG`, `SENTRY_PROJECTS` for the release step. These are configured per GitHub environment. See [Environment Configuration](../Infrastructure/32 Environment Configuration.md) for what each maps to.
