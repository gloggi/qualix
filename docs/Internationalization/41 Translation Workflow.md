# Translation Workflow

[← Technical Documentation](../01 Technical Documentation TOC.md)

Qualix's primary language is **German**. All UI strings are authored in German and translated to other languages (currently **French**) using [Phrase](https://phrase.com), a hosted translation-management service. Configuration lives in [.phraseapp.yml](../../.phraseapp.yml).

## Where translations live

Translation files sit under [`lang/`](../../lang), one directory per locale plus a per-locale JSON file:

```
lang/
  de/            # German (source language)
    auth.php
    pagination.php
    passwords.php
    t.php         # the app's own strings — by far the largest file
    validation.php
  de.json         # "override" strings, keyed by their English source text
  fr/            # French (same file layout as de/)
  fr.json
```

Two formats coexist, and [.phraseapp.yml](../../.phraseapp.yml) maps each to a Phrase **tag**:

- **PHP array files** (`file_format: laravel`) — nested key/value arrays. `t.php` (tag `t`) holds all Qualix-specific strings; `auth.php`, `pagination.php`, `passwords.php`, `validation.php` are the standard Laravel framework strings (tags of the same name). Keys are dot-paths, e.g. `t.global.add` → `"Hinzufügen"`.
- **JSON files** (`file_format: simple_json`, tag `override`) — flat maps keyed by the *English source string*, used to override framework/vendor English text (e.g. `"Confirm Password": "Passwort bestätigen"`).

## Referencing strings in code

### Blade

Use Laravel's `__()` helper with the dot-path key:

```blade
{{ __('t.global.add') }}
```

### Vue

The same `lang/` files are loaded into the Vue app at build time by [`vite-plugin-laravel-translations`](../../vitest.config.js) and exposed through `vue-i18n` (see [`resources/js/i18n.js`](../../resources/js/i18n.js), which uses a custom `laravelTranslationCompiler` so the Laravel `:placeholder` / `|` pluralization syntax works client-side). In components, use `$t()`:

```vue
{{ $t('t.global.add') }}
```

The locale is taken from `<html lang="…">` (defaulting to `de`), with `de` as the fallback locale — so a missing French key falls back to the German text rather than showing the raw key.

## Round-trip: adding or changing a string

1. **Add the German key** to the appropriate `lang/de/*.php` file (almost always [`lang/de/t.php`](../../lang/de/t.php)) — or `lang/de.json` for an English-source override — and reference it from Blade/Vue as above. German is the source of truth, but if you speak French fluently you may also fill the translation.
2. **Sync to Phrase.** On the Phrase side, a GitHub integration is set up to automatically read the strings and allow to translate them.
3. **Sync back to GitHub.** The GitHub integration also allows to add the translated strings back into the repository.

## Changelog convention (bilingual)

Per [AGENTS.md](../../AGENTS.md), every **user-facing** feature, change, or bugfix needs a changelog entry in **both** languages:

- [`CHANGELOG.md`](../../CHANGELOG.md) — German
- [`CHANGELOG_fr.md`](../../CHANGELOG_fr.md) — French

Entries are grouped by month heading (`##### Juli 2025`) and typically link the GitHub issue/PR, e.g.:

```markdown
##### Juli 2025
- In Rückmeldungen kann die Zuteilung … generiert werden … [#260](https://github.com/gloggi/qualix/issues/260)
```

The changelog French text is always written by hand in `CHANGELOG_fr.md` (it does not go through Phrase). Add both entries are commited together with the feature.

***

See also: [CI Pipeline](../Infrastructure/31 CI Pipeline.md) · [Frontend Architecture](../Architecture/13 Frontend Architecture.md) · [Contributing Guidelines](../../CONTRIBUTING.md)
