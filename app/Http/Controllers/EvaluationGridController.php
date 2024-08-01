<?php

namespace App\Http\Controllers;

use App\Http\Requests\EvaluationGridRequest;
use App\Models\Block;
use App\Models\Course;
use App\Models\EvaluationGrid;
use App\Models\EvaluationGridRow;
use App\Models\EvaluationGridTemplate;
use App\Util\HtmlString;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\RouteCollectionInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

class EvaluationGridController extends Controller {
    /**
     * Display a form to create a new resource.
     *
     * @param Request $request
     * @param Course $course
     * @param EvaluationGridTemplate $evaluationGridTemplate
     * @return Response
     */
    public function create(Request $request, Course $course, EvaluationGridTemplate $evaluationGridTemplate) {
        $this->rememberPreviouslyActiveView($request);
        return view('evaluationGrid.new', [
            'evaluationGridTemplate' => $evaluationGridTemplate,
            'evaluationGridRows' => $evaluationGridTemplate->evaluationGridRowTemplates
                ->map(function($rowTemplate) { return [ 'notes' => null, 'value' => null, 'evaluation_grid_row_template_id' => $rowTemplate->id ]; })
                ->keyBy('evaluation_grid_row_template_id'),
            'participants' => $request->input('participant'),
            'block' => $request->input('block'),
            'blocks' => $this->prioritize($evaluationGridTemplate->blocks, function(Block $block) { return $block->block_date->gt(Carbon::now()->subDays(2)); })
        ]);
    }

    private function prioritize(Collection $collection, callable $callable): Collection {
        $nonPrioritized = $collection->reject($callable);
        return $collection->filter($callable)->union($nonPrioritized)->values();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EvaluationGridRequest $request
     * @param Course $course
     * @return RedirectResponse
     */
    public function store(EvaluationGridRequest $request, Course $course, EvaluationGridTemplate $evaluationGridTemplate) {
        $data = $request->validated();
        DB::transaction(function() use ($request, $course, $evaluationGridTemplate, $data) {

            $evaluationGrid = EvaluationGrid::create(array_merge($data, ['user_id' => Auth::user()->getAuthIdentifier(), 'evaluation_grid_template_id' => $evaluationGridTemplate->id]));

            $participantIds = array_filter(explode(',', $data['participants']));
            $evaluationGrid->participants()->attach($participantIds);

            EvaluationGridRow::insert(
                collect($evaluationGrid->evaluationGridTemplate->evaluationGridRowTemplates)
                    ->filter(function ($rowTemplate) use($data) {
                        // Skip heading rows, and rows which have been added to the template in the meantime
                        return isset($data['rows'][$rowTemplate->id]);
                    })
                    ->map(function ($rowTemplate) use($data, $evaluationGrid) {
                        return array_merge(
                            // Some defaults
                            ['value' => null, 'notes' => null],
                            // The user input
                            $data['rows'][$rowTemplate->id],
                            // Fixed values which may not be changed
                            ['evaluation_grid_id' => $evaluationGrid->id, 'evaluation_grid_row_template_id' => $rowTemplate->id]
                        );
                    })
                    ->all()
            );

            $flash = (new HtmlString)->__('t.views.evaluation_grids.add_success');
            foreach ($participantIds as $participantId) {
                $participant = $evaluationGrid->participants()->firstWhere([ 'participants.id' => $participantId ]);
                $route = route('participants.detail', ['course' => $course->id, 'participant' => $participant->id]);
                $flash->s(" <a href=\"{$route}\"><i class=\"fas fa-arrow-right\"></i> ")
                      ->__('t.views.evaluation_grids.go_to_participant', ['name' => $participant->scout_name])
                      ->s('</a>');
            }

            $request->session()->flash('alert-success', $flash);
        });

        return $this->redirectToPreviouslyActiveView($request, $course, collect([]), route('evaluationGrid.new', ['course' => $course->id, 'evaluation_grid_template' => $evaluationGridTemplate->id, 'participants' => $data['participants'], 'block' => $data['block']]));
    }

    /**
     * Stores the participant which the user was just looking at into the session, for restoring the same view later.
     *
     * @param Request $request
     */
    protected function rememberPreviouslyActiveView(Request $request) {
        $previousFlash = $request->session()->get('participant_before_edit');
        $returnTo = $this->extractPathParameter(URL::previous(), 'participants.detail', 'participant', $previousFlash);
        $request->session()->flash('participant_before_edit', $request->session()->get('participant_before_edit', $returnTo));
    }

    /**
     * Parse a URL, interpret it as a route in our app and extract one of the parameters values.
     *
     * @param $url
     * @param $routeName
     * @param $parameterName
     * @param $fallback
     * @return string|object|null
     */
    protected function extractPathParameter($url, $routeName, $parameterName, $fallback) {
        /** @var RouteCollectionInterface $routes */
        $routes = Route::getRoutes();
        return $routes->getByName($routeName)->bind(new Request([], [], [], [], [], ['REQUEST_URI' => $url]))->parameter($parameterName, $fallback);
    }

    /**
     * Redirects the user back to the view that was remembered in the session previously. If the previously viewed
     * participant is not in the passed options, falls back to the first viable option.
     *
     * @param Request $request
     * @param Course $course
     * @param Collection $returnOptions a collection of participant ids that are legal to be viewed
     * @param string|null $fallback
     * @return RedirectResponse
     */
    protected function redirectToPreviouslyActiveView(Request $request, Course $course, Collection $returnOptions, $fallback = null) {
        $returnTo = $request->session()->get('participant_before_edit');
        if (!$returnOptions->contains($returnTo)) {
            $returnTo = $returnOptions->first();
        }

        if ($returnTo) return Redirect::to(route('participants.detail', ['course' => $course->id, 'participant' => $returnTo]));
        return Redirect::to($fallback ?? URL::previous());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param Course $course
     * @param EvaluationGrid $evaluationGrid
     * @return Response
     */
    public function edit(Request $request, Course $course, EvaluationGridTemplate $evaluationGridTemplate, EvaluationGrid $evaluationGrid) {
        $this->rememberPreviouslyActiveView($request);
        return view('evaluationGrid.edit', ['evaluationGridTemplate' => $evaluationGridTemplate, 'evaluationGrid' => $evaluationGrid]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EvaluationGridRequest $request
     * @param Course $course
     * @param EvaluationGrid $evaluationGrid
     * @return RedirectResponse
     */
    public function update(EvaluationGridRequest $request, Course $course, EvaluationGridTemplate $evaluationGridTemplate, EvaluationGrid $evaluationGrid) {
        DB::transaction(function () use ($request, $course, $evaluationGrid, $evaluationGridTemplate) {
            $data = $request->validated();
            $evaluationGrid->update($data);

            $evaluationGrid->participants()->sync(array_filter(explode(',', $data['participants'])));

            $evaluationGrid->rows()->each(function ($row) use($data, $evaluationGrid, $evaluationGridTemplate) {
                if (!isset($data['rows'][$row->evaluation_grid_row_template_id])) {
                    // Skip heading rows, and rows which have been added to the template in the meantime
                    return;
                }
                $row->update(array_merge(
                    $data['rows'][$row->evaluation_grid_row_template_id],
                    ['evaluation_grid_id' => $evaluationGrid->id, 'evaluation_grid_row_template_id' => $row->evaluation_grid_row_template_id]
                ));
            });
        });

        $request->session()->flash('alert-success', __('t.views.evaluation_grids.edit_success'));

        return $this->redirectToPreviouslyActiveView($request, $course, $evaluationGrid->participants()->pluck('participants.id'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Course $course
     * @param EvaluationGrid $evaluationGrid
     * @return RedirectResponse
     */
    public function destroy(Request $request, Course $course, EvaluationGridTemplate $evaluationGridTemplate, EvaluationGrid $evaluationGrid) {
        $participantId = $evaluationGrid->participants()->first()->id;
        $evaluationGrid->delete();
        $request->session()->flash('alert-success', __('t.views.participant_details.delete_evaluation_grid_success'));
        return Redirect::route('participants.detail', ['course' => $course->id, 'participant' => $participantId]);
    }

    /**
     * @param Request $request
     * @param Course $course
     * @param EvaluationGridTemplate $evaluationGridTemplate
     * @param EvaluationGrid $evaluationGrid
     * @return JsonResponse
     */
    public function print(Request $request, Course $course, EvaluationGridTemplate $evaluationGridTemplate, EvaluationGrid $evaluationGrid) {
        return response()->json([
            'course' => $course,
            'evaluationGridTemplate' => $evaluationGridTemplate,
            'evaluationGrid' => EvaluationGrid::with(
                'rows',
                'block',
                'participants',
                'user',
            )->find($evaluationGrid->id),
        ]);
    }
}
