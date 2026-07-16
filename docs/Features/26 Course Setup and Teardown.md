# Course Setup and Teardown

[← Technical Documentation](../01 Technical Documentation TOC.md)

This page covers the lifecycle chores around a [`Course`](../../app/Models/Course.php): filling it with blocks and participants at the start (by import or by generator), and ending it at the finish (archive or delete). See [Application Architecture](../Architecture/11 Application Architecture Overview.md) for course-scoping and the route middleware groups these features live in.

***

# Setup

## Importing external data

Qualix imports two kinds of external data so leaders don't retype it: the course's **block schedule** (from eCamp v3) and the **participant list** (from MiData, the Swiss scouting membership DB). All import services live under [`app/Services/Import/`](../../app/Services/Import/) and follow the same shape: a **Parser** turns a file into a `Collection` of plain arrays, and an **Importer** persists those arrays as models.

Each domain has an abstract importer holding a parser, plus a concrete subclass per source format. The parser is injected by [`ImportServiceProvider`](../../app/Providers/ImportServiceProvider.php) via contextual bindings, and the controller resolves the concrete importer at runtime from the form's `source` key. Spreadsheet files are read through [`SpreadsheetReaderFactory`](../../app/Services/Import/SpreadsheetReaderFactory.php) (a mockable wrapper over PhpSpreadsheet) in read-data-only mode.

### Block import

[`BlockListImporter::import`](../../app/Services/Import/Blocks/BlockListImporter.php) parses the file and **upserts** via `Block::updateOrCreate` keyed on (`course_id`, `day_number`, `block_number`), so re-importing updates existing blocks rather than duplicating them. Each parsed block carries `name`, `day_number`, `block_number`, `full_block_number`, `block_date`.

The active source is **eCamp v3**, whose only export is a **PDF** (no spreadsheet). [`ECamp3BlockOverviewParser`](../../app/Services/Import/Blocks/ECamp3/ECamp3BlockOverviewParser.php) parses it with `smalot/pdfparser`: it reads positioned text runs and keeps only font sizes `> 10` (font size 10 is checklist content whose stray numbers would break parsing), joins them into one blob, and extracts blocks with a single regex capturing day/block number, name, weekday, date and times. It handles German (`Mo`, `Di`, …) and English (`Mon`, `Tue`, …) weekday abbreviations and the English `MM/DD/YYYY` order. Routes `admin.block.uploadV3` → `admin.block.importV3` on [`BlockController`](../../app/Http/Controllers/BlockController.php), source key `eCamp3BlockOverview`, resolved by [`BlockImportRequest::getImporter`](../../app/Http/Requests/BlockImportRequest.php) from `ImportServiceProvider::$BLOCK_IMPORTER_MAP`; parsing exceptions are shown to the user.

> **Deprecated: eCamp2.** The legacy eCamp2 spreadsheet importer (source key `eCamp2BlockOverview`, routes `admin.block.upload`/`.import`) is no longer used — eCamp2 has been sunset — and its code may be removed soon. Do not extend it.

### Participant import

[`ParticipantListImporter::import`](../../app/Services/Import/Participants/ParticipantListImporter.php) **always creates** a new `Participant` per row (no upsert — re-importing intentionally adds duplicates). Each parsed row is a `scout_name` + `group`.

The one source is **MiData** (the Swiss scouting membership DB). [`MiDataParticipantListParser`](../../app/Services/Import/Participants/MiData/MiDataParticipantListParser.php) does **not** assume fixed columns: it scans the header row and locates the scout-name (Pfadiname), first-name, last-name and group (Hauptebene) columns by matching translated header labels, so column order and extra columns don't matter. `scout_name` is the Pfadiname, falling back to `firstName lastName`; `group` is the Hauptebene. Routes `admin.participants.upload` / `.import` on [`ParticipantController`](../../app/Http/Controllers/ParticipantController.php), source key `MiDataParticipantList`.

### Adding a new import source

1. Write a `*Parser implements BlockListParser|ParticipantListParser` producing the expected array shape.
2. Add an empty `*Importer extends BlockListImporter|ParticipantListImporter`.
3. Register the contextual parser binding and add the source key to the relevant map in [`ImportServiceProvider`](../../app/Providers/ImportServiceProvider.php).
4. Expose upload/import routes and a `source` option in the form.

Parser tests use fixture files and mock `SpreadsheetReaderFactory`; see the `tests/Feature` block/participant controllers and the parser unit tests ([Testing Strategy](../Testing/51 Testing Strategy.md)).

## Generating blocks

In addition to the schedule import or manual creation of blocks, a leader can bulk-create **one block per calendar day** across a date range — e.g. a recurring daily "Sonstiges" (miscellaneous) observation block for every day of a camp, as a solution for the need in some courses to create observations outside of blocks or which are not tied to a specific block. This is possible via a plain Blade form + controller (no Vue), reached from the block admin index.

Routes `admin.block.generate` / `admin.block.generate_store` → [`BlockController::generate()` / `generateStore()`](../../app/Http/Controllers/BlockController.php), validated by [`BlockGenerateRequest`](../../app/Http/Requests/BlockGenerateRequest.php) (`name` required `max:255`, `blocks_startdate`/`blocks_enddate` required dates, optional `requirements` multi-select via the `allExistInCourse` rule). `generateStore` iterates a `CarbonPeriod` over the inclusive range:

```php
foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
    $block = Block::create(array_merge($data, [
        'course_id' => $course->id,
        'block_date' => $date,
        'name' => $data['name'] . " - " . $date->format("d.m.y"),
    ]));
    $block->requirements()->sync(array_filter(explode(',', $data['requirements'])));
}
```

Each block gets `course_id`, its `block_date`, a date-suffixed `name` (e.g. `Sonstiges - 01.10.23`), and the same requirements. Only those fields are set — **`day_number`/`block_number` stay null** (they come from imports or manual editing), so generated blocks display purely by name, ordered by date. Notes:

- **Cap:** a range over **370 days** throws a `ValidationException` on `blocks_enddate` — the only bound on rows per submit.
- **Reversed range:** `CarbonPeriod` yields no dates, so zero blocks are created (the success message reflects this); `diffInDays` is absolute, so a reversed range doesn't trip the 370-day guard.
- The date suffix is appended *after* validation, so `max:255` only checks the base name — a name near 255 chars plus the suffix can slightly exceed the column.

Covered by [`tests/Feature/Admin/Block/GenerateBlockTest.php`](../../tests/Feature/Admin/Block/GenerateBlockTest.php).

## Generating participant groups

Leaders often split participants into groups (work groups, venture groups, …) and want people to meet *different* peers each time. The generator proposes such groupings — the classic [social golfer problem](https://en.wikipedia.org/wiki/Social_golfer_problem) — with the algorithm running **entirely client-side in a Web Worker** (adapted from the MIT-licensed [good-enough-golfers](https://github.com/islemaster/good-enough-golfers)).

### Domain model
[`ParticipantGroup`](../../app/Models/ParticipantGroup.php) — `belongsTo` `Course`, `belongsToMany` [`Participant`](../../app/Models/Participant.php) via `participant_groups_participants`. A group is just a `group_name` plus its membership; there is **no persisted concept of a "split"/"round"** — that's UI-only, and each generated group is saved as one independent row. Groups are personal data, purged on archive (below), since they may occasionally be named after users or participants.

### Frontend & algorithm
Components under [`resources/js/components/participantGroups/`](../../resources/js/components/participantGroups/): `ParticipantGroupGenerator.vue` (top-level form), `InputGroupSplits.vue` / `InputGroupSplit.vue` (a **split** = one independent grouping to generate, with a group count and constraints), `InputGeneratedParticipantGroups.vue` (editable result grid), plus `createWorker.js`, `index.worker.js` and `geneticGolferSolver.js`.

Constraints (per-split and/or global): forbid/discourage same **origin group** (`group`/Abteilung), forbidden/discouraged/encouraged **pairings**, prefer-large/prefer-small group, and discourage pairings from existing saved groups. (There is **no gender criterion** since Qualix does not know about the gender of participants; `scout_name` is display-only.)

`geneticGolferSolver.js` is a weighted heuristic/genetic optimiser (not exact, not pure random): constraints become a pairwise weight matrix (forbidden → `Infinity`, discouraged → `+1`, encouraged → `-1`, size prefs vs. virtual empty slots), groupings are scored by `Σ sign(w)·w²` over within-group pairs, and each split is optimised over generations of mutations. Crucially, **earlier splits feed forward into later splits' weights**, which is what minimises repeated pairings; an outer loop also tries different split *orderings*. It runs in a **Web Worker** because it's a CPU-heavy combinatorial search that would otherwise freeze the UI; it streams progress back to a progress bar.

> **Build caveat:** the worker is a *separately built* bundle ([`vite-workers.config.js`](../../vite-workers.config.js) → `public/build/workers/participantGroupGenerator.worker.js`, via `npm run worker:build`). A normal `vite build` does **not** rebuild it — after changing the solver or worker, re-run `npm run worker:build` (or just rely on the extra dev server which does it automatically via vite watch).

### Backend
[`ParticipantGroupController`](../../app/Http/Controllers/ParticipantGroupController.php): `generate` (route `admin.participantGroups.generate`) only renders the view — **the server generates nothing**. Results are *not* auto-saved; the user tweaks group names and submits to `storeMany` (`admin.participantGroups.storeMany`), which in a transaction creates one `ParticipantGroup` per group and `sync`s its participants from a comma-joined ID list, validated by `MultiParticipantGroupRequest` (reusing `allExistInCourse`). Single-group CRUD lives on the same controller.

Tests: [`tests/Vue/components/ParticipantGroupGenerator.spec.js`](../../tests/Vue/components/ParticipantGroupGenerator.spec.js) mocks the worker to run the real solver in-process (retrying up to 100× since it's randomised); `tests/Feature/Admin/ParticipantGroup/` covers `storeMany` + CRUD; `tests/e2e/tests/participant-group-generator.spec.js` is the E2E.

***

# Teardown: archiving vs deleting a course

A course can be ended two ways, both irreversible. The distinction exists for **data protection**: after a course, leaders no longer need participants' personal data, but often want to reuse the course structure (blocks, requirements, evaluation grid templates) later.

Archive state is a boolean `archived` column on `courses` (default `false`, no timestamp, no cast; [migration](../../database/migrations/2019_06_17_120000_add_archived_flag_to_course.php)). There are no model scopes — filtering is `User::nonArchivedCourses()` / `archivedCourses()`, and the header course switcher lists archived courses in a separate group.

**Archiving** — `CourseController::archive()`, route `admin.course.archive` (POST), inside the `courseNotArchived` group (can't re-archive). In one transaction it deletes all **personal** data and flips the flag:

```php
$course->participants()->delete();
$course->observations()->delete();
$course->observationAssignments()->delete();
$course->participantGroups()->delete();
$course->evaluationGrids()->delete();
$course->update(['archived' => true]);
```

Participant image files are deleted from `Storage` *after* the transaction commits, so a failed file deletion can't roll back the archiving. Blocks, requirements, statuses, categories, evaluation grid **templates** and the equipe are kept. There is **no unarchive route** — archiving is one-way.

**Deleting** — `CourseController::delete()`, route `admin.course.delete` (DELETE), *not* behind `courseNotArchived` (an archived course can still be deleted). It calls `$course->delete()`; all child data is removed by database-level `ON DELETE CASCADE` foreign keys (not model events), and participant images are cleaned up in PHP afterwards.

**The `courseNotArchived` middleware** — [`CourseMustNotBeArchived`](../../app/Http/Middleware/CourseMustNotBeArchived.php) (alias in [`bootstrap/app.php`](../../bootstrap/app.php)) flashes an error and redirects to `admin.course` when the bound `{course}` is archived. It guards every route touching live participant/observation data (participants, observations, assignments, participant groups, feedback content/feedbacks, evaluation grid instances). Routes left outside stay usable on an archived course: read-only views, course settings, equipe/invitations, course deletion, and full CRUD of the reusable template material (blocks, requirements, statuses, categories, evaluation grid templates). All archive gating in views is Blade-side via `$course->archived` (no Vue component references it).

| | Archive | Delete |
| --- | --- | --- |
| Route | `admin.course.archive` (POST) | `admin.course.delete` (DELETE) |
| Course row | kept, `archived = true` | removed |
| Deleted | participants, observations, assignments, participant groups, grid instances, images | everything (DB cascade) + images |
| Kept | blocks, requirements, categories, grid templates, equipe | nothing |
| Reversible | No | No |
| Allowed on archived course | No (can't re-archive) | Yes |
| Motivation | data protection: purge personal data, keep reusable material | full permanent removal |
