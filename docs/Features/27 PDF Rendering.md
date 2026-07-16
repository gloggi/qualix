# PDF Rendering

[← Technical Documentation](../01 Technical Documentation TOC.md)

Qualix generates two kinds of printable PDF: **feedbacks** (a participant's qualification writeup, see [Feedback System](../Features/24 Feedback System and Collaborative Editing.md)) and **evaluation grids** (the filled-in or empty [rubrics](../Features/22 Evaluation Grids.md)). Both are rendered **entirely in the browser** — there is no server-side PDF engine, no headless Chrome, no wkhtmltopdf. This page explains why, and how the (non-obvious) client-side pipeline fits together.

## Why client-side

The app runs on traditional shared PHP hosting (see [Architecture Decisions](../Vision/62 Architecture Decisions.md)), where spawning a headless browser or a native PDF binary per request is not an option. So PDF layout is done in JavaScript with [`@react-pdf/renderer`](https://react-pdf.org/), which turns a React element tree into a real PDF `Blob` without any DOM or browser rendering. The server only ships JSON; the user's browser does the layout work and triggers a download.

The trade-offs: it is **React** (the only React in an otherwise [Vue Options-API](../Architecture/13 Frontend Architecture.md) codebase), and react-pdf supports only a small, flexbox-like subset of CSS. That subset — and its quirks — is why the print code looks unlike the rest of the frontend.

## The flow

Everything lives under [`resources/js/components/print/`](../../resources/js/components/print/).

1. A **print button** (Vue component) is clicked in a Blade/Vue page:
   - [`ButtonPrintFeedback.vue`](../../resources/js/components/print/ButtonPrintFeedback.vue) — one feedback.
   - [`ButtonPrintAllFeedbacks.vue`](../../resources/js/components/print/ButtonPrintAllFeedbacks.vue) — every feedback of a course, zipped.
   - [`ButtonPrintEvaluationGrid.vue`](../../resources/js/components/print/ButtonPrintEvaluationGrid.vue) — one grid.

2. The button **fetches the data as JSON** from a Laravel `print` route (see below) via `axios`, using `this.routeUri(...)`. A `401`/`404` reloads the page (session expired / resource gone).

3. The button **lazily imports** the React chain — `const renderPdf = (await import('./feedback/index.js')).default`. This keeps the ~react-pdf bundle out of the initial page load; it is only downloaded the first time someone actually prints.

4. [`feedback/index.js`](../../resources/js/components/print/feedback/index.js) (and [`evaluationGrid/index.js`](../../resources/js/components/print/evaluationGrid/index.js)) is the render entrypoint. It:
   - builds a translator with the current document language (see [i18n](#internationalization)),
   - `await`s `Document.prepare()` to register/load fonts **before** rendering (mandatory — see [Fonts](#fonts)),
   - calls `pdf(React.createElement(Document, { ...data, t })).toBlob()`,
   - returns `{ blob, error }` — errors are caught and returned, never thrown, so the button can show a friendly message.

5. The button turns the blob into an object URL and hands it to [`file-saver`](https://www.npmjs.com/package/file-saver)'s `saveAs` with a `sanitize-filename`-cleaned name. Object URLs are revoked on re-render and on `unmounted` to avoid leaks.

### Batch export

[`ButtonPrintAllFeedbacks.vue`](../../resources/js/components/print/ButtonPrintAllFeedbacks.vue) loops over all feedbacks, fetching and rendering each **sequentially** (not in parallel — memory), collecting the blobs into a [`JSZip`](https://stuk.github.io/jszip/) archive, and downloads a single `.zip`. It reports `completedPDFs / totalParticipants` progress in the button tooltip.

## Backend: the `print` routes

These are plain JSON endpoints, not PDF endpoints — the server does no rendering.

| Route name | Controller | Returns |
| --- | --- | --- |
| `feedbackContent.print` | [`FeedbackContentController::print`](../../app/Http/Controllers/FeedbackContentController.php) | course, participant, feedback, `feedbackContents`, observations (with block + participants, `withPivot('id')`), requirement statuses |
| `evaluationGrid.print` | [`EvaluationGridController`](../../app/Http/Controllers/EvaluationGridController.php) | course + template + the grid's rows/block/participants/author |
| `admin.evaluation_grid_templates.print` | [`EvaluationGridTemplateController`](../../app/Http/Controllers/EvaluationGridTemplateController.php) | course + template only (a blank grid) |

The endpoints are course-scoped like everything else (see [Application Architecture](../Architecture/11 Application Architecture Overview.md)); eager-loads on `observations` exist so the client has everything it needs for one PDF in a single request.

## The document components (`.jsx`)

The tree is built with react-pdf primitives (`Document`, `Page`, `View`, `Text`, `Image`). Key files for feedbacks:

- [`FeedbackDocument.jsx`](../../resources/js/components/print/feedback/FeedbackDocument.jsx) — the `<Document>`/`<Page size="A4">` root: header (participant image, names, course info), the requirement-progress bar, then the content. Also owns font registration (`FeedbackDocument.prepare`).
- [`FeedbackContents.jsx`](../../resources/js/components/print/feedback/FeedbackContents.jsx) — the Tiptap → react-pdf transform (see below).
- [`RequirementProgress.jsx`](../../resources/js/components/print/feedback/RequirementProgress.jsx) — the coloured stacked bar showing how many requirements sit at each status. Uses `flexBasis: count * 999999` as a cheap way to size segments proportionally.
- [`styles.js`](../../resources/js/components/print/feedback/styles.js) — the stylesheet.

Evaluation grids mirror this with [`EvaluationGridDocument.jsx`](../../resources/js/components/print/evaluationGrid/EvaluationGridDocument.jsx), `EvaluationGridRow.jsx`, `EvaluationGridRowControl.jsx`, `EvaluationGridCheckbox.jsx`.

### Layout gotchas worth knowing

- **Units are physical.** Sizes are written in `mm`/`pt` (e.g. `'22mm'`), because the output is a physical A4 page, not a screen.
- **Page numbering** uses a `fixed` `<Text>` with a `render={({ subPageNumber, subPageTotalPages }) => ...}` callback.
- **Page-break control:** `wrap={false}` keeps a block (e.g. an observation) from splitting across pages; `minPresenceAhead={30}` on headings/requirements avoids orphaning a heading at the very bottom of a page.
- **CSS is a subset.** Only flexbox-ish layout is supported — no grid, no floats, limited selectors. When something doesn't lay out as expected, that's usually why.

### Tiptap → react-pdf transform

Feedback content is stored as a structured [Tiptap](../Features/24 Feedback System and Collaborative Editing.md) document (`FeedbackContentNode`s), **not** HTML. [`FeedbackContents.jsx`](../../resources/js/components/print/feedback/FeedbackContents.jsx) walks that node tree recursively (`transformToReactPdf`) and maps each node type to a react-pdf element:

- `doc` / `paragraph` / `text` / `heading` → `View`/`Text` (only heading levels 3, 5, 6 render; empty paragraphs become a `' '` so they keep their height).
- `observation` → looks the observation up in the fetched list by `pivot.id`, renders its content + block name/date; unknown ids render nothing.
- `requirement` → looks up requirement + status, renders the status icon (coloured) and the requirement text.

Two text quirks are handled explicitly: `fixNewlines` normalises `\r\n`/`\r` to `\n` (stray `\r` renders as a crossed-out box in the PDF), and `ucfirst` capitalises requirement text.

## Fonts

Fonts are the most fiddly part. [`FeedbackDocument.jsx`](../../resources/js/components/print/feedback/FeedbackDocument.jsx)'s `registerFonts`:

1. Imports the `.ttf` files as **asset URLs** (`import SourceSansPro from '../../../../fonts/SourceSansPro-Regular.ttf'`). This works because `assetsInlineLimit: 0` in [`vite.config.js`](../../vite.config.js) forces Vite to emit fonts (and images) as real files rather than inlining them as data URIs — inlined data URIs would violate the [CSP](../Architecture/15 Security.md).
2. Registers **Source Sans Pro** (regular/bold/italic/bold-italic) and **Font Awesome Solid** via `Font.register`.
3. Registers a Twemoji **emoji source** (`Font.registerEmojiSource`) pointing at `/twemoji/assets/72x72/` (served from [`public/twemoji/`](../../public/twemoji/)), so emoji in feedback text render as images.
4. `await`s `Font.load(...)` for every face. This is why the entrypoint must `await Document.prepare()` **before** calling `pdf(...)` — react-pdf needs the font bytes in hand before layout, and missing this produces garbled or missing glyphs.

## Icons and colours

react-pdf can't read the app's SCSS, so two lookup tables are **manually mirrored** from the rest of the app:

- [`feedback/colors.js`](../../resources/js/components/print/feedback/colors.js) — copied from `_colors.scss`, mapping colour names to hex + a contrast colour.
- [`feedback/icons.js`](../../resources/js/components/print/feedback/icons.js) — maps Font Awesome icon names to their private-use codepoints (copied from the Font Awesome CSS), so a status icon can be rendered as a glyph in the Font Awesome font.

These are duplicated on purpose — keep them in sync when statuses gain new colours/icons.

## Internationalization

The PDF is not a Vue component, so it can't use the app's `$t`. [`resources/js/components/i18n.js`](../../resources/js/components/i18n.js) builds a standalone translator directly from `@intlify/core` using the same `VITE_LARAVEL_TRANSLATIONS` messages, with German fallback. The entrypoints pass the current `document.documentElement.lang` and hand the resulting `t` function to the document as a prop. See [Translation Workflow](../Internationalization/41 Translation Workflow.md).

## Build setup

No React plugin is configured in Vite — `.jsx` is transpiled by Vite's built-in esbuild, and the components use classic `React.createElement` (each file imports `React`). The only React-specific build requirement is that `react` is a dependency (`package.json`); react-pdf pulls in its own renderer. Because the whole chain is dynamically imported by the print buttons, it lands in a separate lazy chunk and never touches the main app bundle.

## Testing

PDF output is binary and layout-heavy, and there are currently **no automated tests** for the print components or the `print` routes — the whole PDF path (buttons, data endpoints, react-pdf rendering) is verified manually. This is a known gap; see [Testing Strategy](../Testing/51 Testing Strategy.md) for the suites that do exist.
