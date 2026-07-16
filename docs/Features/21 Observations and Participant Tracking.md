# Observations & Participant Tracking

[← Technical Documentation](../01 Technical Documentation TOC.md)

Observations are the core data unit of Qualix: a course leader records a short note about one or more participants during a specific [`Block`](../../app/Models/Block.php) (a lesson), optionally linking it to the [`Requirement`](../../app/Models/Requirement.php)s and `Category`s it touches. These observations later feed the qualification [feedbacks](../Features/24 Feedback System and Collaborative Editing.md).

## Models

### `Observation`
[`app/Models/Observation.php`](../../app/Models/Observation.php) — one recorded note.

- `belongsTo` [`Block`](../../app/Models/Block.php) — when/where it was made.
- `belongsTo` [`User`](../../app/Models/User.php) — who made it (`user_id`, set to the authenticated user on create).
- `belongsToMany` [`Participant`](../../app/Models/Participant.php) via the `observations_participants` pivot — an observation can cover several participants at once.
- `belongsToMany` [`Requirement`](../../app/Models/Requirement.php) via `observations_requirements` — which qualification criteria this note is evidence for.
- `belongsToMany` `Category` via `observations_categories` — free grouping tags.
- `impression` — an integer rating, one of `0`, `1`, `2` (negative / neutral / positive). Validated with `in:0,1,2`. This feature can be turned off per course in the course settings, in case one wants to enforce purely objective observations.
- `content` — the free-text note, capped at `Observation::CHAR_LIMIT` (1023 characters).

All five relations are eager-loaded on every query (`protected $with`).

### `ParticipantObservation`
[`app/Models/ParticipantObservation.php`](../../app/Models/ParticipantObservation.php) — an Eloquent model over the `observations_participants` join table (`observation_id` ↔ `participant_id`). It exists as a first-class model because a single participant-observation pair is what can be embedded into a participant's feedback (see [`Feedback::participant_observations()`](../../app/Models/Feedback.php) and [Feedback System](../Features/24 Feedback System and Collaborative Editing.md)) — the feedback references the observation *as seen from one participant*, not the shared `Observation` row.

### `ObservationAssignment`
[`app/Models/ObservationAssignment.php`](../../app/Models/ObservationAssignment.php) — a planning artifact, **not** an observation itself. It is a task telling certain leaders to observe certain participants during certain blocks. Three `belongsToMany` relations, each a plain pivot:

- `blocks` (`observation_assignment_blocks`)
- `participants` (`observation_assignment_participants`)
- `users` (`observation_assignment_users`)

Assignments are managed under the admin area (`admin.observationAssignments.*` routes) and drive the UI that prompts leaders on what still needs observing; they carry no rating or content.

See the full relation graph in [Domain Model & Database Schema](../Architecture/12 Domain Model and Database Schema.md).

## Controllers & routes

### `ObservationController`
[`app/Http/Controllers/ObservationController.php`](../../app/Http/Controllers/ObservationController.php). Course-scoped routes:

| Method | Route name | Purpose |
| --- | --- | --- |
| `GET` | `observation.new` | Create form |
| `POST` | `observation.store` | Create |
| `GET` | `observation.edit` | Edit form |
| `POST` | `observation.update` | Update |
| `DELETE` | `observation.delete` | Delete |
| `GET` | `overview` | Cross-participant overview table |

`store` and `update` both run in a `DB::transaction`. The participant/requirement/category IDs arrive as comma-separated strings in the form (`"3,7,9"`), are exploded and `array_filter`ed, then `attach`ed (create) or `sync`ed (update). On create, `course_id` and `user_id` (the current user) are merged in.

Validation lives in [`ObservationRequest`](../../app/Http/Requests/ObservationRequest.php):

```php
'participants'  => 'required|regex:/^\d+(,\d+)*$/|allExistInCourse',
'content'       => 'required|max:'.Observation::CHAR_LIMIT,
'impression'    => 'in:0,1,2',
'block'         => 'required|regex:/^\d+$/|existsInCourse',
'requirements'  => 'nullable|regex:/^\d+(,\d+)*$/|allExistInCourse',
'categories'    => 'nullable|regex:/^\d+(,\d+)*$/|allExistInCourse',
```

`allExistInCourse` / `existsInCourse` are the course-scoped [validation rules](../../app/Services/Validation/) that guarantee every submitted ID belongs to the current course.

**Return-to-view logic.** Creating an observation is possible from various different places in the app. After creating the observation, users should be redirected back to a sensible place, depending on where they started. `rememberPreviouslyActiveView` / `redirectToPreviouslyActiveView` / `extractPathParameter` cooperate to send the user back to the participant detail page they came from after editing, falling back to a viable participant or the observation form.

**Prioritized block selection.** Leaders usually record observations soon after a block or in the following evening or during the night. This is why the block dropdown in `create` is `prioritize`d so blocks from the last two days sort first and are easily selectable.

**Block-Requirement connection.** In the course admin UI, requirements can be connected to blocks and vice versa, implying that a certain requirement can probably be observed during these connected blocks. E.g. a requirement about hike planning is probably observable during the actual planning time. Later, when selecting the planning time block in the observation form, the connected requirements are automatically selected. The user always has the option to assign different or no requirements to the observation though.

**`overview`** renders a matrix (which leader made how many observations about which participant) and can overlay feedback progress and selected [evaluation grid](../Features/22 Evaluation Grids.md) templates, selected via the `evaluation_grid_templates` query parameter. The table visualizes with red color which trainer-participant pairings have too few observations, and in green which pairings have enough. The cutoff values for these colors can be defined per course in the course settings.

### `ObservationAssignmentController`
[`app/Http/Controllers/ObservationAssignmentController.php`](../../app/Http/Controllers/ObservationAssignmentController.php) — standard CRUD for assignments (`index`, `store`, `edit`, `update`, `destroy`), all admin-scoped. `update` uses `detach(null)` + `attach` (rather than `sync`) to rebuild each of the three pivots from the submitted comma-separated ID lists.

## Frontend

Vue components (auto-registered, Options API — see [Frontend Architecture](../Architecture/13 Frontend Architecture.md)):

- [`FormObservation.vue`](../../resources/js/components/FormObservation.vue) — the create/edit form.
- [`ObservationList.vue`](../../resources/js/components/ObservationList.vue), [`ParticipantDetailObservationList.vue`](../../resources/js/components/ParticipantDetailObservationList.vue), [`ObservationContent.vue`](../../resources/js/components/ObservationContent.vue) — display.
- [`TableObservationOverview.vue`](../../resources/js/components/TableObservationOverview.vue) — the `overview` matrix.
