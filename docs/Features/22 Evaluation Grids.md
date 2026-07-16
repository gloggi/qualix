# Evaluation Grids

[← Technical Documentation](../01 Technical Documentation TOC.md)

Evaluation grids are structured, printable assessment rubrics. A leader defines a **template** (a list of criteria with control types), then fills in one **grid instance** per participant/block, and can print it. They complement free-text [observations](../Features/21 Observations and Participant Tracking.md) with a fixed-form rubric.

## Models

The grid domain is a template/instance pair, each with its own row model.

### `EvaluationGridTemplate`
[`app/Models/EvaluationGridTemplate.php`](../../app/Models/EvaluationGridTemplate.php) — the rubric definition, `belongsTo` [`Course`](../../app/Models/Course.php).

- `hasMany` `EvaluationGridRowTemplate` (`evaluationGridRowTemplates`), ordered by `order` — the rows/criteria.
- `hasMany` `EvaluationGrid` (`evaluationGrids`) — the filled instances.
- `belongsToMany` [`Requirement`](../../app/Models/Requirement.php) (`evaluation_grid_templates_requirements`) — which qualification criteria this grid contributes to.
- `belongsToMany` [`Block`](../../app/Models/Block.php) (`evaluation_grid_templates_blocks`) — the blocks this grid may be used in (ordered chronologically). Grid instances can only be created for these blocks.

Row templates, requirements and blocks are eager-loaded (`$with`).

### `EvaluationGridRowTemplate`
[`app/Models/EvaluationGridRowTemplate.php`](../../app/Models/EvaluationGridRowTemplate.php) — one criterion in a template.

- `order` — position in the grid.
- `criterion` — the label/text.
- `control_type` — one of `EvaluationGridRowTemplate::CONTROL_TYPES`: `slider`, `radiobuttons`, `checkbox`, `heading`, `notes_only`. `heading` and `notes_only` rows carry no rating input (see below).

### `EvaluationGrid`
[`app/Models/EvaluationGrid.php`](../../app/Models/EvaluationGrid.php) — one filled-in instance.

- `belongsTo` `EvaluationGridTemplate`, [`Block`](../../app/Models/Block.php), [`User`](../../app/Models/User.php) (the author).
- `belongsToMany` [`Participant`](../../app/Models/Participant.php) (`evaluation_grids_participants`) — one grid can cover several participants.
- `hasMany` `EvaluationGridRow` (`rows`), joined against the row templates and ordered by the template's `order` so a grid's rows always render in template order.
- `name` is proxied from the template (`getNameAttribute` / `setNameAttribute`); `display_name` combines grid name, participant name and block name.

### `EvaluationGridRow`
[`app/Models/EvaluationGridRow.php`](../../app/Models/EvaluationGridRow.php) — one filled cell.

- `belongsTo` `EvaluationGrid` and `EvaluationGridRowTemplate`.
- `value` — the control's value (e.g. slider/radio selection).
- `notes` — free text for that row.

There is exactly one `EvaluationGridRow` per (grid, non-heading row template) pair. `heading` rows have no corresponding row instance.

See [Domain Model & Database Schema](../Architecture/12 Domain Model and Database Schema.md) for the full graph.

## Controllers & routes

### `EvaluationGridTemplateController`
[`app/Http/Controllers/EvaluationGridTemplateController.php`](../../app/Http/Controllers/EvaluationGridTemplateController.php) — admin CRUD for templates plus `print`.

The interesting logic is keeping filled grids consistent when a template changes. `store`/`update` `sync` the block and requirement pivots and then reconcile row templates via `normalizeOrder` (re-number `order` to 1..n) and three helpers:

- `createNewRowTemplates` — inserts the new row templates, then **cross-joins** them with every existing grid instance and bulk-inserts blank `EvaluationGridRow`s so existing grids gain the new rows.
- `deleteRowTemplates` — deletes removed row templates (cascading their rows).
- `updateRowTemplates` — updates criterion/control_type/order on kept rows. Note the `TODO` in the code: changing a row's control type does not yet clear now-invalid data in existing grid instances.

Because editing a template can touch existing filled grids, `edit` flashes an `alert-warning` when `evaluationGrids()->count()` is non-zero.

### `EvaluationGridController`
[`app/Http/Controllers/EvaluationGridController.php`](../../app/Http/Controllers/EvaluationGridController.php) — CRUD for grid instances (nested under a template) plus `print`.

| Method | Route name |
| --- | --- |
| `GET` | `evaluationGrid.new` |
| `POST` | `evaluationGrid.store` |
| `GET` | `evaluationGrid.edit` |
| `POST` | `evaluationGrid.update` |
| `DELETE` | `evaluationGrid.delete` |
| `GET` | `evaluationGrid.print` |

`store` creates the grid (setting `user_id` and `evaluation_grid_template_id`), attaches participants, and bulk-`insert`s one `EvaluationGridRow` per submitted `rows[<rowTemplateId>]` entry — rows without submitted data (headings, or row templates added to the template after this form was rendered) are skipped. `update` mirrors this, iterating existing rows and skipping the same cases. Both share the same participant/return-to-view redirect logic as [`ObservationController`](../Features/21 Observations and Participant Tracking.md) (`rememberPreviouslyActiveView` / `redirectToPreviouslyActiveView`), and the block dropdown is likewise `prioritize`d toward recent blocks.

`print` on both controllers returns JSON (course + template, and for a grid also its `rows`/`block`/`participants`/`user`), consumed by the client-side print rendering. There is also a template-level `admin.evaluation_grid_templates.print` route for printing a blank grid which can be taken to a block and filled in live with a pen instead of with an electronic device.

## Frontend

Vue components under [`resources/js/components/evaluationGrid/`](../../resources/js/components/evaluationGrid/): `FormEvaluationGridTemplate.vue`, `InputEvaluationGridTemplate.vue`, `InputEvaluationGrid.vue`, `InputEvaluationGridRow.vue`, `InputEvaluationGridRowControl.vue` (renders the control per `control_type`), `InputEvaluationGridRowNotes.vue`, `InputEvaluationGridRowTemplate.vue`. Plus [`ButtonNewEvaluationGrid.vue`](../../resources/js/components/ButtonNewEvaluationGrid.vue) and print buttons under [`print/`](../../resources/js/components/print/).
