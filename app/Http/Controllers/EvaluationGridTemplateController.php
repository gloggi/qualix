<?php

namespace App\Http\Controllers;

use App\Http\Requests\EvaluationGridTemplateRequest;
use App\Http\Requests\EvaluationGridTemplateUpdateRequest;
use App\Models\Course;
use App\Models\EvaluationGrid;
use App\Models\EvaluationGridRow;
use App\Models\EvaluationGridRowTemplate;
use App\Models\EvaluationGridTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class EvaluationGridTemplateController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        return view('admin.evaluation_grid_templates.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EvaluationGridTemplateRequest $request
     * @param Course $course
     * @return RedirectResponse
     */
    public function store(EvaluationGridTemplateRequest $request, Course $course) {
        $data = $request->validated();
        return DB::transaction(function () use ($request, $course, $data) {
            $gridTemplate = EvaluationGridTemplate::create(array_merge($data, ['course_id' => $course->id]));

            $this->createNewRowTemplates($data['row_templates'], $gridTemplate);

            $request->session()->flash('alert-success', __('t.views.admin.evaluation_grid_templates.create_success', ['name' => $gridTemplate->name]));
            return Redirect::route('admin.evaluation_grid_templates', ['course' => $course->id]);
        });
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param Course $course
     * @param EvaluationGridTemplate $evaluationGridTemplate
     * @return Response
     */
    public function edit(Request $request, Course $course, EvaluationGridTemplate $evaluationGridTemplate) {
        return view('admin.evaluation_grid_templates.edit', ['evaluation_grid_template' => $evaluationGridTemplate]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EvaluationGridTemplateUpdateRequest $request
     * @param Course $course
     * @param EvaluationGridTemplate $evaluationGridTemplate
     * @return RedirectResponse
     */
    public function update(EvaluationGridTemplateUpdateRequest $request, Course $course, EvaluationGridTemplate $evaluationGridTemplate) {
        $data = $request->validated();
        return DB::transaction(function() use($request, $course, $evaluationGridTemplate, $data) {
            $evaluationGridTemplate->update($data);

            /** @var Collection $currentRowTemplates */
            $currentRowTemplates = $evaluationGridTemplate->evaluation_grid_row_templates;
            $specifiedRowTemplates = collect($data['row_templates'])->whereNotNull('id')->unique('id');

            $newRowTemplates = collect($data['row_templates'])->whereNull('id');
            $deletableRowTemplates = $currentRowTemplates->whereNotIn('id', $specifiedRowTemplates->pluck('id'));
            $existingRowTemplates = $specifiedRowTemplates->whereIn('id', $currentRowTemplates->pluck('id'));

            $this->createNewRowTemplates($newRowTemplates, $evaluationGridTemplate);
            $this->deleteRowTemplates($deletableRowTemplates);
            $this->updateAndReorderRowTemplates($existingRowTemplates);

            $request->session()->flash('alert-success', __('t.views.admin.evaluation_grid_templates.edit_success', ['name' => $evaluationGridTemplate->name]));
            return Redirect::route('admin.evaluation_grid_templates', ['course' => $course->id]);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Course $course
     * @param EvaluationGridTemplate $evaluationGridTemplate
     * @return RedirectResponse
     */
    public function destroy(Request $request, Course $course, EvaluationGridTemplate $evaluationGridTemplate) {
        $evaluationGridTemplate->delete();
        $request->session()->flash('alert-success', __('t.views.admin.evaluation_grid_templates.delete_success', ['name' => $evaluationGridTemplate->name]));
        return Redirect::route('admin.evaluation_grid_templates', ['course' => $course->id]);
    }

    protected function createNewRowTemplates(Collection $data, EvaluationGridTemplate $evaluationGridTemplate) {
        $newRowTemplates = $evaluationGridTemplate->evaluationGridRowTemplates()->createMany(
            $data
                ->map(function ($rowTemplate, $index) use ($evaluationGridTemplate) {
                    return array_merge($rowTemplate, [
                        'evaluation_grid_template_id' => $evaluationGridTemplate->id,
                        'order' => $index,
                    ]);
                })
                ->all()
        );

        EvaluationGridRow::insert(
            collect($evaluationGridTemplate->evaluation_grids)
                ->crossJoin($newRowTemplates)
                ->map(function ($input) {
                    /** @var EvaluationGrid $evaluationGrid */
                    /** @var EvaluationGridRowTemplate $rowTemplate */
                    [$evaluationGrid, $rowTemplate] = $input;
                    return ['evaluation_grid_id' => $evaluationGrid->id, 'evaluation_grid_row_template' => $rowTemplate->id];
                })
                ->all()
        );
    }

    protected function deleteRowTemplates(Collection $rowTemplates) {
        EvaluationGridRowTemplate::whereIn('id', $rowTemplates->pluck('id'))->delete();
    }

    protected function updateAndReorderRowTemplates(Collection $rowTemplates, EvaluationGridTemplate $evaluationGridTemplate) {
        $rowTemplates->each(function ($rowTemplateData, $index) use ($evaluationGridTemplate) {
            $evaluationGridTemplate
                ->evaluationGridRowTemplates()
                ->where('id', $rowTemplateData['id'])
                ->update(array_merge($rowTemplateData, ['order' => $index]));

            // TODO update or clear any existing data in connected evaluation grid row instances, in case the control type or config has changed
        });
    }
}
