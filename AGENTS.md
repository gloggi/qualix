# AGENTS.md

This file provides guidance to AI agents when working with code in this repository.

## What this is

Qualix is a Laravel 11 (PHP >= 8.2) web app, server-rendered with Blade views that embed Vue 3 components (via Vite), used by Swiss Pfadi/Scouting J+S course leaders to track participant observations, feedbacks, and qualifications. German is the primary language; UI strings are translated to French via Phrase (`.phraseapp.yml`, `lang/`).

## Documentation

Detailed technical documentation (architecture, domain model, features, testing, deployment) lives under [`docs/`](./docs/01%20Technical%20Documentation%20TOC.md) — start at the table of contents. The feature guides are especially useful when touching a specific area:

- [Observations & Participant Tracking](./docs/Features/21%20Observations%20and%20Participant%20Tracking.md)
- [Evaluation Grids](./docs/Features/22%20Evaluation%20Grids.md)
- [Requirements & Qualifications](./docs/Features/23%20Requirements%20and%20Qualifications.md)
- [Feedback System & Collaborative Editing](./docs/Features/24%20Feedback%20System%20and%20Collaborative%20Editing.md) (incl. the requirements matrix overview)
- [Feedback Allocation Algorithm](./docs/Features/25%20Feedback%20Allocation%20Algorithm.md)
- [Course Setup & Teardown](./docs/Features/26%20Course%20Setup%20and%20Teardown.md) (imports, block/group generators, archiving vs. deleting)
- [PDF Rendering](./docs/Features/27%20PDF%20Rendering.md)

## Local development

Everything runs in Docker; there is no local PHP/Node/Playwright install expected.

```
docker compose up
```

- App: <http://localhost>
- phpMyAdmin: <http://localhost:8081>
- Mailcatcher (dev emails): <http://localhost:1080>

Run PHP/Composer/artisan commands inside the `qualix` container:

```
docker compose exec qualix composer update
docker compose exec qualix php artisan tinker
```

Frontend (Vite) commands run inside the `vite` container:

```
docker compose exec vite npm run test
```

## Working with the repo (mandatory rules)

- Always add tests for new features, changes and bugfixes
- We have many unit tests and write e2e tests only for the most essential user flows
- For the frontend tests, if at all possible, we prefer the testing-library approach: Full mount without mocked subcomponents, and simulate real user events using the user-event library.
- For every user-facing feature, write a CHANGELOG.md entry in German and CHANGELOG_fr.md in French
- Follow the code style of the repo, and follow best-practices of Laravel and Vue.js (exception: NEVER use the Vue 3 Composition API, ALWAYS use the Options API.)

## Tests

### PHP (PHPUnit)

One-time before running unit tests (clears cached config so `testing` env is picked up):

```
docker compose exec qualix php artisan config:clear --env=testing
```

Run tests:

```
docker compose exec qualix vendor/bin/phpunit
docker compose exec qualix vendor/bin/phpunit --filter=Course
```

Suites are `tests/Unit` and `tests/Feature` (see `phpunit.xml`). Feature tests are organized to mirror controllers (e.g. `tests/Feature/Admin/Feedback`, `tests/Feature/Observation`).

### Frontend (Vitest)

```
docker compose exec vite npm run test
docker compose exec vite npm run test -- ObservationList
```

Vue component tests live in `tests/Vue/components` as `*.spec.js`, using `@testing-library/vue` + jsdom (config in `vitest.config.js`, setup in `tests/Vue/setup.js`).

### End-to-end (Playwright)

```
docker compose run e2e run          # headless
docker compose run e2e open         # interactive UI at http://localhost:1100
```

Specs live in `tests/e2e/tests/*.spec.js`. E2E tests drive the real app over HTTP and use special `/__e2e__/*` routes (`routes/e2e.php`, `App\Http\Controllers\E2ETestingController`) — available only in `local`/`testing` environments — to log in as a user, create/generate model fixtures via factories, run arbitrary artisan commands, or snapshot/restore the DB between tests. Global setup/teardown swap in `.env.e2e`.

## Architecture

### Course-scoped resources

Almost the entire app operates within a `Course` (see `app/Models/Course.php`). Routes are nested under `/course/{course}/...` and grouped behind `Route::middleware(['auth', 'verified', 'restoreFormData'])->scopeBindings()` in `routes/web.php` — `scopeBindings()` means child route-model bindings (participants, observations, blocks, requirements, etc.) are automatically constrained to belong to the bound `{course}`. A separate `courseNotArchived` middleware (`app/Http/Middleware/CourseMustNotBeArchived.php`) blocks mutating routes once a course is archived; look at which route group a new endpoint belongs in before adding it.

### Blade + Vue hybrid frontend

Pages are Blade views (`resources/views`) that mount a single global Vue 3 app on `#app` (`resources/js/app.js`). All `resources/js/components/**/*.vue` are auto-registered as global Vue components by filename via `import.meta.glob`, so they can be dropped directly into Blade templates without explicit imports/registration. Laravel route info is passed to JS via a `#laravel-data` element parsed into `window.Laravel`; use the injected `this.routeUri(name, params)` / `this.routeMethod(name)` globals (not hardcoded URLs) to call Laravel routes from Vue components. Bootstrap-Vue-Next components are auto-resolved by `unplugin-vue-components` and also explicitly registered globally in `app.js` for use directly in Blade.

### User model (STI via Parental)

`App\Models\User` is extended via `tightenco/parental` into `NativeUser` (email/password login) and `HitobitoUser` (OAuth login via `laravel/socialite` against hitobito/MiData, the Swiss scouting association's identity provider — see `HITOBITO_*` env vars and `LoginController`). Both share the same `users` table; check `login_provider`/child class rather than adding parallel tables or auth logic.

### Domain services

- `app/Services/Import/` — parsing/importing external data: block schedules (`Blocks/ECamp3`, the eCamp v3 PDF export) and participant lists (`Participants/MiData` — Swiss scouting membership DB export). See [Course Setup & Teardown](./docs/Features/26%20Course%20Setup%20and%20Teardown.md).
- `app/Services/FeedbackAllocation/` — algorithm assigning participants to trainers for feedback sessions based on preferences/capacity (`FeedbackAllocator` interface, `DefaultFeedbackAllocator` implementation).
- `app/Services/Validation/` — custom Laravel validation rules scoped to a course (e.g. `ExistsInCourse`, `AllExistInCourse`) — reuse these instead of writing ad-hoc `exists:` rules when validating that a submitted ID belongs to the current course.
- Feedback content (free-text qualification writeups) is edited with Tiptap and supports real-time multi-user collaboration via Yjs/y-webrtc (`COLLABORATION_*` env vars) — see `FeedbackContentController` and `TiptapFormatter`.

### Key domain models

`Course` → `Participant`, `Block` (a lesson in the course), `ObservationAssignment` (a task to observe participants during a block) → `Observation` → `ParticipantObservation` (join table for the n to m relation `Observation` <-> `Participant`); `Requirement`/`RequirementStatus`/`RequirementDetail` (qualification criteria); `FeedbackData` (model holding shared data for all participants receiving a feedback)/`Feedback` (one feedback belonging to 1 participant)/`FeedbackContentNode` (components of the TipTap rich text feedback content, stored separately instead of as big HTML/JSON chunks in order to enforce database constraints such as relations to requirements); `EvaluationGridTemplate`/`EvaluationGrid`/`EvaluationGridRow(Template)` (structured assessment rubrics, printable). Look at `app/Models/Course.php`'s relations for the full graph before adding a new nested resource.
