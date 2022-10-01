@extends('layouts.default')

@section('pagetitle'){{__('t.views.page_titles.admin_categories') }}@endsection

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.categories.new')}}</template>

        <form-basic :action="['admin.categories.store', { course: {{ $course->id }} }]">

            <input-text name="name" label="{{__('t.models.category.name')}}" required autofocus></input-text>

            <button-submit label="{{__('t.global.add')}}">

                @component('components.help-text', ['id' => 'categoryHelp', 'key' => 't.views.admin.categories.what_are_categories'])@endcomponent

            </button-submit>

        </form-basic>

    </b-card>

    <b-card>
        <template #header>{{__('t.views.admin.categories.existing', ['courseName' => $course->name])}}</template>

        @if (count($course->categories))

            <responsive-table
                :data="{{ json_encode($course->categories->map->append('num_observations')) }}"
                :fields="[
                    { label: $t('t.models.category.name'), value: category => category.name },
                    @if(!$course->archived){ label: $t('t.models.category.num_observations'), value: category => category.num_observations },@endif
                ]"
                :actions="{
                    edit: category => routeUri('admin.categories.edit', {course: {{ $course->id }}, category: category.id}),
                    delete: category => ({
                        text: $t('t.views.admin.categories.really_delete', category) @if(!$course->archived) + ' ' + $tc('t.views.admin.categories.observations_on_category', category.num_observations)@endif,
                        route: ['admin.categories.delete', {course: {{ $course->id }}, category: category.id}]
                    })
                }"></responsive-table>

        @else

            {{__('t.views.admin.categories.no_categories')}}

            @component('components.help-text', ['id' => 'noCategoriesHelp', 'key' => 't.views.admin.categories.are_categories_required'])@endcomponent

        @endif

    </b-card>

@endsection
