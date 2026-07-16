# Architecture Decisions

[← Technical Documentation](../01 Technical Documentation TOC.md)

Short ADR-style entries for the decisions that shape Qualix — the context, the
decision, and its consequences — each citing the files (and, where they exist,
the issues/PRs) that evidence it.

See also: [Application Architecture Overview](../Architecture/11 Application Architecture Overview.md),
[Frontend Architecture](../Architecture/13 Frontend Architecture.md),
[Authentication & Authorization](../Architecture/14 Authentication and Authorization.md),
[Continuous Deployment](../Infrastructure/33 Continuous Deployment.md).

***

## ADR-1: Server-rendered Blade with islands of Vue, not a SPA, no API

**Context.** Qualix is a form-heavy CRUD app (courses, participants,
observations, feedbacks) where most pages are ordinary server-rendered views,
but a few surfaces (observation lists, group generators, the feedback editor)
need rich client-side interactivity. The data in Qualix is also mostly too sensitive to be consumed by other apps.

**Decision.** Pages are Blade views ([`resources/views`](../../resources/views))
that mount a single global Vue 3 app on `#app`
([`resources/js/app.js`](../../resources/js/app.js)). No HTTP API for external applications is developed. Every
`resources/js/components/**/*.vue` is auto-registered as a global component by
filename via `import.meta.glob`, so components can be dropped into Blade without
explicit imports. Laravel route metadata is passed to JS through a
`#laravel-data` element and consumed via the injected `this.routeUri()` /
`this.routeMethod()` globals rather than hardcoded URLs
([`resources/js/app.js`](../../resources/js/app.js)).

**Consequences.**
- Routing, auth, and validation stay in Laravel; there is no client-side router
  for page navigation and no separate API layer to keep in sync.
- Vue is used as progressive enhancement — "islands" inside server-rendered
  HTML — so most pages work as plain forms.
- Trade-off: state is split across Blade and Vue, and passing data from PHP to
  JS goes through the `#laravel-data` bridge instead of typed props.

See [Frontend Architecture](../Architecture/13 Frontend Architecture.md) for the mounting and
registration mechanics.

***

## ADR-2: Vue Options API only, never the Composition API

**Context.** Vue 3 supports two component authoring styles. The project depends
on `vue@^3.5` ([`package.json`](../../package.json)).

**Decision.** Components use the **Options API exclusively**. Our logic isn't complex enough to justify deviating from the easy-to-read options API, we don't need the community-developed composition libraries and we don't use TypeScript.

**Consequences.**
- All existing components follow one consistent style; contributors (and AI
  agents) must not introduce `<script setup>` / `setup()` / composables.
- Trade-off: the project forgoes Composition-API ergonomics (composable reuse,
  better TS inference) in exchange for a single, uniform pattern across the
  codebase.

***

## ADR-3: Deploy to PHP-only shared hosting by building in CI and syncing files

**Context.** The live instance runs at <https://qualix.flamberg.ch> on
cyon.ch-style shared PHP hosting reached over SSH (the deploy script even
links to cyon's PHP-version docs). Qualix is deliberately built to run on
**plain shared PHP hosting with no container runtime or root access** — there is
no CI-driven build *on* the production server. This is so that Qualix is as easy and cheap to run as possible, both the `qualix.flamberg.ch` as well as forks or private instances.

**Decision.** Deployment is a GitHub Actions job that builds artifacts in CI and
pushes them to the host via `rsync` and SSH.

**Consequences.**
- No build toolchain, Docker, or root access is required on the production host
  — it only needs a compatible PHP CLI (the deployment script hard-checks
  `PHP_VERSION_ID` against composer's `platform_check.php` before uploading).
- Nightly-only deploys mean `master` changes go live in a batch each morning,
  not continuously.

The upload mechanism is documented in [Continuous Deployment](../Infrastructure/33 Continuous Deployment.md); see also [CI Pipeline](../Infrastructure/31 CI Pipeline.md).

***

## ADR-4: No `env()` outside config files, so the config can be cached

Once `config:cache` runs (which the deploy does), Laravel stops loading
`.env`, so any `env()` call outside a config file returns `null` in production.
All environment access therefore goes through `config/*.php`; `env()` is never
called from application code — which unlocks the cached,
fast-boot deploy in ADR-3. New code reads settings via `config('...')`; new
settings are added as a config key backed by `env()` in the config file.

***

## ADR-5: No SemVer — a human-readable CHANGELOG instead of versioned releases

**Context.** A versioning system (SemVer releases, git tags/GitHub releases,
"what's new" on the welcome page) has previously been requested so course leaders could see what changed ([#108](https://github.com/gloggi/qualix/issues/108)).

**Decision.** Qualix keeps a plain `CHANGELOG.md` (dual-language) and
links to it from the welcome page, but **deliberately does not adopt SemVer**, because for a continuously-deployed end-user web app the
definition of a "breaking change" is not meaningful to users .

**Consequences.**
- Change communication is prose in `CHANGELOG.md` / `CHANGELOG_fr.md`, not
  version numbers — which is exactly why the "every user-facing feature needs a
  changelog entry" rule exists.
- There are no release tags gating deploys; `master` ships on the nightly
  cadence regardless of a version bump.

***

## ADR-6: Course-scoped routing via `scopeBindings()`

**Context.** Almost every resource (participants, observations, blocks,
requirements, feedbacks, evaluation grids) belongs to a `Course`. Nested routes
must not let a user reach a child of a course they didn't request.

**Decision.** Course-scoped routes are nested under `/course/{course}/...` in a
group with `->scopeBindings()`, so child route-model bindings are automatically
constrained to belong to the bound `{course}`
([`routes/web.php`](../../routes/web.php), line 29:
`Route::middleware(['auth', 'verified', 'restoreFormData'])->scopeBindings()->group(...)`).

**Consequences.**
- Cross-course access is prevented by the framework's binding resolution, not by
  hand-written `where('course_id', …)` checks in every controller.
- New nested resources get this scoping for free by being added to the right
  route group — so "which group does this endpoint belong in?" is the key
  question when adding an endpoint.

See [Application Architecture Overview](../Architecture/11 Application Architecture Overview.md).

***

## ADR-7: Feedback content stored as structured nodes, not an HTML/JSON blob

**Context.** Feedback write-ups are rich text edited with Tiptap. A naive
approach stores the whole document as one HTML or JSON blob per feedback.

**Decision.** Feedback content is decomposed into `FeedbackContentNode` rows —
"components of the TipTap rich text feedback content, stored separately instead
of as big HTML/JSON chunks **in order to enforce database constraints such as
relations to requirements**" ([AGENTS.md](../../AGENTS.md), "Key domain models";
[`app/Models/FeedbackContentNode.php`](../../app/Models/FeedbackContentNode.php);
serialization via `TiptapFormatter` / `FeedbackContentController`).

**Consequences.**
- A node that references a `Requirement` is a real foreign-key relation, so
  referential integrity (and cascade behaviour) is enforced by the DB rather
  than by parsing a blob or JSON column.
- Trade-off: reading/writing the document means (de)serializing between the
  Tiptap document tree and node rows — the reason `TiptapFormatter` exists.

See [Domain Model & Database Schema](../Architecture/12 Domain Model and Database Schema.md)
and [Feedback System & Collaborative Editing](../Features/24 Feedback System and Collaborative Editing.md).

***

## ADR-8: Real-time collaborative feedback editing via Yjs / y-webrtc

**Context.** Multiple trainers may edit the same feedback write-up
concurrently.

**Decision.** The Tiptap editor uses Yjs CRDTs with peer-to-peer sync over
`y-webrtc` (`yjs`, `y-webrtc`, `@tiptap/extension-collaboration*` in
[`package.json`](../../package.json)), gated by `COLLABORATION_*` env vars and a
configurable signaling server (default `wss://y-webrtc-eu.fly.dev`,
[`README.md`](../../README.md), [`.github/actions/deploy/action.yml`](../../.github/actions/deploy/action.yml)).
Collaboration can be disabled per deployment.

**Consequences.**
- Edits sync peer-to-peer between browsers; only a lightweight signaling server
  is needed, not a stateful collaboration backend on the PHP host — which fits
  the shared-hosting model (ADR-3).
- Trade-off: WebRTC connectivity and an external signaling server are required
  for the collaborative path; offline handling is surfaced explicitly in the
  editor (see CHANGELOG, May 2025, issue #342).

See [Feedback System & Collaborative Editing](../Features/24 Feedback System and Collaborative Editing.md).
