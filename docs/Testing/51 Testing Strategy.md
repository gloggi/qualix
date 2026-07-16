# Testing Strategy & Guide

[← Technical Documentation](../01 Technical Documentation TOC.md)

Qualix has three test layers, each with its own runner and Docker container. As a rule ([AGENTS.md](../../AGENTS.md)): **always add tests for new features, changes, and bugfixes.** There are many unit/feature tests; end-to-end tests are written only for the most essential user flows.

| Layer | Runner | Container | Location |
| --- | --- | --- | --- |
| Backend unit + feature | PHPUnit | `qualix` | `tests/Unit`, `tests/Feature` |
| Frontend component | Vitest | `vite` | `tests/Vue/components/*.spec.js` |
| End-to-end | Playwright | `e2e` | `tests/e2e/tests/*.spec.js` |

Everything runs in Docker (`docker compose up`); there is no local PHP/Node/Playwright install expected.

## Backend: PHPUnit

Config: [phpunit.xml](../../phpunit.xml). Two suites are defined, both matching files suffixed `Test.php`:

- **`tests/Unit`** — isolated unit tests.
- **`tests/Feature`** — HTTP/feature tests, organized to **mirror the controllers** they exercise: `tests/Feature/Admin`, `tests/Feature/Observation`, `tests/Feature/FeedbackContent`, `tests/Feature/EvaluationGrid`, `tests/Feature/Auth`, etc.

Base classes under `tests/` bootstrap common fixtures: `tests/TestCase.php`, `tests/TestCaseWithBasicData.php`, `tests/TestCaseWithCourse.php` (most course-scoped feature tests extend the last one). [phpunit.xml](../../phpunit.xml) forces the `testing` env with array/sync drivers (`CACHE_STORE=array`, `QUEUE_CONNECTION=sync`, `MAIL_MAILER=array`, etc.).

### Running

One-time before running unit tests — clears cached config so the `testing` env is picked up:

```
docker compose exec qualix php artisan config:clear --env=testing
```

Then:

```
docker compose exec qualix vendor/bin/phpunit
docker compose exec qualix vendor/bin/phpunit --filter=Course
```

### Model factories

Factories live in [`database/factories/`](../../database/factories/) — one per model (`Course`, `Participant`, `Block`, `Observation`, `Feedback`, `FeedbackData`, `FeedbackContentNode`, `Requirement`, `RequirementStatus`, the evaluation-grid models, and three user factories: `UserFactory`, `NativeUserFactory`, `HitobitoUserFactory`, because auth is polymorphic — see [Authentication](../Architecture/14 Authentication and Authorization.md)).

Conventions to know before writing one:

- **State methods double as builder mixins.** Beyond plain `->state(...)` closures that set columns, factories expose `afterCreating(...)` state helpers that wire up many-to-many relations, e.g. `ObservationFactory::withRequirements()` / `maybeMultiParticipant()`, `FeedbackFactory::withContents()` / `withObservations()`. There are **no `configure()` hooks** — all post-create wiring is per-state.
- **Relationship building** uses Laravel's `has()` / `for()` / `forEachSequence()` and explicit `Sequence` objects. `FeedbackFactory::forParticipants()` cycles generated feedbacks through the course's participants via a `Sequence`; `EvaluationGridFactory::withRows()` uses `for($grid)->forEachSequence(...)` to create one row per row-template.
- **Join-ish models** (`Feedback`, `EvaluationGrid`) have an empty `definition()` — all meaningful data comes from state methods.
- `ParticipantFactory::withImage($bool)` downloads a face image and stores it via `Storage` — pass `false` (the default) to skip the network hit in tests.

The PHPUnit base classes are a **mix**: `tests/TestCaseWithCourse.php` / `TestCaseWithBasicData.php` build the course, participants, blocks, observations with plain `Model::create()`, but use factories for the logged-in user (`NativeUser::factory()`) and the evaluation-grid helpers (`EvaluationGridTemplate::factory()->withBlocks(1)->withRequirements(1)->withRowTemplates(...)`). New tests should prefer factories.

The e2e layer drives the same factories over HTTP — see [`e2e:scenario`](#the-e2escenario-fixture-command) and the `/__e2e__/create` / `/__e2e__/generate` routes below.

> **Keep factories and `e2e:scenario` in sync with the models.** When you add a model or change one (new required columns, new relations), add or update its factory **and** extend the [`e2e:scenario`](#the-e2escenario-fixture-command) chain so the seeded course still exercises the new field/relation. Otherwise the manual-testing environment (and the e2e specs built on it) silently stops covering that part of the app.

## Frontend: Vitest

Config: [vitest.config.js](../../vitest.config.js) (merges [vite.config.js](../../vite.config.js)). Runs under **jsdom** with globals enabled; test files are `tests/Vue/**/*.spec.js`, setup in [`tests/Vue/setup.js`](../../tests/Vue/setup.js) (plus custom matchers in `tests/Vue/custom-matchers.js`). Component tests live in [`tests/Vue/components`](../../tests/Vue/components) and use `@testing-library/vue`. The config injects the compiled Laravel translations into `resources/js/i18n.js` so `$t()` resolves in tests just as in the app.

### Running

```
docker compose exec vite npm run test
docker compose exec vite npm run test -- ObservationList
```

(`npm run test` maps to `vitest run`; `test:watch` maps to `vitest` in watch mode — see [package.json](../../package.json).)

## End-to-end: Playwright

Config: [playwright.config.js](../../playwright.config.js). Specs live in [`tests/e2e/tests/*.spec.js`](../../tests/e2e/tests) — one per essential or extremely frontend-heavy flow: `login`, `register`, `invite`, `observations`, `feedbacks`, `evaluation-grids`, `requirements-matrix`, `name-game`, `participant-group-generator`. Tests run serially (`fullyParallel: false`, `workers: 1`, `retries: 3`) against `http://localhost` across three browser projects (Firefox, Chromium, WebKit).

### Running

```
docker compose run e2e run          # headless
docker compose run e2e open         # interactive UI at http://localhost:1100
```

### The `/__e2e__/*` test-support routes

E2E tests drive the real app over HTTP and steer its backend through special routes defined in [`routes/e2e.php`](../../routes/e2e.php), handled by [`App\Http\Controllers\E2ETestingController`](../../app/Http/Controllers/E2ETestingController.php). The controller's constructor calls `abort_unless(app()->environment('testing', 'local'), 404)`, so these routes exist **only in `local`/`testing`** and 404 in production. Available endpoints:

| Route | Purpose |
| --- | --- |
| `GET /__e2e__/csrf_token` | Fetch a CSRF token for subsequent POSTs |
| `POST /__e2e__/login` / `logout` | Log in as a user (created via factory if absent) / log out |
| `POST /__e2e__/create` | Create + persist model fixtures via factories (with states, `count`, `load`) |
| `POST /__e2e__/generate` | Build (unsaved) model instances via factories |
| `POST /__e2e__/artisan` | Run an arbitrary artisan command |
| `POST /__e2e__/run-php` | `eval()` arbitrary PHP and return the result |
| `GET /__e2e__/create-snapshot/{name?}` | `snapshot:create` — DB snapshot |
| `GET /__e2e__/restore-snapshot/{name?}` | `snapshot:load` — restore DB between tests |
| `GET /__e2e__/cleanup-snapshots` | `snapshot:cleanup --keep=0` |

### The `e2e:scenario` fixture command

[`app/Console/Commands/E2EScenario.php`](../../app/Console/Commands/E2EScenario.php) (`e2e:scenario`) seeds one fully-populated course in a single factory chain — the standard starting fixture for e2e specs. Signature:

```
e2e:scenario {--user-id=} {--with-images}
```

- `--user-id=` — attach the generated course to this existing user (defaults to `User::first()`; errors if the DB has no users).
- `--with-images` — forwarded to `ParticipantFactory::withImage()`, which fetches AI-generated (not real) faces as participant images (off by default; needs network access). It aborts in `production`.

The one scenario it produces is defined inline in `handle()` and is always the same shape (randomised via Faker, structurally fixed): a course with ~4 users, 4 requirements, 3 requirement statuses, 10 participants, 10 blocks × 5 observations, one feedback round × 10 feedbacks (with contents, requirements and observations), and 2 evaluation-grid templates × 4 grids. It is essentially the orchestration layer over the [factories](#model-factories) above — every helper it calls (`fromRandomUser`, `withRequirements`, `forParticipants`, `withContents`, `withRows`, …) is a factory state method.

**Manual local testing.** The usual way to get a populated environment to click around in:

1. Start the app (`docker compose up`) and open <http://localhost>.
2. Register / log in, so a user exists and you have a session.
3. Attach a fully-populated course to your user:

   ```
   docker compose exec qualix php artisan e2e:scenario
   ```

   With a single user this is enough (it defaults to `User::first()`); otherwise pass `--user-id=<your id>`. Add `--with-images` if you want AI-generated (not real) participant faces.
4. Reload the page — the new course now appears in the course switcher.

Playwright specs invoke it per-test in `beforeEach` via the `artisan` fixture, which POSTs to `/__e2e__/artisan`:

```js
await artisan('e2e:scenario', { '--user-id': user.id })
```

It has **no direct coupling to snapshots** — DB isolation is a separate concern handled in the Playwright layer by the `useDatabaseResets` fixture ([`tests/e2e/fixtures/db.js`](../../tests/e2e/fixtures/db.js)), which snapshots before and restores after each test via the `create-snapshot`/`restore-snapshot` routes (backed by `spatie/laravel-db-snapshots`). A typical spec seeds fixtures with `e2e:scenario`, and the snapshot fixture rolls the DB back around each test. The command itself is also covered by a unit test, [`tests/Unit/Console/E2EScenarioTest.php`](../../tests/Unit/Console/E2EScenarioTest.php).

### `.env.e2e` swap (global setup/teardown)

[`tests/e2e/global-setup.js`](../../tests/e2e/global-setup.js) and [`global-teardown.js`](../../tests/e2e/global-teardown.js) swap the environment file around the run:

- **Setup**: if `.env.e2e` exists, back up the current `.env` to `.env.backup` and copy `.env.e2e` → `.env`, then `config:clear` + `cache:clear` via the artisan route. `.env.e2e` points at the same `db` container in `APP_ENV=testing`.
- **Teardown**: clean up snapshots, restore `.env.backup` → `.env` (saving the e2e one back to `.env.e2e`), and clear config/cache again.

Fixtures for specs live in `tests/e2e/fixtures`; failure output (screenshots, traces) lands in `tests/e2e/test-results` (`screenshot: only-on-failure`, `trace: on-first-retry`).

## CI

All three layers run in GitHub Actions ([`.github/workflows/ci.yml`](../../.github/workflows/ci.yml)) as separate jobs — PHPUnit (after the same `config:clear --env=testing` step), `npm run test`, and `docker compose run e2e run` (with e2e output uploaded as an artifact). See [CI Pipeline](../Infrastructure/31 CI Pipeline.md) for the full workflow.

***

See also: [CI Pipeline](../Infrastructure/31 CI Pipeline.md) · [Frontend Architecture](../Architecture/13 Frontend Architecture.md) · [Contributing Guidelines](../../CONTRIBUTING.md)
