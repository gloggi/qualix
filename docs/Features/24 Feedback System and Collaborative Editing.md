# Feedback System & Collaborative Editing

[← Technical Documentation](../01 Technical Documentation TOC.md)

A feedback is the qualification write-up for one participant in one feedback round. Its body is a Tiptap rich-text document that can embed live references to the participant's [observations](../Features/21 Observations and Participant Tracking.md) and [requirements](../Features/23 Requirements and Qualifications.md), and it can be edited by several leaders simultaneously in real time.

Note: In practice, the individual feedback conversations with the individual participants in what we call a "feedback round" here can be hours or even days apart. Sometimes they are even spread out across the whole course, feedbacking each participant as soon as they have had the chance to demonstrate their abilities.

## Models

### `FeedbackData`
[`app/Models/FeedbackData.php`](../../app/Models/FeedbackData.php) — a **feedback round** shared across participants (`name`, `belongsTo Course`). One `FeedbackData` fans out to one `Feedback` per participant (`hasMany feedbacks`, `hasManyThrough participants` / `feedback_requirements`, all ordered by scout name).

### `Feedback`
[`app/Models/Feedback.php`](../../app/Models/Feedback.php) — one participant's feedback within a round.

- `belongsTo` `FeedbackData` and [`Participant`](../../app/Models/Participant.php).
- `belongsToMany` [`User`](../../app/Models/User.php) (`feedbacks_users`) — the leader(s) responsible (assigned manually or by the [allocation algorithm](../Features/25 Feedback Allocation Algorithm.md)).
- `requirements()` — `hasManyThrough` [`FeedbackRequirement`](../../app/Models/FeedbackRequirement.php) → [`Requirement`](../../app/Models/Requirement.php), joined with `requirement_statuses`, selecting the requirement plus its `order`, `status_id` and `comment` from the feedback-requirement row.
- `participant_observations()` — `belongsToMany` [`ParticipantObservation`](../../app/Models/ParticipantObservation.php) via `feedback_observations_participants`, `withPivot('order')`. These are the specific observations pinned into this feedback's body.
- `contentNodes()` — `hasMany` `FeedbackContentNode`, ordered by `order`.
- `collaborationKey` — a random 32-char string generated on create (`booted()` → `creating`), used to key the real-time collaboration session (see below).
- `name` is proxied from `FeedbackData`; `display_name` combines round name + participant name.

### Why content is stored as separate nodes: `FeedbackContentNode`
[`app/Models/FeedbackContentNode.php`](../../app/Models/FeedbackContentNode.php) holds a single Tiptap node as `json` plus an `order` (`belongsTo Feedback`).

The feedback body is **not** stored as one big HTML/JSON blob. A Tiptap document is decomposed into an ordered sequence of pieces of three kinds:

- **`requirement` nodes** → rows in `feedback_requirements` (a real FK relation to `Requirement` and `RequirementStatus`).
- **`observation` nodes** → attachments in `feedback_observations_participants` (a real FK relation to `ParticipantObservation`).
- **everything else** (paragraphs, text, formatting) → generic `FeedbackContentNode` rows storing the node's JSON.

Storing requirement and observation references as their own related rows lets the database enforce integrity (a feedback can only reference requirements/observations that actually exist and belong to the participant/course), and lets requirement statuses/comments be queried and printed without parsing HTML. The `order` column on each of the three tables reassembles the document.

See [Domain Model & Database Schema](../Architecture/12 Domain Model and Database Schema.md) for the graph.

## `TiptapFormatter` — the (de)serializer

[`app/Services/TiptapFormatter.php`](../../app/Services/TiptapFormatter.php) is the bridge between the Tiptap editor format and the three storage tables. It is instantiated per-feedback (`app()->makeWith(TiptapFormatter::class, ['feedback' => $this])`) and reached through accessors on `Feedback`:

- `$feedback->contents` (`getContentsAttribute`) → `TiptapFormatter::toTiptap()`: merges `requirements`, `participant_observations` and `contentNodes`, sorts by `order`, and wraps them in a `{ type: 'doc', content: [...] }` Tiptap document.
- `$feedback->contents = $tiptap` (`setContentsAttribute`) → `applyToFeedback()`: walks the document, splits nodes by `type` (`contentsToModels`), then **replaces** the feedback's `feedback_requirements`, `participant_observations` and `contentNodes` (delete + recreate). Runs inside the controller's DB transaction.

**Requirement drift protection.** Before applying, `checkRequirementsAreUpToDate` compares the requirement IDs in the submitted document against the requirements currently assigned to the feedback. If they differ (e.g. an admin added/removed a requirement while someone was editing), it throws `RequirementsMismatchException` carrying *corrected* contents (dropping stale requirements, appending missing ones to the document). `appendRequirementsToFeedback` is the entry point for adding requirements without a full re-check.

`isValid()` is a static structural validator used by the [`ValidFeedbackContent`](../../app/Services/Validation/ValidFeedbackContent.php) validation rule: it checks the doc shape and that every `observation`/`requirement` node references a known ID (and, for requirements, a valid `status_id` and string `comment`).

## Controller

### `FeedbackContentController`
[`app/Http/Controllers/FeedbackContentController.php`](../../app/Http/Controllers/FeedbackContentController.php) — the body editor.

- `edit` (`feedbackContent.edit`) — renders the editor with the participant's observations (ordered by block date), sends aggressive no-cache headers.
- `update` (`feedbackContent.update`) — assigns `$feedback->contents = $data` inside a `DB::transaction`; translates the three formatter exceptions (`RequirementsMismatchException`, `RequirementNotFoundException`, `ParticipantObservationNotFoundException`) into `ValidationException`s. On a requirements mismatch it rewrites the request's `feedback_contents` to the corrected version so the user sees the reconciled document.
- `print` (`feedbackContent.print`) — returns JSON (`$feedback->contents`, observations, statuses) for client-side print rendering.

Feedback *rounds* and *assignments* (not the body) are managed by [`FeedbackController`](../../app/Http/Controllers/FeedbackController.php) / [`FeedbackListController`](../../app/Http/Controllers/FeedbackListController.php) — see [Feedback Allocation Algorithm](../Features/25 Feedback Allocation Algorithm.md).

## Real-time collaborative editing

Multiple leaders can edit one feedback simultaneously. This is peer-to-peer (no server-side document state) using **Yjs** CRDTs over **y-webrtc**, wired in [`resources/js/components/feedback/FeedbackEditor.vue`](../../resources/js/components/feedback/FeedbackEditor.vue) via Tiptap's `@tiptap/extension-collaboration` and `@tiptap/extension-collaboration-cursor`:

```js
const ydoc = new Y.Doc()
const feedbackKey = 'qualix-feedback-' + courseId + '-' + collaborationKey.substr(0, 8)
const provider = new WebrtcProvider(feedbackKey, ydoc, {
  password: collaborationKey.substr(8),
  ...(signalingServers ? { signaling: signalingServers } : {}),
})
```

- The **room name** is derived from the course ID and the first 8 chars of the feedback's `collaborationKey`; the remaining 24 chars are the WebRTC **password** — so only clients that already know a feedback's secret key can join and decrypt its session.
- Signaling servers come from `window.Laravel.signalingServers`, populated from config (see below); if unset, y-webrtc's defaults are used.
- Collaboration is only enabled when both a `collaborationKey` is present **and** `window.crypto.subtle` is available (`collaborationSupported`). Otherwise the editor falls back to a plain Tiptap history extension (`withHistory`) — single-user editing still works.
- Changes are persisted to the backend separately via autosave (`feedbackContent.update`); Yjs only synchronizes the live editing session between peers.

### Configuration
Collaboration is toggled by `COLLABORATION_ENABLED` and the WebRTC signaling
server(s) by `COLLABORATION_SIGNALING_SERVERS`, both surfaced through
[`config/app.php`](../../config/app.php)'s `collaboration` key. See
[Environment Configuration](../Infrastructure/32 Environment Configuration.md)
for the values and defaults.

## Requirements Matrix (feedback progress overview)

The **Requirements Matrix** ("Anforderungs-Matrix") is a progress overview for one feedback round: a grid of **participants (rows) × minimum [requirements](../Features/23 Requirements and Qualifications.md) (columns)** where each cell shows a participant's fulfilment status and the internal comment for that requirement (which is used for internal notes, status information to share with the team, assigning responsible people etc., but never shown to the participants). It lets a leader see everyone's progress at a glance and edit each cell inline. (Not to be confused with the broader Observation/Qualification overview, `ObservationController::overview` / `overview.blade.php`, which is a separate page.)

- **Rows** — feedbacks (one per participant the user is responsible for), sorted by scout name; the header cell carries the participant avatar plus edit and [print](../Features/27 PDF Rendering.md) actions.
- **Columns** — the distinct requirements referenced across those feedbacks.
- **Cells** — status icon + truncated comment, background tinted by the [requirement status](../Features/23 Requirements and Qualifications.md) color. Clicking opens a modal to change the status, edit the comment (autosaved), and view any matching [evaluation grids](../Features/22 Evaluation Grids.md).

Components live under [`resources/js/components/feedback/requirements_matrix/`](../../resources/js/components/feedback/requirements_matrix/) (`RequirementsMatrix.vue` table container, `RequirementsMatrixHeaderRow.vue`, `RequirementsMatrixRow.vue`, `RequirementsMatrixCell.vue`). The key subtlety: a row does not read requirements from props directly — it builds a **Tiptap `Editor` from `feedback.contents`**, walks the document for `requirement` nodes, and writes edits back into those nodes. So the matrix reflects the requirements actually embedded in each feedback's body, and when collaboration is enabled it attaches the same WebRTC session as the editor (and works offline).

Served by [`FeedbackListController`](../../app/Http/Controllers/FeedbackListController.php): `progressOverview` (`feedback.progressOverview`) renders the `feedback.progress-overview` view (no-cache; `?view=` filters to one team member's participants; falls back to a plain participant list when a round has no requirements), and `updateRequirementStatus` (`feedback.updateRequirementStatus`) is the per-cell autosave endpoint that updates a single `FeedbackRequirement`.

## Frontend components

Under [`resources/js/components/feedback/`](../../resources/js/components/feedback/): `FeedbackEditor.vue` (the Tiptap + Yjs editor), `FormFeedbackContent.vue`, `InputFeedbackEditorLarge.vue`, `FormFeedbackData.vue`, and `tiptap-extensions/` (custom nodes, e.g. `observation/ElementObservation.vue`, `observation/ModalAddObservation.vue`, and requirement extensions). Print rendering lives in [`resources/js/components/print/feedback/`](../../resources/js/components/print/feedback/).
