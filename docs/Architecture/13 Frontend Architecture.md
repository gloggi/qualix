# Frontend Architecture

[← Technical Documentation](../01 Technical Documentation TOC.md)

Qualix renders pages with Blade and enhances them with a single global **Vue 3** app. There is no SPA router; each Blade page ships its own markup and drops in Vue components inline. Entry point: [resources/js/app.js](../../resources/js/app.js); build config: [vite.config.js](../../vite.config.js).

> **Mandatory rule:** Vue components use the **Options API only. Never use the Composition API** (no `setup()`, no `<script setup>`). This is a hard project convention — see [Architecture Decisions](../Vision/62 Architecture Decisions.md).

## One global Vue app on `#app`

The Blade layout [layouts/default.blade.php](../../resources/views/layouts/default.blade.php) wraps the whole page body in a single mount point:

```blade
<div id="app" v-cloak>
    <b-container>
        @include('includes.header', ['navigation' => true])
        @yield('content')
    </b-container>
</div>
```

[app.js](../../resources/js/app.js) creates one app with an empty root (`createApp({})`) and mounts it on `#app`. Because Vue compiles the in-DOM template, any component tag written directly in Blade inside `#app` is rendered by Vue. The Vite alias `'vue' -> 'vue/dist/vue.esm-bundler'` (in [vite.config.js](../../vite.config.js)) enables this runtime template compilation.

## Automatic component registration

All single-file components under `resources/js/components/**/*.vue` are auto-registered globally by filename.

So `components/ObservationList.vue` becomes `<observation-list>` / `<ObservationList>` usable in any Blade template or other component (nested dirs like `components/feedback/` work the same — only the filename matters) — **no import or manual registration needed**. Components are lazy-loaded via `defineAsyncComponent`. Each component is a standard Options-API SFC (`export default { data(), methods: {}, ... }`).

## Bootstrap-Vue-Next

The app uses [bootstrap-vue-next](https://bootstrap-vue-next.github.io/) for UI components:

- **Inside `.vue` files**, BVN components are auto-imported/resolved at build time by `unplugin-vue-components` with `BootstrapVueNextResolver()` (configured in [vite.config.js](../../vite.config.js)) — write `<b-button>` and it just works.
- **Inside Blade templates**, that resolver can't see the tags, so the BVN components actually used in Blade are explicitly registered as globals in [app.js](../../resources/js/app.js) (the `bootstrapComponentsUsedInBlade` map: `BButton`, `BModal`, `BNavbar`, `BFormSelect`, …). If you use a new BVN component directly in a Blade file, add it to that map.
- Directives `v-b-toggle`, `v-b-tooltip`, `v-b-modal` and the plugin (`createBootstrap()`) are registered globally too.

## Shared form components

**Use these instead of hand-rolling `<input>`/`<textarea>`/`<select>` markup.** The wrappers in [resources/js/components/form/](../../resources/js/components/form) give every field the same layout, validation-error display, and old-input restoration for free — a raw input reimplements all of that (usually incompletely) and drifts from the rest of the app.

They all mix in [mixins/input.js](../../resources/js/mixins/input.js), which provides:

- **`v-model` support** plus a required `name` prop (matching the Laravel request field; use `foo[bar]` for nested/array fields).
- **Automatic validation errors**: reads `window.Laravel.errors` for `name` and renders the message as Bootstrap `invalid-feedback` — no wiring needed.
- **Old-input restoration**: initial value falls back to `window.Laravel.oldInput` so values survive a failed server-side validation round-trip or even when the user is logged out while submitting the form and needs to re-authenticate (see the [`restoreFormData` middleware](./11 Application Architecture Overview.md#key-middleware)).
- **Consistent responsive layout**: label + input column classes, switchable to a stacked layout via the `narrowForm` prop.

Available inputs (all take a `label`; most take `required`, `autofocus`):

- `<input-text>` — text/number/etc. (`type` prop), optional `#append` slot.
- `<input-textarea>` — with optional `limit` (maxlength); pairs with [`<char-limit>`](../../resources/js/components/CharLimit.vue).
- `<input-date>`, `<input-file>` (`accept`), `<input-checkbox>` (`switch`, `inline`), `<input-radio-button>` (`options`).
- `<input-multi-select>` — wraps [`<multi-select>`](../../resources/js/components/MultiSelect.vue) (vue-multiselect); `<input-multi-multi-select>` for a variable-length list of them.
- `<input-hidden>` — hidden field (also used internally for method spoofing / checkbox unchecked values).
- `<input-feedback-editor>` — Tiptap rich-text field.

Wrap fields in [`<form-basic>`](../../resources/js/components/FormBasic.vue), which renders a real `<form>` with the correct `action`/`method` from the route helpers and injects the CSRF token and `_method` spoofing hidden fields. Close with [`<button-submit>`](../../resources/js/components/form/ButtonSubmit.vue). Example (from [requirementStatuses/edit.blade.php](../../resources/views/admin/requirementStatuses/edit.blade.php)):

```blade
<form-basic :action="['admin.requirement_statuses.update', { course: {{ $course->id }}, requirement_status: {{ $requirementStatus->id }} }]">
    <input-text name="name" model-value="{{ $requirementStatus->name }}" label="{{ __('t.models.requirement_status.name') }}" required autofocus></input-text>
    <button-submit></button-submit>
</form-basic>
```

More elaborate, model-specific forms (e.g. [FormObservation.vue](../../resources/js/components/FormObservation.vue), [feedback/FormFeedbackData.vue](../../resources/js/components/feedback/FormFeedbackData.vue)) are themselves built out of these same shared inputs — follow that pattern rather than starting from raw HTML.

## `window.Laravel` and route helpers

Server data is handed to JS via a `#laravel-data` element in [layouts/master.blade.php](../../resources/views/layouts/master.blade.php), JSON-encoding old input, validation errors, all named routes (`{ name: { uri, method } }`), the CSRF token, collaboration signaling servers, and the username. [app.js](../../resources/js/app.js) parses it into `window.Laravel` and removes the element.

Two global helpers are registered on `app.config.globalProperties` so components can build URLs from route *names* instead of hardcoding paths:

```js
this.routeUri('observation.edit', { course: 1, observation: 42 })  // → "/course/1/observation/42"
this.routeMethod('observation.delete')                             // → "delete"
```

`routeUri` substitutes named params into the route's `uri`, drops optional params if unset, and appends any leftover params as query string. **Always use these instead of hardcoding URLs.** There is also a global `$window` and a `v-focus` directive (fixes autofocus/caret inside Vue-managed inputs and vue-multiselect).

## Other frontend wiring

- **i18n**: `i18n.js` plugin with translations bundled at build time by `vite-plugin-laravel-translations` (reads `lang/`). See [Translation Workflow](../Internationalization/41 Translation Workflow.md).
- **Sentry**: initialized only if `VITE_SENTRY_VUE_DSN` is set. See [Error Tracking](../Infrastructure/34 Error Tracking.md).
- **CSP**: `Vite::useCspNonce()` (server) plus `assetsInlineLimit: 0` in the build config — Vite must not inline images, which would violate the Content-Security-Policy. Images/fonts are pulled in via `import.meta.glob([...], { eager: true })`.
- **Tiptap + Yjs** power the collaborative feedback editor — see [Feedback System & Collaborative Editing](../Features/24 Feedback System and Collaborative Editing.md).

## Vite build

[vite.config.js](../../vite.config.js) uses `laravel-vite-plugin` with two inputs: `resources/sass/app.scss` and `resources/js/app.js`. Dev server runs on `localhost:5173` (the `vite` Docker container) with HMR/`refresh`. Run frontend commands in that container, e.g. `docker compose exec vite npm run test` (see [AGENTS.md](../../AGENTS.md) and [Testing Strategy](../Testing/51 Testing Strategy.md)). Component tests live in `tests/Vue/components/*.spec.js`.
