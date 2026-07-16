# Requirements & Qualifications

[← Technical Documentation](../01 Technical Documentation TOC.md)

A **requirement** is a qualification criterion a participant must meet in a course (e.g. a J+S competency). Requirements are the connective tissue of the domain: [observations](../Features/21 Observations and Participant Tracking.md) and [evaluation grids](../Features/22 Evaluation Grids.md) which are evidence for passing or failing a requirement reference them, and [feedbacks](../Features/24 Feedback System and Collaborative Editing.md) assign each participant a **status** per requirement. Requirements are defined per course.

## Models

### `Requirement`
[`app/Models/Requirement.php`](../../app/Models/Requirement.php) — `belongsTo` [`Course`](../../app/Models/Course.php).

- `content` — the criterion text.
- `mandatory` (bool cast) — whether it must be fulfilled in order to pass the course and get the qualification(s).
- `belongsToMany` [`Observation`](../../app/Models/Observation.php) via `observations_requirements` — observations recorded as evidence.
- `belongsToMany` [`Block`](../../app/Models/Block.php) via `blocks_requirements` — blocks where this requirement is expected to be observable.
- `hasMany` `RequirementDetail` — sub-points, currently unused (see below).
- `hasMany` [`FeedbackRequirement`](../../app/Models/FeedbackRequirement.php) — the per-feedback status entries.
- `num_observations` (accessor) — count of linked observations, used in the admin overview.
- `num_feedback_datas` (accessor) — how many feedback rounds reference this requirement.

### `RequirementDetail`
[`app/Models/RequirementDetail.php`](../../app/Models/RequirementDetail.php) — a sub-item of a requirement (`belongsTo Requirement`), with its own `content` and `mandatory` flag. Lets a requirement be broken into finer bullet points. There is currently no UI for creating this model, so it is not used at the moment.

### `RequirementStatus`
[`app/Models/RequirementStatus.php`](../../app/Models/RequirementStatus.php) — a course-defined **status label** a requirement can be given for a participant (e.g. "fulfilled", "in progress", "not yet"). It is not tied to a specific requirement; it is a reusable, styled label.

- `name`, `color`, `icon`. Allowed values are constrained by the `RequirementStatus::COLORS` and `RequirementStatus::ICONS` allow-lists (Bootstrap-style color tokens and Font Awesome icon names).
- `hasMany` [`FeedbackRequirement`](../../app/Models/FeedbackRequirement.php); `num_feedback_requirements` accessor counts usages.
- A course has a `default_requirement_status_id` (referenced by [`TiptapFormatter::getDefaultRequirementStatusId`](../../app/Services/TiptapFormatter.php)) — the status newly-added requirements start in inside a feedback.

### Where the participant's actual qualification lives: `FeedbackRequirement`
The status of a requirement *for a given participant* is not stored on `Requirement` or `RequirementStatus` — it lives on [`FeedbackRequirement`](../../app/Models/FeedbackRequirement.php), the join between a [`Feedback`](../../app/Models/Feedback.php) (which belongs to one participant), a `Requirement`, and a `RequirementStatus`, plus an `order` and free-text `comment`. So "participant X's qualification" is the set of `FeedbackRequirement`s across their feedbacks. This is detailed in the [Feedback System](../Features/24 Feedback System and Collaborative Editing.md) doc.

See [Domain Model & Database Schema](../Architecture/12 Domain Model and Database Schema.md) for the full graph.

## Constraints & product rationale

These limits are **product/pedagogical decisions**, not incidental technical ones. They are enforced in code but the *reasoning* comes from the J+S "Rückmelden, Qualifizieren und Fördern" (RQF) doctrine and is not otherwise discoverable from the source — recorded here so it isn't lost.

- **Max 40 requirements per feedback.** Enforced by `maxEntries:40` on the `requirements` field in [`FeedbackUpdateRequest`](../../app/Http/Requests/FeedbackUpdateRequest.php) and [`EvaluationGridTemplateRequest`](../../app/Http/Requests/EvaluationGridTemplateRequest.php) (rule: [`MaxEntries`](../../app/Services/Validation/MaxEntries.php)). The trigger was that the progress-overview matrix stops being technically and visually usable beyond that. Pedagogically, Qualix's feedback concept is not designed to carry many embedded requirements — clarity, the developmental ("Förder-") intent, verifiability, second chances, and re-training all degrade as the count grows.
- **≤ 10 mandatory requirements recommended per course.** Not hard-enforced, but the UI and features are optimised around this assumption. Rationale (RQF brochure): every mandatory requirement ("Mindestanforderung") must have a matching observation moment, participants need room to practise and make mistakes before being assessed, all requirements' content must actually be taught, and each must be met individually (no compensation across requirements). Ideally, for each mandatory requirement, there are also slots for repeated learning and second chances in case some participants don't meet the requirements the first time around. So each mandatory requirement one adds significant time load for both the course team and participants.
- **New feedbacks pre-select only requirements explicitly marked `mandatory`.** Other requirements remain selectable, but are not defaulted, to steer toward the small-set best practice. The `mandatory` checkbox itself defaults to checked on new requirements to encourage hard, verifiable criteria.

Sources: [CHANGELOG.md](../../CHANGELOG.md) (January 2024 entry) and the RQF brochure ["Rückmelden, Qualifizieren und Fördern im Ausbildungskurs"](https://issuu.com/pbs-msds-mss/docs/3118.01de-rqf-20160831-akom), pp. 14–15, 31, 35.

## Controllers & routes

### `RequirementController`
[`app/Http/Controllers/RequirementController.php`](../../app/Http/Controllers/RequirementController.php) — admin CRUD for requirements. Routes: `admin.requirements` (index), `.store`, `.edit`, `.update`, `.delete`.

### `RequirementStatusController`
[`app/Http/Controllers/RequirementStatusController.php`](../../app/Http/Controllers/RequirementStatusController.php) — admin CRUD for status labels. Routes: `admin.requirement_statuses` (index), `.store`, `.edit`, `.update`, `.delete`.

Both controllers sit behind the admin/`courseNotArchived` middleware (see [Application Architecture Overview](../Architecture/11 Application Architecture Overview.md)).

## Tracking flow

1. Admin defines `Requirement`s and a set of `RequirementStatus` labels for the course.
2. During the course, leaders link [observations](../Features/21 Observations and Participant Tracking.md) and [evaluation grids](../Features/22 Evaluation Grids.md) to the relevant requirements as evidence.
3. When writing a participant's [feedback](../Features/24 Feedback System and Collaborative Editing.md), each requirement embedded in the feedback becomes a `FeedbackRequirement` carrying the chosen `RequirementStatus` and a comment (internal note) — this is the recorded qualification decision. The `feedback.progressOverview` / `feedback.updateRequirementStatus` routes drive a quick status-editing overview across participants.
