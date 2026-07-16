# Contributing 🎉

Thank you for wanting to help out! ❤️ Danke, dass du mithelfen möchtest!

Qualix is maintained by the Swiss Pfadi/Scouting community. This guide covers how to work with the code. For the technical deep-dive (architecture, domain model, testing, deployment), start at [docs/01 Technical Documentation TOC.md](./docs/01%20Technical%20Documentation%20TOC.md). For local setup and the mandatory working rules, see [README.md](./README.md) and [AGENTS.md](./AGENTS.md).

## Getting started

1. **Set up your environment.** Everything runs in Docker — `docker compose up`, then the app is at <http://localhost>. Full instructions are in [README.md](./README.md).
2. **Find something to work on.** Browse the [open issues](https://github.com/gloggi/qualix/issues). If you are new, look for smaller, well-defined issues first.
3. **Claim it.** Leave a comment on the issue (or open a draft pull request referencing it) so we can assign it to you — this avoids two people working on the same thing and lets us confirm the specification is still up to date.

If anything about the setup is unclear or you hit an error, open an issue or ask in the discussion — we're happy to help.

## Git workflow

We use a fork-and-pull-request workflow. Contributions are merged into [`master`](https://github.com/gloggi/qualix), which is the branch that gets deployed (see [Continuous Deployment](./docs/Infrastructure/33%20Continuous%20Deployment.md)).

1. Fork the repository to your GitHub account and clone it.
2. Create a feature branch off an up-to-date `master`:

   ```shell
   git fetch origin
   git checkout -b my-new-feature origin/master
   ```

3. Commit your work, push to your fork, and open a pull request against `gloggi/qualix`'s `master`. Reference the issue ID in the description.

Keep pull requests focused — one logical change per PR makes review faster.

## Before submitting a pull request

Please check the following. It keeps quality consistent and speeds up review. 🚀

- [ ] **Tests.** Add tests for new features, changes, and bugfixes. Unit tests are expected for most changes; E2E tests are reserved for the most essential user flows. See [Testing Strategy](./docs/Testing/51%20Testing%20Strategy.md) for how to run each suite:

  ```shell
  docker compose exec qualix vendor/bin/phpunit          # PHP (PHPUnit)
  docker compose exec vite npm run test                  # Frontend (Vitest)
  docker compose run e2e run                             # End-to-end (Playwright)
  ```

- [ ] **Changelog (both languages).** For every user-facing change, add an entry to [`CHANGELOG.md`](./CHANGELOG.md) in **German** and to [`CHANGELOG_fr.md`](./CHANGELOG_fr.md) in **French**. See [Translation Workflow](./docs/Internationalization/41%20Translation%20Workflow.md).
- [ ] **Code style.** Follow the existing code style and Laravel/Vue best practices. There is one hard rule: **always use the Vue Options API, never the Composition API** ([Frontend Architecture](./docs/Architecture/13%20Frontend%20Architecture.md)).
- [ ] **Language.** Use English for variable names, class names, functions, and comments. (User-facing strings are German first, translated via Phrase.)
- [ ] **No sensitive information.** Double-check that no passwords, credentials, keys, or local configuration are included in your changes.
- [ ] **Meaningful changes only.** Avoid unrelated whitespace or formatting churn in files you didn't otherwise touch.
- [ ] **CI is green.** Confirm the GitHub Actions build passes ([CI Pipeline](./docs/Infrastructure/31%20CI%20Pipeline.md)) — PHPUnit, Vitest, and Playwright all run there.

## Reporting bugs & requesting features

Open an [issue](https://github.com/gloggi/qualix/issues). For bugs, include steps to reproduce, what you expected, and what happened. For feature requests, describe the use case — what problem it solves for course leaders.
